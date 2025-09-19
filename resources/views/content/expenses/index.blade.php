@extends('layouts/layoutMaster')

@section('title', 'Expense Management')

<!-- Page Scripts -->
@section('page-script')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
  window.expenseStoreUrl = '{{ route("expenses.store") }}';
  window.expenseUpdateUrl = '{{ route("expenses.update", ":id") }}';
</script>
<script src="{{ asset('assets/js/expense-management.js') }}?v={{ time() }}"></script>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="card-title">Expense Management</h5>
    <div class="card-actions">
      <a href="{{ route('expenses.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
        <i class="ti ti-file-excel me-1"></i>Export Excel
      </a>
      <a href="{{ route('expenses.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
        <i class="ti ti-file-pdf me-1"></i>Export PDF
      </a>
      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#add-new-record">
        <i class="ti ti-plus me-1"></i>Add Expense
      </button>
    </div>
  </div>
  <div class="card-body">
    <!-- Filter Section -->
    <form method="GET" action="{{ route('expenses.index') }}" class="mb-4">
      <div class="row">
        <div class="col-md-3">
          <label for="expense_head_id" class="form-label">Expense Category</label>
          <select name="expense_head_id" id="expense_head_id" class="form-select select2">
            <option value="">All Categories</option>
            @foreach($expenseHeads as $head)
              <option value="{{ $head->id }}" {{ request('expense_head_id') == $head->id ? 'selected' : '' }}>
                {{ $head->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label for="date_from" class="form-label">Date From</label>
          <input type="date" name="date_from" id="date_from" class="form-control" 
                 value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
          <label for="date_to" class="form-label">Date To</label>
          <input type="date" name="date_to" id="date_to" class="form-control" 
                 value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3">
          <label for="search" class="form-label">Search</label>
          <input type="text" name="search" id="search" class="form-control" 
                 placeholder="Search expenses..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <label for="per_page" class="form-label">Rows per page</label>
          <select name="per_page" id="per_page" class="form-select">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
          </select>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-12">
          <button type="submit" class="btn btn-primary">Filter</button>
          <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Clear Filters</a>
        </div>
      </div>
    </form>

    <!-- Results Summary -->
    <div class="row mb-3">
      <div class="col-md-6">
        <p class="text-muted">
          Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} 
          of {{ $expenses->total() }} results
        </p>
      </div>
      <div class="col-md-6 text-end">
        <strong>Total Amount: {{ number_format($expenses->sum('amount'), 2) }}</strong>
      </div>
    </div>

    <!-- Expenses Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Expense Head</th>
            <th>Expense Date</th>
            <th>Amount</th>
            <th>Remarks</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($expenses as $index => $expense)
            <tr>
              <td>{{ $expenses->firstItem() + $index }}</td>
              <td>{{ $expense->expenseHead->name ?? 'N/A' }}</td>
              <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>
              <td class="text-end">{{ number_format($expense->amount, 2) }}</td>
              <td>{{ $expense->remarks ?? 'N/A' }}</td>
              <td>
                <div class="d-inline-block">
                  <button type="button" class="btn btn-sm btn-text-secondary rounded-pill btn-icon edit-expense" 
                          data-id="{{ $expense->id }}" 
                          data-expense-head-id="{{ $expense->expense_head_id }}"
                          data-expense-date="{{ $expense->expense_date }}"
                          data-amount="{{ $expense->amount }}"
                          data-remarks="{{ $expense->remarks }}">
                    <i class="ti ti-pencil ti-md"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-text-secondary rounded-pill btn-icon delete-expense" 
                          data-id="{{ $expense->id }}" data-url="{{ route('expenses.destroy', $expense->id) }}">
                    <i class="ti ti-trash ti-md"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center">No expenses found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
      <div>
        <p class="text-muted">
          Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} 
          of {{ $expenses->total() }} results
        </p>
      </div>
      <div>
        {{ $expenses->links() }}
      </div>
    </div>
  </div>
</div>
<!-- Modal to add new record -->
<div class="offcanvas offcanvas-end" id="add-new-record">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="exampleModalLabel">New Expense</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
      <div class="col-sm-12">
        <label class="form-label" for="add_expense_head_id">Expense Head</label>
        <div class="input-group input-group-merge">
          
          <select id="add_expense_head_id" name="expense_head_id" class="form-select select2">
            <option value="">Select Expense Head</option>
            @foreach($expenseHeads as $head)
              <option value="{{ $head->id }}">{{ $head->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-sm-12">
        <label class="form-label" for="add_expense_date">Expense Date</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-calendar"></i></span>
          <input type="date" id="add_expense_date" name="expense_date" class="form-control" />
        </div>
      </div>
      <div class="col-sm-12">
        <label class="form-label" for="add_amount">Amount</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-currency-dollar"></i></span>
          <input type="number" id="add_amount" name="amount" class="form-control" placeholder="Enter Amount" step="0.01" />
        </div>
      </div>
      <div class="col-sm-12">
        <label class="form-label" for="add_remarks">Remarks</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-file-text"></i></span>
          <textarea id="add_remarks" name="remarks" class="form-control" placeholder="Enter Remarks"></textarea>
        </div>
      </div>
      <div class="col-sm-12">
        <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Submit</button>
        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal to edit record -->
<div class="offcanvas offcanvas-end" id="edit-record">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="editModalLabel">Edit Expense</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body flex-grow-1">
    <form class="edit-record pt-0 row g-2" id="form-edit-record" onsubmit="return false">
      <input type="hidden" id="edit_expense_id" name="expense_id">
      <div class="col-sm-12">
        <label class="form-label" for="edit_expense_head_id">Expense Head</label>
        <div class="input-group input-group-merge">
         
          <select id="edit_expense_head_id" name="expense_head_id" class="form-select select2">
            <option value="">Select Expense Head</option>
            @foreach($expenseHeads as $head)
              <option value="{{ $head->id }}">{{ $head->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-sm-12">
        <label class="form-label" for="edit_expense_date">Expense Date</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-calendar"></i></span>
          <input type="date" id="edit_expense_date" name="expense_date" class="form-control" />
        </div>
      </div>
      <div class="col-sm-12">
        <label class="form-label" for="edit_amount">Amount</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-currency-dollar"></i></span>
          <input type="number" id="edit_amount" name="amount" class="form-control" placeholder="Enter Amount" step="0.01" />
        </div>
      </div>
      <div class="col-sm-12">
        <label class="form-label" for="edit_remarks">Remarks</label>
        <div class="input-group input-group-merge">
          <span class="input-group-text"><i class="ti ti-file-text"></i></span>
          <textarea id="edit_remarks" name="remarks" class="form-control" placeholder="Enter Remarks"></textarea>
        </div>
      </div>
      <div class="col-sm-12">
        <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Update</button>
        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
      </div>
    </form>
  </div>
</div>
<!--/ DataTable with Buttons -->
@endsection
