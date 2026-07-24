<?php

namespace App\Http\Controllers;

use App\Models\InvestmentAccount;
use App\Models\InvestmentInstallment;
use App\Models\LedgerEntry;
use App\Models\PaymentMethod;
use App\Models\Member;
use App\Services\Investment\InvestmentCollectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use InvalidArgumentException;
use RuntimeException;

class InvestmentCollectionController extends Controller
{
    public function __construct(
        private readonly InvestmentCollectionService $collectionService
    ) {
    }

    /**
     * View all investment collections with filters
     */
    public function viewCollection(Request $request)
    {
        if ($request->ajax()) {
            $query = InvestmentInstallment::with(['investment.member', 'investment.investmentType', 'paymentMethod', 'investment.account'])
                ->where('status', 'paid');

            if ($request->filled('member_id')) {
                $query->whereHas('investment', function ($q) use ($request) {
                    $q->where('member_id', $request->member_id);
                });
            }

            if ($request->filled('account_number')) {
                $query->whereHas('investment.account', function ($q) use ($request) {
                    $q->where('account_number', 'like', '%'.$request->account_number.'%');
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
                $query->where(function ($q) use ($search) {
                    $q->where('receipt_number', 'like', "%$search%")
                        ->orWhere('transaction_reference', 'like', "%$search%")
                        ->orWhereHas('investment.member', function ($mq) use ($search) {
                            $mq->where('name', 'like', "%$search%")
                                ->orWhere('member_unique_id', 'like', "%$search%");
                        });
                });
            }

            $collections = $query->orderBy('paid_date', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $collections->items(),
                'pagination' => (string) $collections->links(),
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
        $type = $request->type;

        if ($request->filled('installment_id')) {
            $installment = InvestmentInstallment::with(['investment.member', 'investment.investmentType', 'paymentMethod', 'investment.account'])
                ->where('status', 'paid')
                ->findOrFail($request->installment_id);

            if ($type === 'pdf') {
                $pdf = Pdf::loadView('content.investments.collection.receipt-pdf', compact('installment'));

                return $pdf->download('receipt-'.$installment->receipt_number.'.pdf');
            }

            if ($type === 'print') {
                return view('content.investments.collection.receipt-print', compact('installment'));
            }
        }

        $query = InvestmentInstallment::with(['investment.member', 'investment.investmentType', 'paymentMethod', 'investment.account'])
            ->where('status', 'paid');

        if ($request->filled('member_id')) {
            $query->whereHas('investment', function ($q) use ($request) {
                $q->where('member_id', $request->member_id);
            });
        }
        if ($request->filled('account_number')) {
            $query->whereHas('investment.account', function ($q) use ($request) {
                $q->where('account_number', 'like', '%'.$request->account_number.'%');
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

            return $pdf->download('investment-collections-'.date('Y-m-d').'.pdf');
        }

        if ($type === 'excel') {
            return Excel::download(new class($collections) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
            {
                private $collections;

                public function __construct($collections)
                {
                    $this->collections = $collections;
                }

                public function collection()
                {
                    return $this->collections->map(function ($item) {
                        return [
                            $item->paid_date->format('Y-m-d'),
                            $item->receipt_number,
                            $item->investment->account->account_number ?? 'N/A',
                            $item->investment->member->name,
                            $item->investment->product_name ?? $item->investment->investmentType?->investment_type_name,
                            $item->investment->calculation_method,
                            $item->installment_number,
                            $item->principal_amount,
                            $item->rent,
                            $item->total_amount,
                            $item->discount_amount,
                            $item->total_amount - ($item->discount_amount ?? 0),
                            $item->paymentMethod->payment_method_name ?? 'N/A',
                            $item->transaction_reference,
                        ];
                    });
                }

                public function headings(): array
                {
                    return ['Date', 'Receipt #', 'Account #', 'Member', 'Product', 'Method', 'Inst #', 'Principal', 'Rent', 'Gross', 'Discount', 'Net Paid', 'Pay Method', 'Ref'];
                }
            }, 'investment-collections-'.date('Y-m-d').'.xlsx');
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
        $accounts = InvestmentAccount::with(['investment.member', 'investment.investmentType'])
            ->where('account_status', 'active')
            ->whereHas('investment', function ($q) {
                $q->where('status', 'active');
            })
            ->orderBy('account_number')
            ->get();

        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        return view('content.investments.collection.index', compact('accounts', 'paymentMethods'));
    }

    /**
     * Get pending installments for a selected investment account (AJAX)
     */
    public function getInstallments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_id' => 'required|exists:investment_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $account = InvestmentAccount::findOrFail($request->account_id);
        $data = $this->collectionService->getAccountCollectionData(
            $account,
            $request->boolean('include_paid', false)
        );

        return response()->json([
            'success' => true,
            'data' => $data,
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
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $account = InvestmentAccount::findOrFail($request->account_id);
            $installment = InvestmentInstallment::findOrFail($request->installment_id);
            $result = $this->collectionService->collect($account, $installment, $validator->validated());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment collection processed successfully! Receipt: '.$result['receipt_number'],
                    'data' => [
                        'installment' => $result['installment'],
                        'installment_id' => $result['installment']->id,
                        'receipt_number' => $result['receipt_number'],
                        'fine' => $result['fine'],
                        'days_late' => $result['days_late'],
                        'net_paid' => $result['net_paid'],
                    ],
                ]);
            }

            return redirect()->route('investments.collection.index')
                ->with('success', 'Investment collection processed successfully. Receipt: '.$result['receipt_number']);
        } catch (InvalidArgumentException|RuntimeException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process collection: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to process collection: '.$e->getMessage())
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
            'paid_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $installment = InvestmentInstallment::findOrFail($request->installment_id);
        $paidDate = Carbon::parse($request->paid_date);
        $fine = $installment->calculateFine($paidDate);
        $daysLate = $installment->getDaysLate($paidDate);
        $scheduleTotal = (float) $installment->principal_amount + (float) $installment->rent;
        $baseAmount = round($scheduleTotal + $fine, 2);

        return response()->json([
            'success' => true,
            'data' => [
                'fine' => $fine,
                'days_late' => $daysLate,
                'base_amount' => $baseAmount,
                'total_with_fine' => $baseAmount,
                'principal_amount' => (float) $installment->principal_amount,
                'rent' => (float) $installment->rent,
                'beginning_balance' => (float) $installment->beginning_balance,
                'ending_balance' => (float) $installment->ending_balance,
                'original_total' => (float) $installment->total_amount,
                'schedule_total' => round($scheduleTotal, 2),
            ],
        ]);
    }

    /**
     * Display the specified collection
     */
    public function show(InvestmentInstallment $installment)
    {
        $installment->load([
            'investment.member',
            'investment.investmentType',
            'investment.account',
            'paymentMethod',
        ]);

        return view('content.investments.collection.show', compact('installment'));
    }

    /**
     * Show the form for editing the specified collection
     */
    public function edit(InvestmentInstallment $installment)
    {
        if ($installment->status !== 'paid') {
            return redirect()->back()->with('error', 'Only paid installments can be edited.');
        }

        $installment->load([
            'investment.member',
            'investment.investmentType',
            'investment.account',
            'paymentMethod',
        ]);

        $accounts = InvestmentAccount::with(['investment.member', 'investment.investmentType'])
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
        if ($installment->status !== 'paid') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only paid installments can be updated.',
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
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $targetInstallment = $installment;
            if ($request->filled('installment_id') && $request->installment_id != $installment->id) {
                $targetInstallment = InvestmentInstallment::findOrFail($request->installment_id);

                if ($targetInstallment->status !== 'paid') {
                    DB::rollBack();
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected installment is not paid and cannot be edited.',
                        ], 422);
                    }

                    return redirect()->back()->with('error', 'Selected installment is not paid.');
                }
            }

            $paidDate = Carbon::parse($request->paid_date);
            $discountAmount = (float) ($request->discount_amount ?? 0);
            $baseTotal = (float) $targetInstallment->principal_amount + (float) $targetInstallment->rent + (float) $targetInstallment->fine_amount;
            $netPaidAmount = max(0, $baseTotal - $discountAmount);

            $targetInstallment->update([
                'paid_date' => $paidDate,
                'discount_amount' => $discountAmount,
                'total_amount' => $baseTotal,
                'payment_method_id' => $request->payment_method_id,
                'transaction_reference' => $request->transaction_reference,
                'bank_name' => $request->bank_name,
                'check_number' => $request->check_number,
                'notes' => $request->notes,
                'updated_by' => auth()->id(),
            ]);

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
                    'description' => "Payment for installment #{$targetInstallment->installment_number}".
                        ($targetInstallment->fine_amount > 0 ? ' (Fine: ৳'.number_format($targetInstallment->fine_amount, 2).')' : '').
                        ($discountAmount > 0 ? ' (Discount: ৳'.number_format($discountAmount, 2).', Net: ৳'.number_format($netPaidAmount, 2).')' : ''),
                ]);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collection updated successfully',
                    'data' => $targetInstallment->fresh()->load('paymentMethod'),
                    'installment_id' => $targetInstallment->id,
                ]);
            }

            return redirect()->route('investments.collection.show', $targetInstallment)
                ->with('success', 'Collection updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update collection: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update collection: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified collection (reverse payment)
     */
    public function destroy(Request $request, InvestmentInstallment $installment)
    {
        try {
            $this->collectionService->reverse($installment);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collection reversed successfully.',
                ]);
            }

            return redirect()->route('investments.view-collection')
                ->with('success', 'Collection reversed successfully.');
        } catch (RuntimeException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reverse collection: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to reverse collection: '.$e->getMessage());
        }
    }
}
