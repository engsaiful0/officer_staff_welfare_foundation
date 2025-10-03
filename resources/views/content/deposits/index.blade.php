@extends('layouts/contentNavbarLayout')

@section('title', 'Deposits - List')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/deposits-list.js')}}"></script>
@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Deposits</h5>
    <a href="{{ route('deposits.add-deposit') }}" class="btn btn-primary">
      <i class="bx bx-plus me-1"></i> Add Deposit
    </a>
  </div>
  
  <div class="card-body">
    <!-- Filters -->
    <div class="row mb-4">
      <div class="col-md-3">
        <label for="member_filter" class="form-label">Member</label>
        <select class="form-select" id="member_filter">
          <option value="">All Members</option>
          @foreach($members as $member)
            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->member_unique_id }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label for="status_filter" class="form-label">Status</label>
        <select class="form-select" id="status_filter">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="matured">Matured</option>
          <option value="closed">Closed</option>
        </select>
      </div>
      <div class="col-md-2">
        <label for="type_filter" class="form-label">Type</label>
        <select class="form-select" id="type_filter">
          <option value="">All Types</option>
          <option value="savings">Savings</option>
          <option value="fixed">Fixed</option>
          <option value="recurring">Recurring</option>
        </select>
      </div>
      <div class="col-md-2">
        <label for="date_from" class="form-label">From Date</label>
        <input type="date" class="form-control" id="date_from">
      </div>
      <div class="col-md-2">
        <label for="date_to" class="form-label">To Date</label>
        <input type="date" class="form-control" id="date_to">
      </div>
      <div class="col-md-1 d-flex align-items-end">
        <button type="button" class="btn btn-outline-secondary" id="clear_filters">
          <i class="bx bx-x"></i>
        </button>
      </div>
    </div>

    <!-- Deposits Table -->
    <div class="table-responsive">
      <table class="table table-hover" id="depositsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Member</th>
            <th>Product</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Current Balance</th>
            <th>Rate</th>
            <th>Start Date</th>
            <th>Maturity Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($deposits as $deposit)
            <tr>
              <td>{{ $deposit->id }}</td>
              <td>
                <div class="d-flex flex-column">
                  <span class="fw-semibold">{{ $deposit->member->name }}</span>
                  <small class="text-muted">{{ $deposit->member->member_unique_id }}</small>
                </div>
              </td>
              <td>{{ $deposit->product_name ?: 'N/A' }}</td>
              <td>
                <span class="badge bg-label-{{ $deposit->deposit_type === 'savings' ? 'info' : ($deposit->deposit_type === 'fixed' ? 'warning' : 'success') }}">
                  {{ ucfirst($deposit->deposit_type) }}
                </span>
              </td>
              <td>৳{{ number_format($deposit->deposit_amount, 2) }}</td>
              <td>৳{{ number_format($deposit->current_balance, 2) }}</td>
              <td>{{ $deposit->rate_percentage }}%</td>
              <td>{{ $deposit->start_date->format('M d, Y') }}</td>
              <td>{{ $deposit->maturity_date ? $deposit->maturity_date->format('M d, Y') : 'N/A' }}</td>
              <td>
                <span class="badge bg-label-{{ $deposit->status === 'active' ? 'success' : ($deposit->status === 'matured' ? 'warning' : 'secondary') }}">
                  {{ ucfirst($deposit->status) }}
                </span>
              </td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('deposits.show', $deposit) }}">
                      <i class="bx bx-show me-1"></i> View
                    </a>
                    <a class="dropdown-item" href="{{ route('deposits.edit', $deposit) }}">
                      <i class="bx bx-edit me-1"></i> Edit
                    </a>
                    <a class="dropdown-item" href="{{ route('deposits.ledger.index', $deposit) }}">
                      <i class="bx bx-list-ul me-1"></i> Ledger
                    </a>
                    @if($deposit->status === 'active')
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item text-warning" href="#" onclick="closeDeposit({{ $deposit->id }})">
                        <i class="bx bx-x-circle me-1"></i> Close
                      </a>
                    @endif
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
      {{ $deposits->links() }}
    </div>
  </div>
</div>

<!-- Close Deposit Modal -->
<div class="modal fade" id="closeDepositModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Close Deposit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to close this deposit? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" id="confirmClose">Close Deposit</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function closeDeposit(depositId) {
  $('#closeDepositModal').modal('show');
  $('#confirmClose').off('click').on('click', function() {
    $.ajax({
      url: `/deposits/${depositId}/close`,
      type: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        location.reload();
      },
      error: function(xhr) {
        alert('Error closing deposit: ' + xhr.responseJSON.message);
      }
    });
  });
}

$(document).ready(function() {
  // Initialize DataTable
  $('#depositsTable').DataTable({
    responsive: true,
    pageLength: 25,
    order: [[0, 'desc']]
  });

  // Filter functionality
  $('#member_filter, #status_filter, #type_filter, #date_from, #date_to').on('change', function() {
    applyFilters();
  });

  $('#clear_filters').on('click', function() {
    $('#member_filter, #status_filter, #type_filter, #date_from, #date_to').val('');
    applyFilters();
  });

  function applyFilters() {
    const params = new URLSearchParams();
    
    if ($('#member_filter').val()) params.append('member_id', $('#member_filter').val());
    if ($('#status_filter').val()) params.append('status', $('#status_filter').val());
    if ($('#type_filter').val()) params.append('deposit_type', $('#type_filter').val());
    if ($('#date_from').val()) params.append('date_from', $('#date_from').val());
    if ($('#date_to').val()) params.append('date_to', $('#date_to').val());
    
    window.location.href = '{{ route("deposits.view-deposits") }}?' + params.toString();
  }
});
</script>
@endpush
