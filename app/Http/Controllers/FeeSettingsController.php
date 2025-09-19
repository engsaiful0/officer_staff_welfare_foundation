<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeSettings;
use App\Models\MonthlyFeePayment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FeeSettingsController extends Controller
{
    /**
     * Display the fee settings page
     */
    public function index()
    {
        $feeSettings = FeeSettings::getActive() ?? new FeeSettings();
        
        return view('content.fee-management.settings', compact('feeSettings'));
    }

    /**
     * Store or update fee settings
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'monthly_fee_amount' => 'required|numeric|min:0|max:999999.99',
            'payment_deadline_day' => 'required|integer|min:1|max:31',
            'fine_type' => 'required|in:fixed,percentage',
            'fine_amount_per_day' => 'required_if:fine_type,fixed|nullable|numeric|min:0|max:99999.99',
            'fine_percentage' => 'required_if:fine_type,percentage|nullable|numeric|min:0|max:100',
            'maximum_fine_amount' => 'nullable|numeric|min:0|max:999999.99',
            'grace_period_days' => 'required|integer|min:0|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Deactivate all existing settings
            FeeSettings::where('is_active', true)->update(['is_active' => false]);

            // Create new settings
            $feeSettings = FeeSettings::create([
                'monthly_fee_amount' => $request->monthly_fee_amount,
                'payment_deadline_day' => $request->payment_deadline_day,
                'fine_amount_per_day' => $request->fine_type === 'fixed' ? $request->fine_amount_per_day : 0,
                'fine_percentage' => $request->fine_type === 'percentage' ? $request->fine_percentage : null,
                'is_percentage_fine' => $request->fine_type === 'percentage',
                'maximum_fine_amount' => $request->maximum_fine_amount,
                'grace_period_days' => $request->grace_period_days,
                'is_active' => true,
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fee settings updated successfully!',
                'data' => $feeSettings
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating fee settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current fee settings
     */
    public function getFeeSettings()
    {
        $feeSettings = FeeSettings::getActive();
        
        return response()->json([
            'success' => true,
            'data' => $feeSettings
        ]);
    }

    /**
     * Generate monthly payments for current month
     */
    public function generateMonthlyPayments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $createdCount = MonthlyFeePayment::generateMonthlyPayments(
                $request->month,
                $request->year,
                $request->academic_year_id
            );

            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$createdCount} monthly fee payments!",
                'created_count' => $createdCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating monthly payments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update overdue status for all unpaid payments
     */
    public function updateOverdueStatus()
    {
        try {
            $unpaidPayments = MonthlyFeePayment::unpaid()->get();
            $updatedCount = 0;

            foreach ($unpaidPayments as $payment) {
                $payment->calculateAndUpdateOverdue();
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} payment records!",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating overdue status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update fee amounts for all existing monthly fee payments
     */
    public function updateFeeAmounts()
    {
        try {
            $feeSettings = FeeSettings::getActive();
            if (!$feeSettings) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active fee settings found. Please configure fee settings first.'
                ], 400);
            }

            $payments = MonthlyFeePayment::where('fee_amount', 0)
                ->orWhereNull('fee_amount')
                ->get();

            $updatedCount = 0;
            foreach ($payments as $payment) {
                $payment->update([
                    'fee_amount' => $feeSettings->monthly_fee_amount,
                    'total_amount' => $feeSettings->monthly_fee_amount + $payment->fine_amount,
                ]);
                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated fee amounts for {$updatedCount} payment records!",
                'updated_count' => $updatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating fee amounts: ' . $e->getMessage()
            ], 500);
        }
    }
}
