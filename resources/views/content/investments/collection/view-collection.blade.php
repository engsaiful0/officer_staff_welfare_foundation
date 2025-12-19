@extends('layouts.contentNavbarLayout')

@section('title', 'View Investment Collections')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bx bx-list-ul me-2"></i>Investment Collection History
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('investments.collection.index') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-plus me-1"></i> New Collection
                        </a>
                    </div>
                </div>
                <div class="card-body mt-4">
                    <!-- Filters -->
                    <form id="filterForm" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="member_id" class="form-label">Member</label>
                                <select class="form-select select2" id="member_id" name="member_id">
                                    <option value="">All Members</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->unique_id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="account_number" class="form-label">Account #</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Search Account">
                            </div>
                            <div class="col-md-2">
                                <label for="payment_method_id" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method_id" name="payment_method_id">
                                    <option value="">All Methods</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}">{{ $method->payment_method_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" id="resetFilters" class="btn btn-outline-secondary w-100" title="Reset">
                                    <i class="bx bx-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Export Buttons -->
                    <div class="d-flex justify-content-end gap-2 mb-3 no-print">
                        <button type="button" class="btn btn-outline-danger btn-sm export-btn" data-type="pdf">
                            <i class="bx bxs-file-pdf me-1"></i> PDF
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm export-btn" data-type="excel">
                            <i class="bx bxs-file-export me-1"></i> Excel
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm export-btn" data-type="print">
                            <i class="bx bx-printer me-1"></i> Print
                        </button>
                    </div>

                    <!-- AJAX Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="collectionTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt #</th>
                                    <th>Account #</th>
                                    <th>Member</th>
                                    <th>Inst #</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="collectionBody">
                                <tr>
                                    <td colspan="9" class="text-center">Loading collections...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div id="paginationLinks" class="mt-4 d-flex justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Base URL for collection routes
    const baseUrl = '{{ url("/app/investments/collection") }}';
    
    const filterForm = document.getElementById('filterForm');
    const collectionBody = document.getElementById('collectionBody');
    const paginationLinks = document.getElementById('paginationLinks');
    const resetBtn = document.getElementById('resetFilters');

    // Initial load
    fetchCollections();

    // Filter events
    filterForm.querySelectorAll('input, select').forEach(element => {
        element.addEventListener('change', () => fetchCollections(1));
    });

    // Reset filters
    resetBtn.addEventListener('click', () => {
        filterForm.reset();
        fetchCollections(1);
    });

    // Pagination click handling
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.closest('a').href);
            const page = url.searchParams.get('page');
            fetchCollections(page);
        }
    });

    // Export buttons
    document.querySelectorAll('.export-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            params.append('type', type);
            window.open('{{ route("investments.collection.export") }}?' + params.toString(), '_blank');
        });
    });

    function fetchCollections(page = 1) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        params.append('page', page);

        collectionBody.innerHTML = '<tr><td colspan="9" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>';

        fetch('{{ route("investments.view-collection") }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                renderTable(response.data);
                paginationLinks.innerHTML = response.pagination;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            collectionBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Failed to load collections.</td></tr>';
        });
    }

    function renderTable(data) {
        if (data.length === 0) {
            collectionBody.innerHTML = '<tr><td colspan="9" class="text-center">No collections found matching your filters.</td></tr>';
            return;
        }

        let html = '';
        data.forEach(item => {
            const paidDate = new Date(item.paid_date).toLocaleDateString();
            const netAmount = (parseFloat(item.total_amount) - (parseFloat(item.discount_amount) || 0)).toFixed(2);
            
            html += `
                <tr>
                    <td>${paidDate}</td>
                    <td><strong>${item.receipt_number}</strong></td>
                    <td>${item.investment?.account?.account_number || 'N/A'}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <span>${item.investment?.member?.name || 'N/A'}</span>
                            <small class="text-muted">${item.investment?.member?.unique_id || ''}</small>
                        </div>
                    </td>
                    <td>#${item.installment_number}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <strong>$${netAmount}</strong>
                            ${item.discount_amount > 0 ? `<small class="text-success">Disc: $${item.discount_amount}</small>` : ''}
                        </div>
                    </td>
                    <td>${item.payment_method?.payment_method_name || 'N/A'}</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="${baseUrl}/${item.id}" 
                               class="btn btn-sm btn-outline-info" 
                               title="View">
                                <i class="bx bx-show me-1"></i>View
                            </a>
                            <a href="${baseUrl}/${item.id}/edit" 
                               class="btn btn-sm btn-outline-primary" 
                               title="Edit">
                                <i class="bx bx-edit-alt me-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-collection" 
                                data-id="${item.id}" 
                                data-receipt="${item.receipt_number}"
                                title="Delete">
                                <i class="bx bx-trash me-1"></i>Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        collectionBody.innerHTML = html;

        // Attach delete event listeners
        document.querySelectorAll('.delete-collection').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const receipt = this.dataset.receipt;
                const url = baseUrl + '/' + id;

                if (confirm(`Are you sure you want to reverse this payment?\nReceipt: ${receipt}\n\nThis action will mark the installment as pending again.`)) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message || 'Collection payment reversed successfully');
                            fetchCollections();
                        } else {
                            toastr.error(data.message || 'Failed to reverse payment');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('An error occurred while reversing the payment');
                    });
                }
            });
        });
    }
});
</script>
@endsection

