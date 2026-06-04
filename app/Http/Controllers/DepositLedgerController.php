<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class DepositLedgerController extends Controller
{
    use DepositLedgerControllerMethods;
    /**
     * Display ledger entries for a deposit
     */
    public function index(Deposit $deposit)
    {
        $ledgerEntries = $deposit->ledgerEntries()
            ->with('createdBy')
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $ledgerEntries
            ]);
        }

        return view('deposits.ledger.index', compact('deposit', 'ledgerEntries'));
    }

    /**
     * Record a deposit transaction
     */
    public function deposit(Request $request, Deposit $deposit)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $currentBalance = $deposit->current_balance;
            $newBalance = $currentBalance + $request->amount;

            $ledgerEntry = LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $request->entry_date,
                'type' => 'deposit',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'description' => $request->description ?: 'Additional deposit',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit recorded successfully',
                    'data' => $ledgerEntry
                ], 201);
            }

            return redirect()->back()->with('success', 'Deposit recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record deposit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to record deposit: ' . $e->getMessage());
        }
    }

    /**
     * Show member deposit ledger selection page
     */
    public function memberLedger(Request $request)
    {
        $members = Member::select('id', 'name', 'member_unique_id')
            ->orderBy('name')
            ->get();

        return view('content.deposits.member-ledger', compact('members'));
    }

    /**
     * Get deposit ledger for a member with days and product calculations
     */
    public function getMemberLedger(Request $request, $memberId)
    {
        $validator = Validator::make(['member_id' => $memberId], [
            'member_id' => 'required|exists:members,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid member ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $member = Member::with(['deposits' => function($query) {
                $query->where('status', 'active')
                    ->orderBy('start_date', 'asc');
            }])->findOrFail($memberId);

            // Get all ledger entries for all deposits of this member, ordered by date
            $ledgerEntries = LedgerEntry::where('entity_type', 'deposit')
                ->whereIn('entity_id', $member->deposits->pluck('id'))
                ->orderBy('entry_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            // Build ledger data with days and product calculations
            $ledgerData = [];
            $previousDate = null;
            $previousBalance = 0;

            // Add initial balance row if first entry date is after deposit start date
            if ($ledgerEntries->count() > 0 && $member->deposits->count() > 0) {
                $firstDeposit = $member->deposits->first();
                $firstEntry = $ledgerEntries->first();
                $depositStartDate = Carbon::parse($firstDeposit->start_date);
                $firstEntryDate = Carbon::parse($firstEntry->entry_date);

                if ($depositStartDate->lt($firstEntryDate)) {
                    $endingDate = $firstEntryDate->copy()->subDay();
                    $days = $depositStartDate->diffInDays($endingDate) + 1;

                    $ledgerData[] = [
                        'date' => $depositStartDate->format('d/m/Y'),
                        'ending_date' => $endingDate->format('d/m/Y'),
                        'particulars' => 'By balance',
                        'debit' => 0,
                        'credit' => 0,
                        'balance' => 0,
                        'days' => $days,
                        'product' => 0,
                        'entry_date' => $depositStartDate->format('Y-m-d'),
                        'type' => 'balance'
                    ];
                }
            }

            foreach ($ledgerEntries as $index => $entry) {
                $currentDate = Carbon::parse($entry->entry_date);
                $endingDate = null;
                $days = 0;

                // Calculate ending date (next entry date or today if last entry)
                if ($index < $ledgerEntries->count() - 1) {
                    $nextEntry = $ledgerEntries[$index + 1];
                    $endingDate = Carbon::parse($nextEntry->entry_date)->subDay();
                } else {
                    $endingDate = Carbon::today();
                }

                // Calculate days in period
                $days = $currentDate->diffInDays($endingDate) + 1;

                // Determine debit and credit
                $debit = 0;
                $credit = 0;
                if ($entry->type === 'withdrawal') {
                    $debit = $entry->amount;
                } elseif ($entry->type === 'deposit') {
                    $credit = $entry->amount;
                }

                // Get balance after this transaction
                $balance = $entry->balance_after;

                // Calculate product (Balance * Days)
                $product = $balance * $days;

                // Determine particulars
                $particulars = $entry->description;
                if (empty($particulars)) {
                    if ($index === 0) {
                        $particulars = 'By balance';
                    } else {
                        $particulars = ucfirst($entry->type);
                    }
                }

                $ledgerData[] = [
                    'date' => $currentDate->format('d/m/Y'),
                    'ending_date' => $endingDate->format('d/m/Y'),
                    'particulars' => $particulars,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'days' => $days,
                    'product' => $product,
                    'entry_date' => $currentDate->format('Y-m-d'),
                    'type' => $entry->type
                ];

                $previousDate = $currentDate;
                $previousBalance = $balance;
            }

            // If no entries but member has deposits, show message
            if (empty($ledgerData) && $member->deposits->count() > 0) {
                // Member has deposits but no ledger entries yet
                $ledgerData = [];
            } elseif (empty($ledgerData)) {
                // Member has no deposits at all
                $ledgerData = [];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'member' => [
                        'id' => $member->id,
                        'name' => $member->name,
                        'unique_id' => $member->unique_id
                    ],
                    'ledger' => $ledgerData,
                    'total_product' => collect($ledgerData)->sum('product'),
                    'total_balance' => !empty($ledgerData) ? end($ledgerData)['balance'] : 0
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show deposit account ledger selection page
     */
    public function accountLedger(Request $request)
    {
        $deposits = Deposit::with('member')
            ->where('status', 'active')
            ->orderBy('deposit_account_number')
            ->get()
            ->map(function($deposit) {
                return [
                    'id' => $deposit->id,
                    'account_number' => $deposit->deposit_account_number,
                    'member_name' => $deposit->member->name ?? 'N/A',
                    'member_id' => $deposit->member->unique_id ?? 'N/A',
                    'display' => $deposit->deposit_account_number . ' - ' . ($deposit->member->name ?? 'N/A')
                ];
            });

        return view('content.deposits.account-ledger', compact('deposits'));
    }

    /**
     * Get deposit account ledger with days and product calculations
     */
    public function getAccountLedger(Request $request, $depositId)
    {
        $validator = Validator::make(['deposit_id' => $depositId], [
            'deposit_id' => 'required|exists:deposits,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid deposit account ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deposit = Deposit::with('member')->findOrFail($depositId);

            // Get all ledger entries for this deposit, ordered by date
            $ledgerEntries = LedgerEntry::where('entity_type', 'deposit')
                ->where('entity_id', $deposit->id)
                ->orderBy('entry_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            // Build ledger data with days and product calculations
            $ledgerData = [];
            $depositStartDate = Carbon::parse($deposit->start_date);

            // Add initial balance row if first entry date is after deposit start date
            if ($ledgerEntries->count() > 0) {
                $firstEntry = $ledgerEntries->first();
                $firstEntryDate = Carbon::parse($firstEntry->entry_date);

                if ($depositStartDate->lt($firstEntryDate)) {
                    $endingDate = $firstEntryDate->copy()->subDay();
                    $days = $depositStartDate->diffInDays($endingDate) + 1;

                    $ledgerData[] = [
                        'date' => $depositStartDate->format('d/m/Y'),
                        'ending_date' => $endingDate->format('d/m/Y'),
                        'particulars' => 'By balance',
                        'debit' => 0,
                        'credit' => 0,
                        'balance' => 0,
                        'days' => $days,
                        'product' => 0,
                        'entry_date' => $depositStartDate->format('Y-m-d'),
                        'type' => 'balance'
                    ];
                }
            } elseif ($ledgerEntries->count() === 0) {
                // No entries yet, show initial balance row
                $endingDate = Carbon::today();
                $days = $depositStartDate->diffInDays($endingDate) + 1;

                $ledgerData[] = [
                    'date' => $depositStartDate->format('d/m/Y'),
                    'ending_date' => $endingDate->format('d/m/Y'),
                    'particulars' => 'By balance',
                    'debit' => 0,
                    'credit' => 0,
                    'balance' => 0,
                    'days' => $days,
                    'product' => 0,
                    'entry_date' => $depositStartDate->format('Y-m-d'),
                    'type' => 'balance'
                ];
            }

            foreach ($ledgerEntries as $index => $entry) {
                $currentDate = Carbon::parse($entry->entry_date);
                $endingDate = null;
                $days = 0;

                // Calculate ending date (next entry date or today if last entry)
                if ($index < $ledgerEntries->count() - 1) {
                    $nextEntry = $ledgerEntries[$index + 1];
                    $endingDate = Carbon::parse($nextEntry->entry_date)->subDay();
                } else {
                    $endingDate = Carbon::today();
                }

                // Calculate days in period
                $days = $currentDate->diffInDays($endingDate) + 1;

                // Determine debit and credit
                $debit = 0;
                $credit = 0;
                if ($entry->type === 'withdrawal') {
                    $debit = $entry->amount;
                } elseif ($entry->type === 'deposit') {
                    $credit = $entry->amount;
                }

                // Get balance after this transaction
                $balance = $entry->balance_after;

                // Calculate product (Balance * Days)
                $product = $balance * $days;

                // Determine particulars
                $particulars = $entry->description;
                if (empty($particulars)) {
                    $particulars = ucfirst($entry->type);
                }

                $ledgerData[] = [
                    'date' => $currentDate->format('d/m/Y'),
                    'ending_date' => $endingDate->format('d/m/Y'),
                    'particulars' => $particulars,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'days' => $days,
                    'product' => $product,
                    'entry_date' => $currentDate->format('Y-m-d'),
                    'type' => $entry->type
                ];
            }

            $totalProduct = collect($ledgerData)->sum('product');
            $totalBalance = !empty($ledgerData) ? end($ledgerData)['balance'] : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'deposit' => [
                        'id' => $deposit->id,
                        'account_number' => $deposit->deposit_account_number,
                        'member_name' => $deposit->member->name ?? 'N/A',
                        'member_id' => $deposit->member->unique_id ?? 'N/A',
                        'start_date' => $deposit->start_date->format('d/m/Y'),
                    ],
                    'ledger' => $ledgerData,
                    'total_product' => $totalProduct,
                    'total_balance' => $totalBalance
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export deposit account ledger
     */
    public function exportAccountLedger(Request $request, $depositId)
    {
        $type = $request->get('type', 'print'); // pdf, excel, print
        $deposit = Deposit::with('member')->findOrFail($depositId);

        // Get ledger data (reuse the same logic)
        $ledgerEntries = LedgerEntry::where('entity_type', 'deposit')
            ->where('entity_id', $deposit->id)
            ->orderBy('entry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $ledgerData = [];
        $depositStartDate = Carbon::parse($deposit->start_date);

        // Add initial balance row if first entry date is after deposit start date
        if ($ledgerEntries->count() > 0) {
            $firstEntry = $ledgerEntries->first();
            $firstEntryDate = Carbon::parse($firstEntry->entry_date);

            if ($depositStartDate->lt($firstEntryDate)) {
                $endingDate = $firstEntryDate->copy()->subDay();
                $days = $depositStartDate->diffInDays($endingDate) + 1;

                $ledgerData[] = [
                    'date' => $depositStartDate->format('d/m/Y'),
                    'ending_date' => $endingDate->format('d/m/Y'),
                    'particulars' => 'By balance',
                    'debit' => 0,
                    'credit' => 0,
                    'balance' => 0,
                    'days' => $days,
                    'product' => 0,
                ];
            }
        } elseif ($ledgerEntries->count() === 0) {
            $endingDate = Carbon::today();
            $days = $depositStartDate->diffInDays($endingDate) + 1;

            $ledgerData[] = [
                'date' => $depositStartDate->format('d/m/Y'),
                'ending_date' => $endingDate->format('d/m/Y'),
                'particulars' => 'By balance',
                'debit' => 0,
                'credit' => 0,
                'balance' => 0,
                'days' => $days,
                'product' => 0,
            ];
        }

        foreach ($ledgerEntries as $index => $entry) {
            $currentDate = Carbon::parse($entry->entry_date);
            $endingDate = null;

            if ($index < $ledgerEntries->count() - 1) {
                $nextEntry = $ledgerEntries[$index + 1];
                $endingDate = Carbon::parse($nextEntry->entry_date)->subDay();
            } else {
                $endingDate = Carbon::today();
            }

            $days = $currentDate->diffInDays($endingDate) + 1;

            $debit = 0;
            $credit = 0;
            if ($entry->type === 'withdrawal') {
                $debit = $entry->amount;
            } elseif ($entry->type === 'deposit') {
                $credit = $entry->amount;
            }

            $balance = $entry->balance_after;
            $product = $balance * $days;

            $particulars = $entry->description ?: ucfirst($entry->type);

            $ledgerData[] = [
                'date' => $currentDate->format('d/m/Y'),
                'ending_date' => $endingDate->format('d/m/Y'),
                'particulars' => $particulars,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'days' => $days,
                'product' => $product,
            ];
        }

        $totalProduct = collect($ledgerData)->sum('product');
        $totalBalance = !empty($ledgerData) ? end($ledgerData)['balance'] : 0;

        $data = compact('deposit', 'ledgerData', 'totalProduct', 'totalBalance');

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('content.deposits.account-ledger-export-pdf', $data);
            $filename = 'deposit-ledger-' . $deposit->deposit_account_number . '-' . date('Y-m-d') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'excel') {
            return Excel::download(new class($ledgerData, $deposit, $totalProduct, $totalBalance) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithTitle {
                private $ledgerData;
                private $deposit;
                private $totalProduct;
                private $totalBalance;

                public function __construct($ledgerData, $deposit, $totalProduct, $totalBalance) {
                    $this->ledgerData = $ledgerData;
                    $this->deposit = $deposit;
                    $this->totalProduct = $totalProduct;
                    $this->totalBalance = $totalBalance;
                }

                public function collection() {
                    return collect($this->ledgerData)->map(function($item) {
                        return [
                            $item['date'],
                            $item['ending_date'],
                            $item['particulars'],
                            $item['debit'],
                            $item['credit'],
                            $item['balance'],
                            $item['days'],
                            $item['product'],
                        ];
                    });
                }

                public function headings(): array {
                    return ['Date', 'Ending date', 'Particulars', 'Dr', 'Cr', 'Balance', 'Days', 'Product'];
                }

                public function title(): string {
                    return 'Deposit Ledger';
                }
            }, 'deposit-ledger-' . $deposit->deposit_account_number . '-' . date('Y-m-d') . '.xlsx');
        }

        if ($type === 'print') {
            return view('content.deposits.account-ledger-export-print', $data);
        }

        return redirect()->back();
    }
}
