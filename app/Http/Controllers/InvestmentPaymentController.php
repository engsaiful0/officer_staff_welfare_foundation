<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentInstallment;
use App\Models\LedgerEntry;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InvestmentPaymentController extends Controller
{
    /**
     * Payment Investment - Show all investments with pending installments
     */
    public function paymentInvestment(Request $request)
    {
        // Get investments with pending installments
        $query = Investment::with(['member', 'account', 'installments' => function($q) {
            $q->where('status', 'pending')->orderBy('installment_number');
        }])
        ->whereHas('installments', function($q) {
            $q->where('status', 'pending');
        });

        // Filter by member
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $investments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get pending installments count for each investment
        foreach ($investments as $investment) {
            $investment->pending_count = $investment->installments->where('status', 'pending')->count();
            $investment->overdue_count = $investment->installments->where('status', 'pending')
                ->filter(function($installment) {
                    return $installment->isOverdue();
                })->count();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $investments
            ]);
        }

        $members = \App\Models\Member::select('id', 'name', 'unique_id')->get();

        return view('content.investments.payments.payment-investment', compact('investments', 'members'));
    }

    /**
     * Pay Investment - Show pending installments or redirect to payment form
     */
    public function payInvestment(Request $request, Investment $investment)
    {
        $investment->load(['member', 'account', 'installments']);
        
        // Get pending installments
        $pendingInstallments = $investment->installments()
            ->where('status', 'pending')
            ->orderBy('installment_number')
            ->get();
        
        // If no pending installments
        if ($pendingInstallments->isEmpty()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending installments found for this investment.'
                ], 404);
            }
            
            return redirect()->route('investments.payments.index', $investment)
                ->with('info', 'No pending installments found. All installments have been paid.');
        }
        
        // If only one pending installment, redirect directly to payment form
        if ($pendingInstallments->count() === 1) {
            $installment = $pendingInstallments->first();
            return redirect()->route('investments.payments.show', [$investment, $installment->id]);
        }
        
        // If multiple pending installments, show selection page
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'investment' => $investment,
                    'pending_installments' => $pendingInstallments
                ]
            ]);
        }
        
        return view('content.investments.payments.select-installment', compact('investment', 'pendingInstallments'));
    }

    /**
     * Display payment list for an investment
     */
    public function index(Request $request, Investment $investment)
    {
        $investment->load(['member', 'installments']);
        
        $query = $investment->installments()->orderBy('installment_number');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $installments = $query->get();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $installments
            ]);
        }

        return view('content.investments.payments.index', compact('investment', 'installments'));
    }

    /**
     * Show payment form for a specific installment
     */
    public function show(Request $request, Investment $investment, $installmentId)
    {
        $installment = InvestmentInstallment::findOrFail($installmentId);
        
        // Verify installment belongs to investment
        if ($installment->investment_id != $investment->id) {
            abort(404);
        }

        $installment->load('investment.member');

        // Calculate fine if payment is late
        $fine = $installment->calculateFine();
        $daysLate = $installment->getDaysLate();

        // Get payment methods for dropdown
        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'installment' => $installment,
                    'fine' => $fine,
                    'days_late' => $daysLate,
                    'total_with_fine' => $installment->total_amount + $fine,
                    'payment_methods' => $paymentMethods
                ]
            ]);
        }

        return view('content.investments.payments.show', compact('investment', 'installment', 'fine', 'daysLate', 'paymentMethods'));
    }

    /**
     * Process payment for an installment
     */
    public function store(Request $request, Investment $investment, $installmentId)
    {
        $installment = InvestmentInstallment::findOrFail($installmentId);
        
        // Verify installment belongs to investment
        if ($installment->investment_id != $investment->id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Installment does not belong to this investment'
                ], 422);
            }
            abort(404);
        }

        // Check if already paid
        if ($installment->status === 'paid') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This installment has already been paid'
                ], 422);
            }
            return redirect()->back()->with('error', 'This installment has already been paid.');
        }

        $validator = Validator::make($request->all(), [
            'paid_date' => 'required|date',
            'paid_amount' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'transaction_reference' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'check_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $paidDate = Carbon::parse($request->paid_date);
            
            // Calculate fine based on paid date
            $fine = $installment->calculateFine($paidDate);
            $daysLate = $installment->getDaysLate($paidDate);
            
            // Calculate base total (principal + rent + fine)
            $baseTotal = (float)$installment->principal_amount + (float)$installment->rent + (float)$fine;
            
            // Get discount amount (default to 0 if not provided)
            $discountAmount = (float)($request->discount_amount ?? 0);
            
            // Calculate net paid amount (base total - discount)
            $netPaidAmount = $baseTotal - $discountAmount;
            
            // Ensure net paid amount is not negative
            if ($netPaidAmount < 0) {
                $netPaidAmount = 0;
            }
            
            // Generate receipt number
            $receiptNumber = $this->generateReceiptNumber();
            
            // Update installment
            $installment->update([
                'status' => 'paid',
                'paid_date' => $paidDate,
                'fine_amount' => $fine,
                'discount_amount' => $discountAmount,
                'total_amount' => $baseTotal,
                'paid_by' => auth()->id(),
                'payment_method_id' => $request->payment_method_id,
                'transaction_reference' => $request->transaction_reference,
                'receipt_number' => $receiptNumber,
                'bank_name' => $request->bank_name,
                'check_number' => $request->check_number,
                'notes' => $request->notes
            ]);

            // Create ledger entry for payment
            $investmentId = $investment->getKey();
            $lastEntry = LedgerEntry::where('entity_type', 'investment')
                ->where('entity_id', $investmentId)
                ->orderBy('entry_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            $previousBalance = $lastEntry ? (float)$lastEntry->balance_after : (float)$investment->principal_amount;
            $newBalance = $previousBalance - (float)$installment->principal_amount;

            // Use net paid amount (base total - discount) for ledger entry
            $ledgerAmount = $netPaidAmount;
            
            LedgerEntry::create([
                'entity_type' => 'investment',
                'entity_id' => $investmentId,
                'entry_date' => $paidDate,
                'type' => 'payment',
                'amount' => (float)$ledgerAmount,
                'principal_amount' => (float)$installment->principal_amount,
                'interest_amount' => (float)$installment->rent,
                'balance_after' => (float)$newBalance,
                'description' => "Payment for installment #{$installment->installment_number}" . 
                    ($fine > 0 ? " (Fine: $" . number_format($fine, 2) . " for {$daysLate} days late)" : "") .
                    ($discountAmount > 0 ? " (Discount: $" . number_format($discountAmount, 2) . ", Net: $" . number_format($netPaidAmount, 2) . ")" : ""),
                'created_by' => auth()->id()
            ]);

            // Update investment account if exists
            if ($investment->account) {
                $account = $investment->account;
                $account->total_principal_paid = (float)$account->total_principal_paid + (float)$installment->principal_amount;
                $account->total_rent_received = (float)$account->total_rent_received + (float)$installment->rent;
                $account->total_payments_made = (float)$account->total_payments_made + (float)$ledgerAmount; // Use net paid amount
                $account->total_installments_paid = (int)$account->total_installments_paid + 1;
                $account->installments_paid_count = (int)$account->installments_paid_count + 1;
                $account->installments_pending_count = max(0, (int)$account->installments_pending_count - 1);
                $account->current_balance = (float)$newBalance;
                $account->save();
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'data' => [
                        'installment' => $installment->fresh()->load('paymentMethod'),
                        'fine' => $fine,
                        'days_late' => $daysLate,
                        'receipt_number' => $receiptNumber
                    ]
                ]);
            }

            return redirect()->route('investments.payments.index', $investment)
                ->with('success', 'Payment processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process payment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to process payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Calculate fine for an installment (AJAX endpoint)
     */
    public function calculateFine(Request $request, Investment $investment, $installmentId)
    {
        $installment = InvestmentInstallment::findOrFail($installmentId);
        
        // Verify installment belongs to investment
        if ($installment->investment_id != $investment->id) {
            return response()->json([
                'success' => false,
                'message' => 'Installment does not belong to this investment'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'paid_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $paidDate = Carbon::parse($request->paid_date);
        $fine = $installment->calculateFine($paidDate);
        $daysLate = $installment->getDaysLate($paidDate);
        $baseAmount = (float)$installment->principal_amount + (float)$installment->rent + (float)$fine;

        return response()->json([
            'success' => true,
            'data' => [
                'fine' => $fine,
                'days_late' => $daysLate,
                'base_amount' => $baseAmount,
                'total_with_fine' => $baseAmount,
                'principal_amount' => $installment->principal_amount,
                'rent' => $installment->rent,
                'original_total' => $installment->total_amount
            ]
        ]);
    }

    /**
     * Generate unique receipt number
     * Format: RCP-YYYYMMDD-XXXXXX
     */
    private function generateReceiptNumber()
    {
        $prefix = 'RCP-' . date('Ymd') . '-';
        $lastReceipt = InvestmentInstallment::where('receipt_number', 'like', $prefix . '%')
            ->orderBy('receipt_number', 'desc')
            ->first();

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt_number, -6);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $receiptNumber = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (InvestmentInstallment::where('receipt_number', $receiptNumber)->exists()) {
            $newNumber++;
            $receiptNumber = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        }

        return $receiptNumber;
    }
}

