@extends('layouts/contentNavbarLayout')

@section('title', 'Deposits - List')



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
        display: none;
        /* Hide default Bootstrap dropdown arrow */
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
                <select class="form-select select2" id="member_filter">
                    <option value="">All Members</option>
                    @foreach($members as $member)
                    <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->member_unique_id }})</option>
                    @endforeach
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
            <div class="col-md-5 d-flex align-items-end">
                <div class="col-md-12">
                  
                    <a href="{{ route('deposits.view-deposits') }}" class="btn btn-secondary">Clear Filters</a>
                  </div>
            </div>
        </div>

        <!-- Deposits Table -->
        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="depositsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member Name</th>
                        <th>Member ID</th>
                        <th>Employee ID</th>
                        <th>Deposit Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deposits as $deposit)
                    <tr>
                        <td>{{ $deposits->firstItem() + $loop->index }}</td>
                        <td>{{ $deposit->member->name ?? '—' }}</td>
                        <td>{{ $deposit->member->unique_id ?? ($deposit->member->member_unique_id ?? '—') }}</td>
                        <td>{{ $deposit->member->employees_id ?? '—' }}</td>
                        <td>{{ number_format($deposit->deposit_amount, 2) }}</td>
                        <td>{{ $deposit->deposit_date?->format('M d, Y') ?? '—' }}</td>
                        <td>
                          <div class="d-inline-block">
                            <a href="{{ route('deposits.edit', $deposit) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                              <i class="bx bx-edit-alt"></i> Edit
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger delete-deposit"
                                    data-id="{{ $deposit->id }}"
                                    data-url="{{ route('deposits.destroy', $deposit->id) }}"
                                    title="Delete">
                              <span class="delete-text"><i class="bx bx-trash"></i> Delete</span>
                              <span class="spinner-border spinner-border-sm d-none delete-spinner" role="status" aria-hidden="true"></span>
                            </button>
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
                url: `/app/deposits/${depositId}/close`
                , type: 'PATCH'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    , 'Accept': 'application/json'
                    , 'X-Requested-With': 'XMLHttpRequest'
                }
                , success: function(response) {
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
                }
                , error: function(xhr) {
                    const errorMessage = (xhr.responseJSON && xhr.responseJSON.message) || 'Error closing deposit';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            });
        });
    }

    // Delete deposit with SweetAlert confirm + spinner
    $(document).on('click', '.delete-deposit', function(e) {
        e.preventDefault();
        const btn = $(this);
        const depositId = btn.data('id');
        const deleteUrl = btn.data('url') || '{{ url("/app/deposits") }}/' + depositId;
        const spinner = btn.find('.delete-spinner');
        const textSpan = btn.find('.delete-text');

        if (typeof Swal === 'undefined') {
            if (!confirm('Are you sure you want to delete this deposit? This action cannot be undone.')) {
                return;
            }
        }

        const proceedDelete = function() {
            if (btn.prop('disabled')) return;
            btn.prop('disabled', true);
            textSpan.addClass('d-none');
            spinner.removeClass('d-none');

            $.ajax({
                url: deleteUrl,
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
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Deposit deleted successfully');
                        } else {
                            alert(response.message || 'Deposit deleted successfully');
                        }
                        setTimeout(function() {
                            location.reload();
                        }, 800);
                    } else {
                        btn.prop('disabled', false);
                        spinner.addClass('d-none');
                        textSpan.removeClass('d-none');
                        const errorMessage = response.message || 'Failed to delete deposit';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    textSpan.removeClass('d-none');
                    const msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Error deleting deposit';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This deposit will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    proceedDelete();
                }
            });
        } else {
            proceedDelete();
        }
    });

    $(document).ready(function() {
        // Initialize DataTable
       

        // Close deposit button handler
        $(document).on('click', '.close-deposit-btn', function(e) {
            e.preventDefault();
            const depositId = $(this).data('deposit-id');
            closeDeposit(depositId);
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
            if ($('#type_filter').val()) params.append('deposit_type_id', $('#type_filter').val());
            if ($('#date_from').val()) params.append('date_from', $('#date_from').val());
            if ($('#date_to').val()) params.append('date_to', $('#date_to').val());

            window.location.href = '{{ route("deposits.view-deposits") }}?' + params.toString();
        }
    });

</script>
@endpush
