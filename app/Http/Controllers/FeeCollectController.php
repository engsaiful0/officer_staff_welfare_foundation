<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeCollect;
use App\Models\FeeHead;
use App\Models\Semester;
use App\Models\Student;
use App\Models\PaymentMethod;
use App\Models\AppSetting;
use App\Services\FeeManagementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeeCollectController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeCollect::with(['student', 'academic_year', 'semester'])->orderBy('id', 'desc');

        // Search by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Search by semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Search by student info (name or ID)
        if ($request->filled('student_info')) {
            $studentInfo = $request->student_info;
            $query->whereHas('student', function ($q) use ($studentInfo) {
                $q->where('full_name_in_english_block_letter', 'like', '%' . $studentInfo . '%')
                    ->orWhere('id', $studentInfo);
            });
        }

        // Search by date range
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $feeCollections = $query->paginate(10)->appends($request->except('page'));

        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        return view('content.collect-fee.index', compact('feeCollections', 'academicYears', 'semesters', 'request'));
    }

    public function create()
    {
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $payment_methods = PaymentMethod::all();
        
        // Generate year options (current year ± 10 years)
        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear - 10; $i <= $currentYear + 10; $i++) {
            $years[] = $i;
        }

        return view('content.collect-fee.create', compact('academicYears', 'semesters', 'payment_methods', 'years', 'currentYear'));
    }

    public function getStudents($academic_year_id, $semester_id)
    {
        try {
            $students = Student::where('academic_year_id', $academic_year_id)
                ->where('semester_id', $semester_id)
                ->select('id', 'student_unique_id', 'full_name_in_english_block_letter', 'email', 'personal_number')
                ->orderBy('student_unique_id')
                ->get();
                
            return response()->json($students);
        } catch (\Exception $e) {
            \Log::error('Error fetching students: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch students'], 500);
        }
    }

    public function getFees(Request $request, $semester_id, $fee_type)
    {
        $query = FeeHead::query();
        
        // Get months from query parameter
        $months = $request->get('months');
        if ($months) {
            $months = is_array($months) ? $months : explode(',', $months);
            $months = array_map('intval', $months); // Convert to integers
        }

        // Get payment date for fine calculation
        $paymentDate = $request->get('payment_date', date('Y-m-d'));

        if ($fee_type === 'Regular') {
            $query->where('fee_type', 'Regular')->where('semester_id', $semester_id);
        } elseif ($fee_type === 'Monthly') {
            $query->where('fee_type', 'Monthly');
            // Filter by selected months if provided
            if ($months && !empty($months)) {
                $query->whereIn('month_id', $months);
            }
        } else if ($fee_type === 'Both') {
            $query->where(function ($q) use ($semester_id, $months) {
                $q->where('fee_type', 'Monthly');
                // Filter monthly fees by selected months if provided
                if ($months && !empty($months)) {
                    $q->whereIn('month_id', $months);
                }
                $q->orWhere(function ($q2) use ($semester_id) {
                    $q2->where('fee_type', 'Regular')->where('semester_id', $semester_id);
                });
            });
        }

        $fees = $query->get();
        
        // Calculate fines for monthly fees
        $feeManagementService = new FeeManagementService();
        $feesWithFines = collect();
        
        if ($fee_type === 'Monthly' && $months && !empty($months)) {
            // For monthly fees with selected months, create one entry per selected month
            foreach ($months as $monthId) {
                $month = \App\Models\Month::find($monthId);
                if ($month) {
                    // Find the fee head for this month
                    $fee = $fees->where('month_id', $monthId)->first();
                    if ($fee) {
                        $feeHeadData = [
                            'id' => $fee->id,
                            'months' => [$monthId]
                        ];
                        
                        $overdueInfo = $feeManagementService->calculateOverdueFine([$feeHeadData], [$monthId], $paymentDate);
                        
                        $feeData = $fee->toArray();
                        $feeData['id'] = $fee->id . '_' . $monthId; // Unique ID for each month
                        $feeData['name'] = $month->month_name . ' ' . $fee->name;
                        $feeData['month_id'] = $monthId;
                        $feeData['month_name'] = $month->month_name;
                        $feeData['fine_amount'] = $overdueInfo['total_fine_amount'];
                        $feeData['overdue_days'] = $overdueInfo['overdue_days'];
                        $feeData['fine_details'] = $overdueInfo['fine_details'];
                        
                        $feesWithFines->push($feeData);
                    }
                }
            }
        } else {
            // For regular fees or when no specific months are selected
            foreach ($fees as $fee) {
                if ($fee->fee_type === 'Monthly') {
                    $feeHeadMonths = $months ?: [$fee->month_id];
                    
                    // Create separate entries for each month
                    foreach ($feeHeadMonths as $monthId) {
                        $month = \App\Models\Month::find($monthId);
                        if ($month) {
                            $feeHeadData = [
                                'id' => $fee->id,
                                'months' => [$monthId]
                            ];
                            
                            $overdueInfo = $feeManagementService->calculateOverdueFine([$feeHeadData], [$monthId], $paymentDate);
                            
                            $feeData = $fee->toArray();
                            $feeData['id'] = $fee->id . '_' . $monthId; // Unique ID for each month
                            $feeData['name'] = $month->month_name . ' ' . $fee->name;
                            $feeData['month_id'] = $monthId;
                            $feeData['month_name'] = $month->month_name;
                            $feeData['fine_amount'] = $overdueInfo['total_fine_amount'];
                            $feeData['overdue_days'] = $overdueInfo['overdue_days'];
                            $feeData['fine_details'] = $overdueInfo['fine_details'];
                            
                            $feesWithFines->push($feeData);
                        }
                    }
                } else {
                    // For non-monthly fees, keep as is
                    $feeData = $fee->toArray();
                    $feeData['fine_amount'] = 0;
                    $feeData['overdue_days'] = 0;
                    $feeData['fine_details'] = [];
                    $feesWithFines->push($feeData);
                }
            }
        }
        
        return response()->json($feesWithFines);
    }

    /**
     * Check if monthly fees are already paid for selected months
     */
    public function checkPaidStatus(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'months' => 'required|array',
            'months.*' => 'integer|min:1|max:12'
        ]);

        $studentId = $request->student_id;
        $months = $request->months;
        
        $paidMonths = [];
        $unpaidMonths = [];
        
        foreach ($months as $monthId) {
            $isPaid = \App\Models\StudentMonthlyFee::where('student_id', $studentId)
                ->where('month_id', $monthId)
                ->where('is_paid', true)
                ->exists();
                
            if ($isPaid) {
                $month = \App\Models\Month::find($monthId);
                $paidMonths[] = [
                    'month_id' => $monthId,
                    'month_name' => $month ? $month->month_name : "Month $monthId"
                ];
            } else {
                $month = \App\Models\Month::find($monthId);
                $unpaidMonths[] = [
                    'month_id' => $monthId,
                    'month_name' => $month ? $month->month_name : "Month $monthId"
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'paid_months' => $paidMonths,
            'unpaid_months' => $unpaidMonths,
            'has_paid_months' => count($paidMonths) > 0,
            'has_unpaid_months' => count($unpaidMonths) > 0
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required',
            'semester_id' => 'required',
            'total_payable' => 'required',
            'student_id' => 'required',
            'payment_method_id' => 'required',
            'fee_heads' => 'required|array',
            'discount' => 'nullable|numeric',
            'net_payable' => 'required|numeric',
            'year' => 'required|string',
        ]);

        // Validation for duplicate fee heads
        $studentId = $request->student_id;
        $feeHeadIds = $request->fee_heads;
        $months = $request->months;

        $previousFeeCollections = FeeCollect::where('student_id', $studentId)->get();

        $paidFeeHeadIds = [];
        if ($request->fee_type !== 'Monthly') {
            foreach ($previousFeeCollections as $collection) {
                $decodedFeeHeads = json_decode($collection->fee_heads, true);
                if (is_array($decodedFeeHeads)) {
                    // Extract fee head IDs from the array of objects
                    $paidFeeHeadIds = array_merge($paidFeeHeadIds, array_map(function($fee) { return $fee['id']; }, $decodedFeeHeads));
                }
            }

            foreach ($feeHeadIds as $feeHeadId) {
                if (in_array($feeHeadId, $paidFeeHeadIds)) {
                    $feeHead = FeeHead::find($feeHeadId);
                    $feeHeadName = $feeHead ? $feeHead->name : 'a fee head';
                    return response()->json(['error' => 'This student has already paid for ' . $feeHeadName . '.'], 422);
                }
            }
        } else if ($request->fee_type === 'Monthly' && $months) {
            // Check for duplicate monthly fees
            $paidMonths = [];
            foreach ($months as $monthId) {
                $isPaid = \App\Models\StudentMonthlyFee::where('student_id', $studentId)
                    ->where('month_id', $monthId)
                    ->where('is_paid', true)
                    ->exists();
                    
                if ($isPaid) {
                    $month = \App\Models\Month::find($monthId);
                    $monthName = $month ? $month->month_name : "Month $monthId";
                    $paidMonths[] = $monthName;
                }
            }
            
            if (!empty($paidMonths)) {
                $paidMonthsList = implode(', ', $paidMonths);
                return response()->json(['error' => "This student has already paid for the following months: $paidMonthsList. Please select only unpaid months."], 422);
            }
        }


        $totalAmount = 0;
        $isDiscountable = false;
        $feeHeadsDetails = [];
        foreach ($request->fee_heads as $feeHeadId) {
            $feeHead = FeeHead::find($feeHeadId);
            if ($feeHead) {
                if ($feeHead->fee_type === 'Monthly' && $months) {
                    $totalAmount += $feeHead->amount * count($months);
                } else {
                    $totalAmount += $feeHead->amount;
                }

                if ($feeHead->is_discountable) {
                    $isDiscountable = true;
                }
                $detail = [
                    'id' => $feeHead->id,
                    'name' => $feeHead->name,
                    'amount' => $feeHead->amount,
                ];
                if ($feeHead->fee_type === 'Monthly' && $months) {
                    $detail['months'] = $months;
                }
                $feeHeadsDetails[] = $detail;
            }
        }

        // Calculate overdue fine for monthly fees
        $feeManagementService = new FeeManagementService();
        
        // For monthly fees, calculate fine for each selected month individually
        $totalFineAmount = 0;
        $maxOverdueDays = 0;
        $allFineDetails = [];
        
        if ($months && !empty($months)) {
            foreach ($months as $monthId) {
                $month = \App\Models\Month::find($monthId);
                if ($month) {
                    // Find the fee head for this month
                    $feeHead = $feeHeadsDetails[0]; // Assuming there's one fee head for monthly fees
                    $feeHeadData = [
                        'id' => $feeHead['id'],
                        'months' => [$monthId]
                    ];
                    
                    $overdueInfo = $feeManagementService->calculateOverdueFine([$feeHeadData], [$monthId], $request->date);
                    
                    $totalFineAmount += $overdueInfo['total_fine_amount'];
                    $maxOverdueDays = max($maxOverdueDays, $overdueInfo['overdue_days']);
                    $allFineDetails = array_merge($allFineDetails, $overdueInfo['fine_details']);
                }
            }
        } else {
            $overdueInfo = $feeManagementService->calculateOverdueFine($feeHeadsDetails, $months, $request->date);
            $totalFineAmount = $overdueInfo['total_fine_amount'];
            $maxOverdueDays = $overdueInfo['overdue_days'];
            $allFineDetails = $overdueInfo['fine_details'];
        }
        
        $fineAmount = $totalFineAmount;
        $overdueDays = $maxOverdueDays;
        $fineDetails = $allFineDetails;

        // Add fine amount to total
        $totalAmount += $fineAmount;

        if ($request->discount && $request->discount > 0) {
            if (!$isDiscountable) {
                return response()->json(['error' => 'Discount is not applicable to the selected fee heads.'], 422);
            }
            $totalAmount -= $request->discount;
        }
        $user = Auth::user();
        $userId = $user->id;

        $feeCollect = FeeCollect::create([
            'academic_year_id' => $request->academic_year_id,
            'semester_id' => $request->semester_id,
            'student_id' => $request->student_id,
            'fee_heads' => json_encode($feeHeadsDetails),
            'discount' => $request->discount,
            'fine_amount' => $fineAmount,
            'overdue_days' => $overdueDays,
            'fine_details' => json_encode($fineDetails),
            'total_amount' => $totalAmount,
            'total_payable' =>$request->total_payable,
            'net_payable' => $request->net_payable,
            'user_id' => $userId,
            'payment_method_id' => $request->payment_method_id,
            'year' => $request->year,
            'months' => $months,
            'date' => date('Y-m-d', strtotime($request->date)),
        ]);

        // Process fee collection using the service
        $feeManagementService = new FeeManagementService();
        $feeManagementService->processFeeCollection($feeCollect);

        // Return JSON response for AJAX with additional fee status info
        $summary = $feeManagementService->updateFeeSummary($feeCollect->student_id, $feeCollect->academic_year_id);
        
        return response()->json([
            'success' => 'Fee collected successfully.',
            'message' => 'Fee collected successfully. Student has completed ' . $summary->semesters_completed . '/8 semester fees and ' . $summary->months_completed . '/48 monthly fees.',
            'fee_collect_id' => $feeCollect->id,
            'summary' => [
                'semesters_completed' => $summary->semesters_completed,
                'months_completed' => $summary->months_completed,
                'completion_percentage' => $summary->completion_percentage,
                'all_fees_paid' => $summary->all_fees_paid
            ]
        ]);
    }

    public function showReceipt($id)
    {
        $feeCollect = FeeCollect::with(['student', 'academic_year', 'semester', 'payment_method', 'user'])->findOrFail($id);
        $fee_heads = json_decode($feeCollect->fee_heads);
        $app_setting = AppSetting::first();
        
        // Get student fee summary for completion status
        $feeManagementService = new FeeManagementService();
        $summary = $feeManagementService->updateFeeSummary($feeCollect->student_id, $feeCollect->academic_year_id);
        
        return view('content.collect-fee.receipt', compact('feeCollect', 'fee_heads', 'app_setting', 'summary'));
    }

    public function showDetails($id)
    {
        $feeCollect = FeeCollect::with(['student', 'academic_year', 'semester', 'payment_method', 'user'])->findOrFail($id);
        $fee_heads = json_decode($feeCollect->fee_heads);
        $app_setting = AppSetting::first();
        return view('content.collect-fee.details', compact('feeCollect', 'fee_heads', 'app_setting'));
    }

    public function edit($id)
    {
        $feeCollect = FeeCollect::findOrFail($id);
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $payment_methods = PaymentMethod::all();
        $students = Student::where('academic_year_id', $feeCollect->academic_year_id)->where('semester_id', $feeCollect->semester_id)->get();
        $fee_heads = FeeHead::all();
        
        // Generate year options (current year ± 10 years)
        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear - 10; $i <= $currentYear + 10; $i++) {
            $years[] = $i;
        }
        
        return view('content.collect-fee.edit', compact('feeCollect', 'academicYears', 'semesters', 'payment_methods', 'students', 'fee_heads', 'years', 'currentYear'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'academic_year_id' => 'required',
            'semester_id' => 'required',
            'student_id' => 'required',
            'payment_method_id' => 'required',
            'fee_heads' => 'required|array',
            'discount' => 'nullable|numeric',
            'year' => 'required|digits:4|min:1900|max:' . (date('Y') + 10),
        ]);

        $feeCollect = FeeCollect::findOrFail($id);

        $totalAmount = 0;
        $feeHeadsDetails = [];
        foreach ($request->fee_heads as $feeHeadId) {
            $feeHead = FeeHead::find($feeHeadId);
            if ($feeHead) {
                $totalAmount += $feeHead->amount;
                $feeHeadsDetails[] = [
                    'id' => $feeHead->id,
                    'name' => $feeHead->name,
                    'amount' => $feeHead->amount,
                ];
            }
        }

        if ($request->discount) {
            $totalAmount -= $request->discount;
        }

        $feeCollect->update([
            'academic_year_id' => $request->academic_year_id,
            'semester_id' => $request->semester_id,
            'student_id' => $request->student_id,
            'fee_heads' => json_encode($feeHeadsDetails),
            'discount' => $request->discount,
            'total_amount' => $totalAmount,
            'payment_method_id' => $request->payment_method_id,
            'year' => $request->year,
            'months' => $request->months,
            'date' => date('Y-m-d', strtotime($request->date)),
        ]);

        return redirect()->route('app-collect-fee.receipt', $feeCollect->id)->with('success', 'Fee collection updated successfully.');
    }

    public function destroy($id)
    {
        $feeCollect = FeeCollect::findOrFail($id);
        $feeCollect->delete();

        return redirect()->route('app-collect-fee.view-collect-fee')->with('success', 'Fee collection deleted successfully.');
    }
    public function getPaidFeeHeads($student_id, $academic_year_id, $semester_id)
    {
        $previousFeeCollections = FeeCollect::where('student_id', $student_id)
            // ->where('academic_year_id', $academic_year_id) // We need to check all years for monthly fees
            // ->where('semester_id', $semester_id)
            ->get();

        $paidFeeHeads = ['ids' => [], 'months' => []];
        foreach ($previousFeeCollections as $collection) {
            $decodedFeeHeads = json_decode($collection->fee_heads, true);
            if (is_array($decodedFeeHeads)) {
                foreach ($decodedFeeHeads as $feeHead) {
                    if (isset($feeHead['id'])) {
                        $paidFeeHeads['ids'][] = $feeHead['id'];
                        if (isset($feeHead['months'])) {
                            $paidFeeHeads['months'] = array_merge($paidFeeHeads['months'], $feeHead['months']);
                        }
                    } else {
                         $paidFeeHeads['ids'][] = $feeHead;
                    }
                }
            }
        }
        $paidFeeHeads['ids'] = array_unique($paidFeeHeads['ids']);
        $paidFeeHeads['months'] = array_unique($paidFeeHeads['months']);

        return response()->json($paidFeeHeads);
    }

    public function getFeeSettings()
    {
        $feeSettings = FeeSettings::getActive();
        if (!$feeSettings) {
            return response()->json(['error' => 'No active fee settings found'], 404);
        }

        return response()->json([
            'payment_deadline_day' => $feeSettings->payment_deadline_day,
            'fine_amount_per_day' => $feeSettings->fine_amount_per_day,
            'maximum_fine_amount' => $feeSettings->maximum_fine_amount,
            'is_percentage_fine' => $feeSettings->is_percentage_fine,
            'fine_percentage' => $feeSettings->fine_percentage,
            'grace_period_days' => $feeSettings->grace_period_days,
        ]);
    }
}
