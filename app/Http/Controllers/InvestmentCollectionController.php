<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentAccount;
use App\Models\InvestmentInstallment;
use App\Models\LedgerEntry;
use App\Models\PaymentMethod;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InvestmentCollectionController extends Controller
{
    /**
     * View all investment collections with filters
     */
    public function viewCollection(Request $request)
    {
        if ($request->ajax()) {
            $query = InvestmentInstallment::with(['investment.member', 'paymentMethod', 'investment.account'])
                ->where('status', 'paid');

            // Apply filters
            if ($request->filled('member_id')) {
                $query->whereHas('investment', function($q) use ($request) {
                    $q->where('member_id', $request->member_id);
                });
            }

            if ($request->filled('account_number')) {
                $query->whereHas('investment.account', function($q) use ($request) {
                    $q->where('account_number', 'like', '%' . $request->account_number . '%');
                });
            }

            if ($request->filled('payment_method_id')) {
                $query->where('payment_method_id', $request->payment_method_id);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('paid_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('paid_date', '<=', $request->date_to);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('receipt_number', 'like', "%$search%")
                      ->orWhere('transaction_reference', 'like', "%$search%")
                      ->orWhereHas('investment.member', function($mq) use ($search) {
                          $mq->where('name', 'like', "%$search%")
                            ->orWhere('member_unique_id', 'like', "%$search%");
                      });
                });
            }

            $collections = $query->orderBy('paid_date', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $collections->items(),
                'pagination' => (string) $collections->links()
            ]);
        }

        $members = Member::select('id', 'name', 'member_unique_id')->get();
        $paymentMethods = PaymentMethod::all();

        return view('content.investments.collection.view-collection', compact('members', 'paymentMethods'));
    }

    /**
     * Export collections to PDF, Excel or Print
     */
    public function export(Request $request)
    {
        $type = $request->type; // pdf, excel, print
        
        // If single installment ID is provided, export single receipt
        if ($request->filled('installment_id')) {
            $installment = InvestmentInstallment::with(['investment.member', 'paymentMethod', 'investment.account'])
                ->where('status', 'paid')
                ->findOrFail($request->installment_id);
            
            if ($type === 'pdf') {
                $pdf = Pdf::loadView('content.investments.collection.receipt-pdf', compact('installment'));
                return $pdf->download('receipt-' . $installment->receipt_number . '.pdf');
            }
            
            if ($type === 'print') {
                return view('content.investments.collection.receipt-print', compact('installment'));
            }
        }
        
        $query = InvestmentInstallment::with(['investment.member', 'paymentMethod', 'investment.account'])
            ->where('status', 'paid');

        // Apply same filters as viewCollection
        if ($request->filled('member_id')) {
            $query->whereHas('investment', function($q) use ($request) {
                $q->where('member_id', $request->member_id);
            });
        }
        if ($request->filled('account_number')) {
            $query->whereHas('investment.account', function($q) use ($request) {
                $q->where('account_number', 'like', '%' . $request->account_number . '%');
            });
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('paid_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('paid_date', '<=', $request->date_to);
        }

        $collections = $query->orderBy('paid_date', 'desc')->get();

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('content.investments.collection.export-pdf', compact('collections'));
            return $pdf->download('investment-collections-' . date('Y-m-d') . '.pdf');
        }

        if ($type === 'excel') {
            // Simplistic excel export for now, usually requires an export class
            return Excel::download(new class($collections) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $collections;
                public function __construct($collections) { $this->collections = $collections; }
                public function collection() {
                    return $this->collections->map(function($item) {
                        return [
                            $item->paid_date->format('Y-m-d'),
                            $item->receipt_number,
                            $item->investment->account->account_number ?? 'N/A',
                            $item->investment->member->name,
                            $item->installment_number,
                            $item->total_amount,
                            $item->discount_amount,
                            $item->total_amount - ($item->discount_amount ?? 0),
                            $item->paymentMethod->payment_method_name ?? 'N/A',
                            $item->transaction_reference
                        ];
                    });
                }
                public function headings(): array {
                    return ['Date', 'Receipt #', 'Account #', 'Member', 'Inst #', 'Gross Amount', 'Discount', 'Net Paid', 'Method', 'Ref'];
                }
            }, 'investment-collections-' . date('Y-m-d') . '.xlsx');
        }

        if ($type === 'print') {
            return view('content.investments.collection.export-print', compact('collections'));
        }

        return redirect()->back();
    }

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

        // Include paid installments if requested (for edit form)
        $includePaid = $request->boolean('include_paid', false);
        $statuses = $includePaid ? ['pending', 'paid'] : ['pending'];
        
        $account = InvestmentAccount::with(['investment.member', 'investment.installments' => function($q) use ($statuses) {
            $q->whereIn('status', $statuses)->orderBy('installment_number');
        }])->findOrFail($request->account_id);

        $installments = $account->investment->installments->map(function($installment) {
            $daysLate = $installment->getDaysLate();
            $fine = $installment->status === 'paid' ? ($installment->fine_amount ?? 0) : $installment->calculateFine();
            $totalAmount = $installment->status === 'paid' 
                ? $installment->total_amount 
                : ($installment->principal_amount + $installment->rent + $fine);

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
                'month_name' => $installment->schedule_date->format('F Y'),
                'status' => $installment->status
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
                    'message' => 'Investment collection processed successfully! Receipt: ' . $receiptNumber,
                    'data' => [
                        'installment' => $installment->fresh()->load('paymentMethod'),
                        'installment_id' => $installment->id,
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
     * Display the specified collection
     */
    public function show(InvestmentInstallment $installment)
    {
        $installment->load([
            'investment.member',
            'investment.account',
            'paymentMethod'
        ]);

        return view('content.investments.collection.show', compact('installment'));
    }

    /**
     * Show the form for editing the specified collection
     */
    public function edit(InvestmentInstallment $installment)
    {
        // Only allow editing paid installments
        if ($installment->status !== 'paid') {
            return redirect()->back()->with('error', 'Only paid installments can be edited.');
        }

        $installment->load([
            'investment.member',
            'investment.account',
            'paymentMethod'
        ]);

        // Get all active investment accounts for dropdown
        $accounts = InvestmentAccount::with(['investment.member'])
            ->where('account_status', 'active')
            ->orderBy('account_number')
            ->get();

        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        return view('content.investments.collection.edit', compact('installment', 'paymentMethods', 'accounts'));
    }

    /**
     * Update the specified collection
     */
    public function update(Request $request, InvestmentInstallment $installment)
    {
        // Only allow updating paid installments
        if ($installment->status !== 'paid') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only paid installments can be updated.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Only paid installments can be updated.');
        }

        $validator = Validator::make($request->all(), [
            'account_id' => 'nullable|exists:investment_accounts,id',
            'installment_id' => 'nullable|exists:investment_installments,id',
            'paid_date' => 'required|date',
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

            // If a different installment is selected, use that one
            $targetInstallment = $installment;
            if ($request->filled('installment_id') && $request->installment_id != $installment->id) {
                $targetInstallment = InvestmentInstallment::findOrFail($request->installment_id);
                
                // Verify it's a paid installment
                if ($targetInstallment->status !== 'paid') {
                    if ($request->expectsJson() || $request->ajax()) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected installment is not paid and cannot be edited.'
                        ], 422);
                    }
                    return redirect()->back()->with('error', 'Selected installment is not paid.');
                }
            }

            $paidDate = Carbon::parse($request->paid_date);
            $discountAmount = (float)($request->discount_amount ?? 0);
            
            // Calculate net paid amount
            $baseTotal = (float)$targetInstallment->principal_amount + (float)$targetInstallment->rent + (float)$targetInstallment->fine_amount;
            $netPaidAmount = max(0, $baseTotal - $discountAmount);

            // Update installment
            $targetInstallment->update([
                'paid_date' => $paidDate,
                'discount_amount' => $discountAmount,
                'total_amount' => $baseTotal,
                'payment_method_id' => $request->payment_method_id,
                'transaction_reference' => $request->transaction_reference,
                'bank_name' => $request->bank_name,
                'check_number' => $request->check_number,
                'notes' => $request->notes,
                'updated_by' => auth()->id()
            ]);

            // Update related ledger entry if exists
            $ledgerEntry = LedgerEntry::where('entity_type', 'investment')
                ->where('entity_id', $targetInstallment->investment_id)
                ->where('description', 'like', "%installment #{$targetInstallment->installment_number}%")
                ->where('type', 'payment')
                ->orderBy('entry_date', 'desc')
                ->first();

            if ($ledgerEntry) {
                $ledgerEntry->update([
                    'entry_date' => $paidDate,
                    'amount' => $netPaidAmount,
                    'description' => "Payment for installment #{$targetInstallment->installment_number}" . 
                        ($targetInstallment->fine_amount > 0 ? " (Fine: $" . number_format($targetInstallment->fine_amount, 2) . ")" : "") .
                        ($discountAmount > 0 ? " (Discount: $" . number_format($discountAmount, 2) . ", Net: $" . number_format($netPaidAmount, 2) . ")" : "")
                ]);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collection updated successfully',
                    'data' => $targetInstallment->fresh()->load('paymentMethod'),
                    'installment_id' => $targetInstallment->id
                ]);
            }

            return redirect()->route('investments.collection.show', $targetInstallment)
                ->with('success', 'Collection updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update collection: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update collection: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified collection (reverse payment)
     */
    public function destroy(Request $request, InvestmentInstallment $installment)
    {
        // Only allow deleting paid installments
        if ($installment->status !== 'paid') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only paid installments can be deleted.'
                ], 422);
            }
            return redirect()->back()->with('error', 'Only paid installments can be deleted.');
        }

        try {
            DB::beginTransaction();

            $investment = $installment->investment;
            $account = $investment->account;

            // Reverse ledger entry
            $ledgerEntry = LedgerEntry::where('entity_type', 'investment')
                ->where('entity_id', $investment->id)
                ->where('description', 'like', "%installment #{$installment->installment_number}%")
                ->where('type', 'payment')
                ->orderBy('entry_date', 'desc')
                ->first();

            if ($ledgerEntry) {
                // Recalculate balance after reversal
                $previousBalance = $ledgerEntry->balance_after;
                $newBalance = $previousBalance + (float)$installment->principal_amount;

                // Update subsequent ledger entries
                LedgerEntry::where('entity_type', 'investment')
                    ->where('entity_id', $investment->id)
                    ->where('entry_date', '>=', $ledgerEntry->entry_date)
                    ->where('id', '!=', $ledgerEntry->id)
                    ->get()
                    ->each(function($entry) use ($installment) {
                        $entry->balance_after = (float)$entry->balance_after + (float)$installment->principal_amount;
                        $entry->save();
                    });

                $ledgerEntry->delete();
            }

            // Update account balances
            $netPaidAmount = (float)$installment->total_amount - (float)($installment->discount_amount ?? 0);
            $account->total_principal_paid = max(0, (float)$account->total_principal_paid - (float)$installment->principal_amount);
            $account->total_rent_received = max(0, (float)$account->total_rent_received - (float)$installment->rent);
            $account->total_payments_made = max(0, (float)$account->total_payments_made - (float)$netPaidAmount);
            $account->total_installments_paid = max(0, (int)$account->total_installments_paid - 1);
            $account->installments_paid_count = max(0, (int)$account->installments_paid_count - 1);
            $account->installments_pending_count = (int)$account->installments_pending_count + 1;
            $account->current_balance = (float)$newBalance;
            $account->save();

            // Reset installment to pending
            $installment->update([
                'status' => 'pending',
                'paid_date' => null,
                'fine_amount' => 0,
                'discount_amount' => 0,
                'paid_by' => null,
                'payment_method_id' => null,
                'transaction_reference' => null,
                'receipt_number' => null,
                'bank_name' => null,
                'check_number' => null,
                'notes' => null,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collection payment reversed successfully'
                ]);
            }

            return redirect()->route('investments.view-collection')
                ->with('success', 'Collection payment reversed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete collection: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete collection: ' . $e->getMessage());
        }
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
