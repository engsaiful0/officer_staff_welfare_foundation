<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseHead;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('expenseHead');
        
        // Apply expense head filter
        if ($request->filled('expense_head_id')) {
            $query->where('expense_head_id', $request->expense_head_id);
        }
        
        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('remarks', 'like', "%{$searchTerm}%")
                  ->orWhere('amount', 'like', "%{$searchTerm}%")
                  ->orWhereHas('expenseHead', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        // Get pagination per page
        $perPage = $request->get('per_page', 10);
        
        // Paginate results
        $expenses = $query->latest('expense_date')->paginate($perPage)->withQueryString();
        
        // Get filter options with caching
        $expenseHeads = cache()->remember('expense_heads', 3600, function () {
            return ExpenseHead::all();
        });
        
        return view('content.expenses.index', compact('expenses', 'expenseHeads'));
    }

    public function getExpenses(Request $request)
    {
        $expenses = Expense::with('expenseHead')->get();
        return response()->json([
            'data' => $expenses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_head_id' => 'required|exists:expense_heads,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $expense = Expense::create($data);

        return response()->json($expense, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expense_head_id' => 'required|exists:expense_heads,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($request->all());

        return response()->json($expense);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Export expenses to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Expense::with('expenseHead');
        
        // Apply expense head filter
        if ($request->filled('expense_head_id')) {
            $query->where('expense_head_id', $request->expense_head_id);
        }
        
        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('remarks', 'like', "%{$searchTerm}%")
                  ->orWhere('amount', 'like', "%{$searchTerm}%")
                  ->orWhereHas('expenseHead', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $expenses = $query->latest('expense_date')->get();
        
        return Excel::download(new ExpensesExport($expenses), 'expenses_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Export expenses to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Expense::with('expenseHead');
        
        // Apply expense head filter
        if ($request->filled('expense_head_id')) {
            $query->where('expense_head_id', $request->expense_head_id);
        }
        
        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('remarks', 'like', "%{$searchTerm}%")
                  ->orWhere('amount', 'like', "%{$searchTerm}%")
                  ->orWhereHas('expenseHead', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $expenses = $query->latest('expense_date')->get();
        
        $pdf = Pdf::loadView('content.expenses.export-pdf', compact('expenses'));
        return $pdf->download('expenses_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
