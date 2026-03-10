<?php

namespace App\Http\Controllers;

use App\Models\DepositInstallmentAmount;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DepositInstallmentAmountController extends Controller
{
    /**
     * Display the deposit installment amounts list (Laravel pagination).
     */
    public function index(Request $request)
    {
        $query = DepositInstallmentAmount::with(['member:id,name,unique_id', 'user:id,name'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $installments = $query->paginate(15)->withQueryString();
        $members = Member::orderBy('name')->get(['id', 'name', 'unique_id']);
        $membersJson = $members->map(function ($m) {
            return ['id' => $m->id, 'name' => $m->name, 'unique_id' => $m->unique_id ?? ''];
        })->values();

        return view('content.monthly-deposit-installment-settings.index', compact('installments', 'members', 'membersJson'));
    }

    /**
     * Show the form for creating a new deposit installment.
     */
    public function create()
    {
        $members = Member::orderBy('name')->get(['id', 'name', 'unique_id']);
        $membersJson = $members->map(function ($m) {
            return ['id' => $m->id, 'name' => $m->name, 'unique_id' => $m->unique_id ?? ''];
        })->values();
        return view('content.monthly-deposit-installment-settings.create', compact('members', 'membersJson'));
    }

    /**
     * Get members list for dropdown (same format as members.get-members).
     */
    public function getMembers()
    {
        $members = Member::orderBy('name')->get(['id', 'name', 'unique_id']);
        return response()->json(['members' => $members]);
    }

    /**
     * Get data for DataTables (member_id, installment_amount, date, user_id with member name).
     */
    public function getData(Request $request)
    {
        $query = DepositInstallmentAmount::with(['member:id,name,unique_id', 'user:id,name'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $data = $query->get()->map(function ($row) {
            return $this->formatRecord($row);
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Get the last installment amount for a member (member always pays the last amount).
     */
    public function getLastAmount($memberId)
    {
        $last = DepositInstallmentAmount::where('member_id', $memberId)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return response()->json([
            'installment_amount' => $last ? (float) $last->installment_amount : null,
            'date' => $last && $last->date ? (\Carbon\Carbon::parse($last->date))->format('Y-m-d') : null,
            'month' => $last ? $last->month : null,
            'year' => $last ? $last->year : null,
        ]);
    }

    /**
     * Store a new deposit installment payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'installment_amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $user = Auth::user();
        $userId = $user ? $user->id : null;

        $record = DepositInstallmentAmount::create([
            'member_id' => $request->member_id,
            'installment_amount' => $request->installment_amount,
            'date' => $request->date,
            'month' => $request->filled('month') ? (int) $request->month : null,
            'year' => $request->filled('year') ? (int) $request->year : null,
            'user_id' => $userId,
        ]);

        $record->load(['member:id,name,unique_id', 'user:id,name']);

        return response()->json([
            'message' => 'Deposit installment created successfully.',
            'data' => $this->formatRecord($record),
        ], Response::HTTP_CREATED);
    }

    /**
     * Show a single deposit installment record (for View modal).
     */
    public function show($id)
    {
        $record = DepositInstallmentAmount::with(['member:id,name,unique_id', 'user:id,name'])
            ->findOrFail($id);
        return response()->json(['data' => $this->formatRecord($record)]);
    }

    /**
     * Update the specified deposit installment record.
     */
    public function update(Request $request, $id)
    {
        $record = DepositInstallmentAmount::findOrFail($id);

        $request->validate([
            'member_id' => 'required|exists:members,id',
            'installment_amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $record->update([
            'member_id' => $request->member_id,
            'installment_amount' => $request->installment_amount,
            'date' => $request->date,
            'month' => $request->filled('month') ? (int) $request->month : null,
            'year' => $request->filled('year') ? (int) $request->year : null,
        ]);

        $record->load(['member:id,name,unique_id', 'user:id,name']);

        return response()->json([
            'message' => 'Deposit installment updated successfully.',
            'data' => $this->formatRecord($record),
        ]);
    }

    protected function formatRecord($row)
    {
        $dateObj = $row->date ? \Carbon\Carbon::parse($row->date) : null;
        $months = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
        $month = $row->month !== null ? (int) $row->month : null;
        return [
            'id' => $row->id,
            'member_id' => $row->member_id,
            'member_name' => $row->member ? $row->member->name : '—',
            'member_unique_id' => $row->member ? $row->member->unique_id : '—',
            'installment_amount' => number_format((float) $row->installment_amount, 2),
            'installment_amount_raw' => (float) $row->installment_amount,
            'date' => $dateObj ? $dateObj->format('Y-m-d') : '',
            'date_formatted' => $dateObj ? $dateObj->format('M d, Y') : '—',
            'month' => $row->month,
            'month_name' => $month && isset($months[$month]) ? $months[$month] : '—',
            'year' => $row->year,
            'user_id' => $row->user_id,
            'user_name' => $row->user ? $row->user->name : '—',
            'created_at' => $row->created_at?->format('Y-m-d H:i'),
            'updated_at' => $row->updated_at?->format('Y-m-d H:i'),
        ];
    }

    /**
     * Remove the specified installment record.
     */
    public function destroy($id)
    {
        $record = DepositInstallmentAmount::findOrFail($id);
        $record->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
