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
<style>
  /* Make action dropdown button more visible */
  .btn-text-primary.dropdown-toggle {
    color: #696cff !important;
    background-color: rgba(105, 108, 255, 0.08);
    border: 1px solid rgba(105, 108, 255, 0.2);
    transition: all 0.2s ease;
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    min-width: 2.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .btn-text-primary.dropdown-toggle:hover {
    color: #696cff !important;
    background-color: rgba(105, 108, 255, 0.15) !important;
    border-color: rgba(105, 108, 255, 0.4) !important;
    transform: scale(1.05);
  }
  .btn-text-primary.dropdown-toggle:focus,
  .btn-text-primary.dropdown-toggle.show {
    color: #696cff !important;
    background-color: rgba(105, 108, 255, 0.2) !important;
    border-color: rgba(105, 108, 255, 0.5) !important;
    box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
  }
  .btn-text-primary.dropdown-toggle::after {
    display: none; /* Hide default Bootstrap dropdown arrow */
  }
  .btn-text-primary.dropdown-toggle span {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: 3px;
  }
</style>
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
            <th>Account Number</th>
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
              <td>
                <span class="fw-semibold text-primary">{{ $deposit->deposit_account_number ?: ($deposit->account_number ?: 'N/A') }}</span>
                <br><small class="text-muted">ID: #{{ $deposit->id }}</small>
              </td>
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
                  <button type="button" class="btn btn-sm btn-text-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                    <span style="font-size: 1.25rem; font-weight: 600; letter-spacing: 2px;">⋯</span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="{{ route('deposits.show', $deposit) }}">
                        <i class="bx bx-show me-2"></i> View
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('deposits.edit', $deposit) }}">
                        <i class="bx bx-edit me-2"></i> Edit
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="{{ route('deposits.ledger.index', $deposit) }}">
                        <i class="bx bx-list-ul me-2"></i> Ledger
                      </a>
                    </li>
                    @if($deposit->status === 'active')
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <a class="dropdown-item text-warning close-deposit-btn" href="#" data-deposit-id="{{ $deposit->id }}">
                          <i class="bx bx-x-circle me-2"></i> Close
                        </a>
                      </li>
                    @endif
                    @if($deposit->ledgerEntries()->count() <= 1)
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        @php
                          $accountNum = $deposit->deposit_account_number ?: ($deposit->account_number ?: 'N/A');
                          $deleteUrl = route('deposits.destroy', $deposit);
                        @endphp
                        <a class="dropdown-item text-danger delete-deposit-btn" href="#" 
                           data-deposit-id="{{ $deposit->id }}" 
                           data-account-number="{{ $accountNum }}" 
                           data-delete-url="{{ $deleteUrl }}">
                          <i class="bx bx-trash me-2"></i> Delete
                        </a>
                      </li>
                    @endif
                  </ul>
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

<!-- Delete Deposit Modal -->
<div class="modal fade" id="deleteDepositModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Delete Deposit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this deposit account?</p>
        <p class="mb-0"><strong>Account Number:</strong> <span id="deleteAccountNumber"></span></p>
        <p class="text-danger mt-2"><small>This action cannot be undone. The deposit will be permanently deleted.</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDelete">
          <i class="bx bx-trash me-1"></i> Delete Deposit
        </button>
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
      url: `/app/deposits/${depositId}/close`,
      type: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response.success) {
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Deposit closed successfully');
          } else {
            alert(response.message || 'Deposit closed successfully');
          }
          setTimeout(function() {
            location.reload();
          }, 1000);
        }
      },
      error: function(xhr) {
        const errorMessage = xhr.responseJSON?.message || 'Error closing deposit';
        if (typeof toastr !== 'undefined') {
          toastr.error(errorMessage);
        } else {
          alert(errorMessage);
        }
      }
    });
  });
}

function deleteDeposit(depositId, accountNumber, deleteUrl) {
  $('#deleteAccountNumber').text(accountNumber || 'N/A');
  $('#deleteDepositModal').modal('show');
  
  $('#confirmDelete').off('click').on('click', function() {
    const btn = $(this);
    const originalText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Deleting...');
    
    $.ajax({
      url: deleteUrl || ('{{ url("/app/deposits") }}/' + depositId),
      type: 'POST',
      data: {
        _method: 'DELETE',
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response.success) {
          $('#deleteDepositModal').modal('hide');
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Deposit deleted successfully');
          } else {
            alert(response.message || 'Deposit deleted successfully');
          }
          setTimeout(function() {
            location.reload();
          }, 1000);
        } else {
          btn.prop('disabled', false).html(originalText);
          const errorMessage = response.message || 'Failed to delete deposit';
          if (typeof toastr !== 'undefined') {
            toastr.error(errorMessage);
          } else {
            alert(errorMessage);
          }
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html(originalText);
        const errorMessage = xhr.responseJSON?.message || 'Error deleting deposit';
        if (typeof toastr !== 'undefined') {
          toastr.error(errorMessage);
        } else {
          alert(errorMessage);
        }
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

  // Close deposit button handler
  $(document).on('click', '.close-deposit-btn', function(e) {
    e.preventDefault();
    const depositId = $(this).data('deposit-id');
    closeDeposit(depositId);
  });

  // Delete deposit button handler
  $(document).on('click', '.delete-deposit-btn', function(e) {
    e.preventDefault();
    const depositId = $(this).data('deposit-id');
    const accountNumber = $(this).data('account-number');
    const deleteUrl = $(this).data('delete-url');
    deleteDeposit(depositId, accountNumber, deleteUrl);
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
