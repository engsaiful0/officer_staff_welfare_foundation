<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\FeeCollect;
use App\Models\Expense;
use App\Models\Technology;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\ExpenseHead;
use App\Models\Teacher;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Analytics extends Controller
{
  public function index()
  {
    // Get current date and date ranges
    $today = Carbon::today();
    $thisWeek = Carbon::now()->startOfWeek();
    $thisMonth = Carbon::now()->startOfMonth();
    $lastMonth = Carbon::now()->subMonth()->startOfMonth();

    // Department-wise student count (using Technology as department)
    $departmentWiseStudents = Student::with('technology')
        ->select('technology_id', DB::raw('count(*) as total'))
        ->groupBy('technology_id')
        ->get()
        ->map(function($item) {
            return [
                'department' => $item->technology->technology_name ?? 'Unknown',
                'count' => $item->total
            ];
        });

    // Fee Collection Analytics
    $feeCollection = [
        'today' => FeeCollect::whereDate('date', $today)->sum('total_amount'),
        'this_week' => FeeCollect::where('date', '>=', $thisWeek)->sum('total_amount'),
        'this_month' => FeeCollect::where('date', '>=', $thisMonth)->sum('total_amount'),
        'last_month' => FeeCollect::whereBetween('date', [$lastMonth, $thisMonth])->sum('total_amount'),
    ];

    // Expense Analytics
    $expenseAnalytics = [
        'today' => Expense::whereDate('expense_date', $today)->sum('amount'),
        'this_week' => Expense::where('expense_date', '>=', $thisWeek)->sum('amount'),
        'this_month' => Expense::where('expense_date', '>=', $thisMonth)->sum('amount'),
        'last_month' => Expense::whereBetween('expense_date', [$lastMonth, $thisMonth])->sum('amount'),
    ];

    // Student Statistics
    $studentStats = [
        'total' => Student::count(),
        'this_month' => Student::where('created_at', '>=', $thisMonth)->count(),
        'by_academic_year' => Student::with('academicYear')
            ->select('academic_year_id', DB::raw('count(*) as total'))
            ->groupBy('academic_year_id')
            ->get()
            ->map(function($item) {
                return [
                    'academic_year' => $item->academicYear->academic_year_name ?? 'Unknown',
                    'count' => $item->total
                ];
            }),
    ];

    // Teacher Statistics
    $teacherStats = [
        'total' => Teacher::count(),
        'this_month' => Teacher::where('created_at', '>=', $thisMonth)->count(),
    ];

    // Employee Statistics
    $employeeStats = [
        'total' => Employee::count(),
        'this_month' => Employee::where('created_at', '>=', $thisMonth)->count(),
    ];

    // Monthly Fee Collection Trend (Last 6 months)
    $monthlyFeeTrend = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = Carbon::now()->subMonths($i);
        $monthlyFeeTrend[] = [
            'month' => $month->format('M Y'),
            'amount' => FeeCollect::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('total_amount')
        ];
    }

    // Monthly Expense Trend (Last 6 months)
    $monthlyExpenseTrend = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = Carbon::now()->subMonths($i);
        $monthlyExpenseTrend[] = [
            'month' => $month->format('M Y'),
            'amount' => Expense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount')
        ];
    }

    // Expense by Category
    $expenseByCategory = Expense::with('expenseHead')
        ->select('expense_head_id', DB::raw('sum(amount) as total'))
        ->groupBy('expense_head_id')
        ->get()
        ->map(function($item) {
            return [
                'category' => $item->expenseHead->name ?? 'Unknown',
                'amount' => $item->total
            ];
        });

    // Recent Activities
    $recentActivities = collect()
        ->merge(
            Student::with('technology')->latest()->take(5)->get()->map(function($student) {
                return [
                    'type' => 'student',
                    'message' => "New student {$student->full_name_in_english_block_letter} enrolled in {$student->technology->technology_name}",
                    'date' => $student->created_at,
                    'icon' => 'ti ti-user-plus',
                    'color' => 'success'
                ];
            })
        )
        ->merge(
            FeeCollect::with('student')->latest()->take(5)->get()->map(function($fee) {
                return [
                    'type' => 'fee',
                    'message' => "Fee collected from {$fee->student->full_name_in_english_block_letter} - ৳{$fee->total_amount}",
                    'date' => $fee->date,
                    'icon' => 'ti ti-currency-dollar',
                    'color' => 'primary'
                ];
            })
        )
        ->merge(
            Expense::with('expenseHead')->latest()->take(5)->get()->map(function($expense) {
                return [
                    'type' => 'expense',
                    'message' => "Expense recorded for {$expense->expenseHead->name} - ৳{$expense->amount}",
                    'date' => $expense->expense_date,
                    'icon' => 'ti ti-receipt',
                    'color' => 'warning'
                ];
            })
        )
        ->sortByDesc('date')
        ->take(10);

    // Calculate growth percentages
    $feeGrowth = $feeCollection['last_month'] > 0 
        ? (($feeCollection['this_month'] - $feeCollection['last_month']) / $feeCollection['last_month']) * 100 
        : 0;

    $expenseGrowth = $expenseAnalytics['last_month'] > 0 
        ? (($expenseAnalytics['this_month'] - $expenseAnalytics['last_month']) / $expenseAnalytics['last_month']) * 100 
        : 0;

    return view('content.dashboard.dashboards-analytics', compact(
        'departmentWiseStudents',
        'feeCollection',
        'expenseAnalytics',
        'studentStats',
        'teacherStats',
        'employeeStats',
        'monthlyFeeTrend',
        'monthlyExpenseTrend',
        'expenseByCategory',
        'recentActivities',
        'feeGrowth',
        'expenseGrowth'
    ));
  }
}
