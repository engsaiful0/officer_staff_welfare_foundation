@extends('layouts/contentNavbarLayout')

@section('title', 'Monthly Fee Due Report')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
@endsection

@section('page-script')
<script>
// Define specific URLs for this page
window.monthlyReportUrls = {
  data: '{{ route("fee-management.monthly-report.data") }}',
  bulkUpdate: '{{ route("fee-management.monthly-report.bulk-update") }}',
  exportPdf: '{{ route("fee-management.monthly-report.export-pdf") }}',
  exportExcel: '{{ route("fee-management.monthly-report.export-excel") }}'
};

// Override buildUrl function to use specific URLs
window.buildUrl = function(path) {
  // Hardcoded URLs with correct base path
  if (path.includes('monthly-report/data')) {
    return '{{ url("app/fee-management/monthly-report/data") }}';
  } else if (path.includes('monthly-report/bulk-update')) {
    return '{{ url("app/fee-management/monthly-report/bulk-update") }}';
  } else if (path.includes('monthly-report/export-pdf')) {
    return '{{ url("app/fee-management/monthly-report/export-pdf") }}';
  } else if (path.includes('monthly-report/export-excel')) {
    return '{{ url("app/fee-management/monthly-report/export-excel") }}';
  }
  
  // Fallback to original logic - construct URL properly
  var baseUrl = '{{ url("/") }}';
  if (path.startsWith('/')) {
    path = path.substring(1);
  }
  
  // Remove trailing slash from baseUrl if it exists
  if (baseUrl.endsWith('/')) {
    baseUrl = baseUrl.slice(0, -1);
  }
  
  // Add single slash between baseUrl and path
  return baseUrl + '/' + path;
};

// Debug information
console.log('=== URL DEBUG INFO ===');
console.log('Laravel url("/"):', '{{ url("/") }}');
console.log('Laravel url("app/fee-management/monthly-report/data"):', '{{ url("app/fee-management/monthly-report/data") }}');
console.log('Test buildUrl result:', window.buildUrl('app/fee-management/monthly-report/data'));
console.log('Base URL:', '{{ url("/") }}');
console.log('Route URL:', '{{ route("fee-management.monthly-report.data") }}');
</script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Fee Management /</span> Monthly Fee Due Report
</h4>

<!-- Statistics Cards -->
<div class="row mb-4" id="statisticsCards">
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="me-1">
            <p class="text-heading mb-2">Total Students</p>
            <div class="d-flex align-items-center">
              <h4 class="mb-2 me-1 display-6" id="totalStudents">0</h4>
            </div>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-info rounded">
              <i class="ti ti-users ti-md"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="me-1">
            <p class="text-heading mb-2">Paid</p>
            <div class="d-flex align-items-center">
              <h4 class="mb-2 me-1 display-6 text-success" id="paidCount">0</h4>
            </div>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-success rounded">
              <i class="ti ti-check ti-md"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="me-1">
            <p class="text-heading mb-2">Unpaid</p>
            <div class="d-flex align-items-center">
              <h4 class="mb-2 me-1 display-6 text-warning" id="unpaidCount">0</h4>
            </div>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-warning rounded">
              <i class="ti ti-clock ti-md"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="me-1">
            <p class="text-heading mb-2">Overdue</p>
            <div class="d-flex align-items-center">
              <h4 class="mb-2 me-1 display-6 text-danger" id="overdueCount">0</h4>
            </div>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-danger rounded">
              <i class="ti ti-alert-triangle ti-md"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Financial Summary -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Financial Summary</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <div class="d-flex justify-content-between">
              <span>Total Fee Amount:</span>
              <strong>৳<span id="totalFeeAmount">0.00</span></strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="d-flex justify-content-between">
              <span>Total Fine Amount:</span>
              <strong>৳<span id="totalFineAmount">0.00</span></strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="d-flex justify-content-between">
              <span>Total Collected:</span>
              <strong class="text-success">৳<span id="totalCollected">0.00</span></strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="d-flex justify-content-between">
              <span>Total Pending:</span>
              <strong class="text-danger">৳<span id="totalPending">0.00</span></strong>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-6">
            <div class="d-flex justify-content-between">
              <span>Collection Rate:</span>
              <strong><span id="collectionRate">0</span>%</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="card mb-4">
  <div class="card-body">
    <div class="row">
      <div class="col-md-3">
        <label class="form-label" for="monthFilter">Month</label>
        <select class="form-select" id="monthFilter">
          @for($month = 1; $month <= 12; $month++)
            <option value="{{ $month }}" {{ $currentMonth == $month ? 'selected' : '' }}>
              {{ DateTime::createFromFormat('!m', $month)->format('F') }}
            </option>
          @endfor
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label" for="yearFilter">Year</label>
        <input type="number" class="form-control" id="yearFilter" value="{{ $currentYear }}" min="2020" max="2030">
      </div>
      <div class="col-md-2">
        <label class="form-label" for="statusFilter">Status</label>
        <select class="form-select" id="statusFilter">
          <option value="all">All</option>
          <option value="paid">Paid</option>
          <option value="unpaid">Unpaid</option>
          <option value="overdue">Overdue</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label" for="searchFilter">Search</label>
        <input type="text" class="form-control" id="searchFilter" placeholder="Student name or ID">
      </div>
      <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <button type="button" class="btn btn-primary w-100" id="applyFiltersBtn">
          <i class="ti ti-search"></i>
        </button>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-12">
        <div class="btn-group">
          <button type="button" class="btn btn-success" id="exportPdfBtn">
            <i class="ti ti-file-type-pdf me-1"></i>Export PDF
          </button>
          <button type="button" class="btn btn-info" id="exportExcelBtn">
            <i class="ti ti-file-spreadsheet me-1"></i>Export Excel
          </button>
          <button type="button" class="btn btn-warning" id="bulkActionBtn" disabled>
            <i class="ti ti-edit me-1"></i>Bulk Actions
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Report Table -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Monthly Fee Payment Records</h5>
    <span class="badge bg-primary" id="recordCount">0 records</span>
  </div>
  <div class="card-datatable table-responsive">
    <table class="table table-bordered" id="monthlyFeeTable">
      <thead>
        <tr>
          <th>
            <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
          </th>
          <th>SL</th>
          <th>Student ID</th>
          <th>Student Name</th>
          <th>Month</th>
          <th>Fee Amount</th>
          <th>Fine Amount</th>
          <th>Total Amount</th>
          <th>Due Date</th>
          <th>Payment Date</th>
          <th>Status</th>
          <th>Days Overdue</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Will be populated by JavaScript -->
      </tbody>
    </table>
  </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkActionModalLabel">Bulk Actions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="bulkActionForm">
          <div class="mb-3">
            <label class="form-label" for="bulkAction">Action</label>
            <select class="form-select" id="bulkAction" name="action" required>
              <option value="">Select Action</option>
              <option value="mark_paid">Mark as Paid</option>
              <option value="mark_unpaid">Mark as Unpaid</option>
            </select>
          </div>
          <div class="mb-3" id="paymentDateSection" style="display: none;">
            <label class="form-label" for="bulkPaymentDate">Payment Date</label>
            <input type="date" class="form-control" id="bulkPaymentDate" name="payment_date">
          </div>
          <div class="alert alert-info">
            <i class="ti ti-info-circle me-2"></i>
            <span id="selectedCount">0</span> payment records selected.
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmBulkActionBtn">Apply Changes</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('page-script')
<script>
// Pass PHP variables to JavaScript
window.currentMonth = {{ $currentMonth }};
window.currentYear = {{ $currentYear }};
window.feeSettings = @json($feeSettings);
</script>
@endsection
