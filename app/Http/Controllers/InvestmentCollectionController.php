<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentAccount;
use App\Models\InvestmentInstallment;
use App\Models\LedgerEntry;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InvestmentCollectionController extends Controller
{
    /**
     * Show investment collection form
     */
    public function index(Request $request)
    {
        // Get all active investment accounts with their investments and members
        $accounts = InvestmentAccount::with(['investment.member'])
            ->where('account_status', 'active')
            ->orderBy('account_number')
            ->get();

        // Get payment methods
        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        return view('content.investments.collection.index', compact('accounts', 'paymentMethods'));
    }

    /**
     * Get pending installments for a selected investment account (AJAX)
     */
    public function getInstallments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:investment_accounts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $account = InvestmentAccount::with(['investment.member', 'investment.installments' => function($q) {
            $q->where('status', 'pending')->orderBy('installment_number');
        }])->findOrFail($request->account_id);

        $installments = $account->investment->installments->map(function($installment) {
            $daysLate = $installment->getDaysLate();
            $fine = $installment->calculateFine();
            $totalAmount = $installment->principal_amount + $installment->rent + $fine;

            return [
                'id' => $installment->id,
                'installment_number' => $installment->installment_number,
                'schedule_date' => $installment->schedule_date->format('Y-m-d'),
                'schedule_date_formatted' => $installment->schedule_date->format('M d, Y'),
                'principal_amount' => (float)$installment->principal_amount,
                'rent' => (float)$installment->rent,
                'fine_amount' => (float)$fine,
                'total_amount' => (float)$totalAmount,
                'days_late' => $daysLate,
                'is_overdue' => $installment->isOverdue(),
                'month_name' => $installment->schedule_date->format('F Y')
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'account' => [
                    'id' => $account->id,
                    'account_number' => $account->account_number,
                    'member_name' => $account->investment->member->name,
                    'member_id' => $account->investment->member->unique_id,
                    'current_balance' => (float)$account->current_balance,
                    'principal_amount' => (float)$account->investment->principal_amount,
                ],
                'installments' => $installments
            ]
        ]);
    }

    /**
     * Process investment collection payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:investment_accounts,id',
            'installment_id' => 'required|exists:investment_installments,id',
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

        $account = InvestmentAccount::with('investment')->findOrFail($request->account_id);
        $installment = InvestmentInstallment::findOrFail($request->installment_id);

        // Verify installment belongs to investment
        if ($installment->investment_id != $account->investment_id) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Installment does not belong to this investment account'
                ], 422);
            }
            return redirect()->back()->with('error', 'Installment does not belong to this investment account.');
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
            $investment = $account->investment;
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

            // Update investment account
            $account->total_principal_paid = (float)$account->total_principal_paid + (float)$installment->principal_amount;
            $account->total_rent_received = (float)$account->total_rent_received + (float)$installment->rent;
            $account->total_payments_made = (float)$account->total_payments_made + (float)$ledgerAmount;
            $account->total_installments_paid = (int)$account->total_installments_paid + 1;
            $account->installments_paid_count = (int)$account->installments_paid_count + 1;
            $account->installments_pending_count = max(0, (int)$account->installments_pending_count - 1);
            $account->current_balance = (float)$newBalance;
            $account->save();

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment collection processed successfully',
                    'data' => [
                        'installment' => $installment->fresh()->load('paymentMethod'),
                        'receipt_number' => $receiptNumber,
                        'fine' => $fine,
                        'days_late' => $daysLate
                    ]
                ]);
            }

            return redirect()->route('investments.collection.index')
                ->with('success', 'Investment collection processed successfully. Receipt: ' . $receiptNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process collection: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to process collection: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Calculate fine for an installment (AJAX endpoint)
     */
    public function calculateFine(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'installment_id' => 'required|exists:investment_installments,id',
            'paid_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $installment = InvestmentInstallment::findOrFail($request->installment_id);
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
