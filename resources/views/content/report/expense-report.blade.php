@extends('layouts/layoutMaster')

@section('title', 'Expense Report')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script>
  $(function () {
    // Initialize datepickers
    $('.datepicker').flatpickr();

    // Initialize Select2
    $('#expense_head_id').select2({
      placeholder: "Select an Expense Head",
      allowClear: true
    });
  });
</script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Expense Report</h5>
        <div>
            <a href="{{ route('expense-report.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">Export to PDF</a>
            <a href="{{ route('expense-report.excel', request()->query()) }}" class="btn btn-success">Export to Excel</a>
        </div>
    </div>
  <div class="card-body">
    {{-- Search Form --}}
    <form action="{{ route('expense-report') }}" method="GET">
      <div class="row">
        <div class="col-md-3">
          <label for="expense_head_id" class="form-label">Expense Head</label>
          <select id="expense_head_id" name="expense_head_id" class="form-select">
            <option value="">All</option>
            @foreach ($expenseHeads as $head)
            <option value="{{ $head->id }}" {{ request('expense_head_id') == $head->id ? 'selected' : '' }}>
              {{ $head->name }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
            <label for="date_range" class="form-label">Date Range</label>
            <select id="date_range" name="date_range" class="form-select">
                <option value="">Custom</option>
                <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="last_six_months" {{ request('date_range') == 'last_six_months' ? 'selected' : '' }}>Last 6 Months</option>
                <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
            </select>
        </div>
        <div class="col-md-2">
          <label for="from_date" class="form-label">From Date</label>
          <input type="text" id="from_date" name="from_date" class="form-control datepicker"
            value="{{ request('from_date') }}">
        </div>
        <div class="col-md-2">
          <label for="to_date" class="form-label">To Date</label>
          <input type="text" id="to_date" name="to_date" class="form-control datepicker"
            value="{{ request('to_date') }}">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary mt-4">Search</button>
        </div>
      </div>
    </form>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Expense Head</th>
          <th>Date</th>
          <th>Remarks</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse ($expenses as $expense)
        <tr>
          <td>{{ $expense->expenseHead->name ?? '' }}</td>
          <td>{{ $expense->expense_date }}</td>
          <td>{{ $expense->remarks }}</td>
          <td>{{ $expense->amount }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center">No expenses found.</td>
        </tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">Total Amount:</th>
          <th>{{ number_format($totalAmount, 2) }}</th>
        </tr>
      </tfoot>
    </table>
  </div>
  <div class="card-footer">
    {{ $expenses->links() }}
  </div>
</div>
@endsection
