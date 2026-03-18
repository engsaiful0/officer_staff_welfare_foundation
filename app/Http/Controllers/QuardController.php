<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Member;
use App\Models\Quard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class QuardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Quard::with(['member:id,name,unique_id,employees_id']);

        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quards = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $quards
            ]);
        }

        $members = Member::select('id', 'name', 'unique_id')->get();

        return view('content.quard.index', compact('quards', 'members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $memberId = $request->get('member_id');
        $member = $memberId ? Member::find($memberId) : null;
        $members = Member::select('id', 'name', 'unique_id')->get();

        return view('content.quard.create', compact('member', 'members'));
    }

    /**
     * Get total deposit amount for a member (sum of deposits.deposit_amount).
     */
    public function getMemberTotalDeposits($memberId)
    {
        $total = (float) Deposit::where('member_id', $memberId)->sum('deposit_amount');
        return response()->json([
            'member_id' => (int) $memberId,
            'total_deposit_amount' => $total,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'total_deposit_amount' => 'required|numeric|min:0',
            'percentage_of_deposit' => 'required|numeric|min:0|max:100',
            'quard_amount' => 'required|numeric|min:0',
            'period_in_years' => 'required|integer|min:1|max:50',
            'installment_number' => 'required|integer|min:1|max:600',
            'installment_amount' => 'required|numeric|min:0',
            'charge_percentage' => 'nullable|numeric|min:0|max:100',
            'charge_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'maturity_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,closed,matured',
            'notes' => 'nullable|string'
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

            $quard = Quard::create([
                'member_id' => $request->member_id,
                'total_deposit_amount' => $request->total_deposit_amount,
                'percentage_of_deposit' => $request->percentage_of_deposit,
                'quard_amount' => $request->quard_amount,
                'period_in_years' => $request->period_in_years,
                'installment_number' => $request->installment_number,
                'installment_amount' => $request->installment_amount,
                'charge_percentage' => $request->charge_percentage ?? 0,
                'charge_amount' => $request->charge_amount ?? 0,
                'start_date' => $request->start_date,
                'maturity_date' => $request->maturity_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quard created successfully',
                    'data' => $quard->load('member')
                ], 201);
            }

            return redirect()->route('quards.index')
                ->with('success', 'Quard created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create quard: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create quard: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quard $quard)
    {
        $quard->load(['member']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'quard' => $quard,
                ]
            ]);
        }

        return view('content.quard.show', compact('quard'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quard $quard)
    {
        $members = Member::select('id', 'name', 'unique_id')->get();
        return view('content.quard.edit', compact('quard', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quard $quard)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'total_deposit_amount' => 'required|numeric|min:0',
            'percentage_of_deposit' => 'required|numeric|min:0|max:100',
            'quard_amount' => 'required|numeric|min:0',
            'period_in_years' => 'required|integer|min:1|max:50',
            'installment_number' => 'required|integer|min:1|max:600',
            'installment_amount' => 'required|numeric|min:0',
            'charge_percentage' => 'nullable|numeric|min:0|max:100',
            'charge_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'maturity_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,closed,matured',
            'notes' => 'nullable|string'
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

            $quard->update([
                'member_id' => $request->member_id,
                'total_deposit_amount' => $request->total_deposit_amount,
                'percentage_of_deposit' => $request->percentage_of_deposit,
                'quard_amount' => $request->quard_amount,
                'period_in_years' => $request->period_in_years,
                'installment_number' => $request->installment_number,
                'installment_amount' => $request->installment_amount,
                'charge_percentage' => $request->charge_percentage ?? 0,
                'charge_amount' => $request->charge_amount ?? 0,
                'start_date' => $request->start_date,
                'maturity_date' => $request->maturity_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quard updated successfully',
                    'data' => $quard->load('member')
                ]);
            }

            return redirect()->route('quards.index')
                ->with('success', 'Quard updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update quard: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update quard: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quard $quard)
    {
        try {
            $quard->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Quard deleted successfully'
                ]);
            }

            return redirect()->route('quards.index')
                ->with('success', 'Quard deleted successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete quard: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete quard: ' . $e->getMessage());
        }
    }
}
