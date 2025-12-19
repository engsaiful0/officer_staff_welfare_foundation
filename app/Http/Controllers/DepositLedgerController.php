<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\LedgerEntry;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        $members = Member::select('id', 'name', 'unique_id')
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
}
