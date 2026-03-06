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
     * Display the deposit installment amounts list.
     */
    public function index()
    {
        $members = Member::orderBy('name')->get(['id', 'name', 'unique_id']);
        return view('content.members.monthly-deposit-installment-settings.index', compact('members'));
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
            $dateObj = $row->date ? \Carbon\Carbon::parse($row->date) : null;
            return [
                'id' => $row->id,
                'member_id' => $row->member_id,
                'member_name' => $row->member ? $row->member->name : '—',
                'member_unique_id' => $row->member ? $row->member->unique_id : '—',
                'installment_amount' => number_format((float) $row->installment_amount, 2),
                'installment_amount_raw' => (float) $row->installment_amount,
                'date' => $dateObj ? $dateObj->format('Y-m-d') : '',
                'date_formatted' => $dateObj ? $dateObj->format('M d, Y') : '—',
                'user_id' => $row->user_id,
                'user_name' => $row->user ? $row->user->name : '—',
                'created_at' => $row->created_at?->format('Y-m-d H:i'),
            ];
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
        ]);

        $user = Auth::user();
        $userId = $user ? $user->id : null;

        $record = DepositInstallmentAmount::create([
            'member_id' => $request->member_id,
            'installment_amount' => $request->installment_amount,
            'date' => $request->date,
            'user_id' => $userId,
        ]);

        $record->load(['member:id,name,unique_id', 'user:id,name']);

        return response()->json([
            'message' => 'Deposit installment saved successfully.',
            'data' => [
                'id' => $record->id,
                'member_id' => $record->member_id,
                'member_name' => $record->member?->name,
                'installment_amount' => number_format((float) $record->installment_amount, 2),
                'date' => $record->date ? (\Carbon\Carbon::parse($record->date))->format('Y-m-d') : null,
                'user_name' => $record->user?->name,
            ],
        ], Response::HTTP_CREATED);
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
