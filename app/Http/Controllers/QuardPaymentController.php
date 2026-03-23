<?php

namespace App\Http\Controllers;

use App\Models\Quard;
use App\Models\QuardPayment;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuardPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = QuardPayment::with(['member', 'quard']);

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        $members = Member::select('id', 'name', 'unique_id')->get();

        return view('content.quard-payment.index', compact('payments', 'members'));
    }

    public function create(Request $request)
    {
        $members = Member::select('id', 'name', 'unique_id')->get();
        return view('content.quard-payment.create', compact('members'));
    }

    public function edit(QuardPayment $quardPayment)
    {
        $members = Member::select('id', 'name', 'unique_id')->get();

        // Eager load relations used in the view.
        $quardPayment->load(['member', 'quard']);

        return view('content.quard-payment.edit', compact('quardPayment', 'members'));
    }

    /**
     * When selecting a member, return the latest active quard amount.
     */
    public function getQuardAmountForMember($memberId)
    {
        $quard = Quard::where('member_id', $memberId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$quard) {
            $quard = Quard::where('member_id', $memberId)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return response()->json([
            'quard_id' => $quard ? (int) $quard->id : null,
            'payment_amount' => $quard ? (float) $quard->installment_amount : 0,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'quard_id' => 'required|exists:quards,id',
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payment = QuardPayment::create([
            'member_id' => $request->member_id,
            'quard_id' => $request->quard_id,
            'payment_amount' => $request->payment_amount,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quard payment created successfully.',
            'data' => $payment->load(['member', 'quard']),
        ], 201);
    }

    public function update(Request $request, QuardPayment $quardPayment)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'quard_id' => 'required|exists:quards,id',
            'payment_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $quardPayment->update([
            'member_id' => $request->member_id,
            'quard_id' => $request->quard_id,
            'payment_amount' => $request->payment_amount,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quard payment updated successfully.',
            'data' => $quardPayment->load(['member', 'quard']),
        ]);
    }

    public function destroy(Request $request, QuardPayment $quardPayment)
    {
        try {
            $quardPayment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Quard payment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quard payment.',
            ], 500);
        }
    }
}

