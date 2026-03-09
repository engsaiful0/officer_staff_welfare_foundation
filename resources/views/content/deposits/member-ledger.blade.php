@extends('layouts.contentNavbarLayout')

@section('title', 'Member Deposit Ledger')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-book me-2"></i>Member Deposit Ledger
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Member Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="member_select" class="form-label">Select Member <span class="text-danger">*</span></label>
                            <select id="member_select" class="form-select">
                                <option value="">-- Select Member --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" data-name="{{ $member->name }}" data-id="{{ $member->unique_id }}">
                                        {{ $member->name }} ({{ $member->unique_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" id="loadLedgerBtn" class="btn btn-primary" disabled>
                                <i class="bx bx-search me-1"></i>Load Ledger
                            </button>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading deposit ledger...</p>
                    </div>

                    <!-- Member Info -->
                    <div id="memberInfo" class="row mb-4" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6 class="mb-1"><strong>Member Information</strong></h6>
                                <p class="mb-0">
                                    <strong>Name:</strong> <span id="member_name"></span> | 
                                    <strong>ID:</strong> <span id="member_id"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ledger Table -->
                    <div id="ledgerContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="ledgerTable" style="border: 2px dashed #28a745;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="text-align: center;">Date</th>
                                        <th style="text-align: center;">Ending date</th>
                                        <th style="text-align: left;">Particulars</th>
                                        <th style="text-align: right;">Dr</th>
                                        <th style="text-align: right;">Cr</th>
                                        <th style="text-align: right;">Balance</th>
                                        <th style="text-align: center;">Days</th>
                                        <th style="text-align: right;">Product</th>
                                    </tr>
                                </thead>
                                <tbody id="ledgerTableBody">
                                    <!-- Ledger rows will be inserted here via AJAX -->
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" style="text-align: right;"><strong>Total Product:</strong></td>
                                        <td colspan="3" style="text-align: right;">
                                            <strong id="totalProduct">0.00</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-5" style="display: none;">
                        <i class="bx bx-book-open display-4 text-muted"></i>
                        <h5 class="mt-3">No Ledger Data</h5>
                        <p class="text-muted">Select a member and click "Load Ledger" to view deposit ledger.</p>
                    </div>

                    <!-- Error State -->
                    <div id="errorState" class="alert alert-danger" style="display: none;">
                        <i class="bx bx-error-circle me-2"></i>
                        <span id="errorMessage"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #ledgerTable {
        border-collapse: collapse;
        width: 100%;
    }
    #ledgerTable th,
    #ledgerTable td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    #ledgerTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    #ledgerTable tfoot {
        font-weight: bold;
        background-color: #e9ecef;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberSelect = document.getElementById('member_select');
    const loadLedgerBtn = document.getElementById('loadLedgerBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const memberInfo = document.getElementById('memberInfo');
    const ledgerContainer = document.getElementById('ledgerContainer');
    const ledgerTableBody = document.getElementById('ledgerTableBody');
    const emptyState = document.getElementById('emptyState');
    const errorState = document.getElementById('errorState');
    const errorMessage = document.getElementById('errorMessage');
    const totalProduct = document.getElementById('totalProduct');
    const memberNameSpan = document.getElementById('member_name');
    const memberIdSpan = document.getElementById('member_id');

    // Enable/disable load button based on member selection
    memberSelect.addEventListener('change', function() {
        loadLedgerBtn.disabled = !this.value;
    });

    // Load ledger on button click
    loadLedgerBtn.addEventListener('click', function() {
        const memberId = memberSelect.value;
        if (!memberId) {
            toastr.error('Please select a member');
            return;
        }

        loadLedger(memberId);
    });

    function loadLedger(memberId) {
        // Show loading spinner
        loadingSpinner.style.display = 'block';
        ledgerContainer.style.display = 'none';
        memberInfo.style.display = 'none';
        emptyState.style.display = 'none';
        errorState.style.display = 'none';
        loadLedgerBtn.disabled = true;
        loadLedgerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';

        // Get member info
        const selectedOption = memberSelect.options[memberSelect.selectedIndex];
        const memberName = selectedOption.getAttribute('data-name');
        const memberUniqueId = selectedOption.getAttribute('data-id');

        $.ajax({
            url: '{{ url("/app/deposits/member") }}/' + memberId + '/ledger',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success && response.data) {
                    const ledger = response.data.ledger;
                    const member = response.data.member;

                    // Hide loading spinner
                    loadingSpinner.style.display = 'none';

                    if (ledger && ledger.length > 0) {
                        // Show member info
                        memberNameSpan.textContent = member.name;
                        memberIdSpan.textContent = member.unique_id;
                        memberInfo.style.display = 'block';

                        // Render ledger table
                        renderLedgerTable(ledger);

                        // Show total product
                        totalProduct.textContent = parseFloat(response.data.total_product).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                        // Show ledger container
                        ledgerContainer.style.display = 'block';
                        emptyState.style.display = 'none';
                    } else {
                        emptyState.style.display = 'block';
                        ledgerContainer.style.display = 'none';
                    }
                } else {
                    showError(response.message || 'Failed to load ledger data');
                }
            },
            error: function(xhr) {
                let errorMsg = 'An error occurred while loading the ledger';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showError(errorMsg);
            },
            complete: function() {
                loadLedgerBtn.disabled = false;
                loadLedgerBtn.innerHTML = '<i class="bx bx-search me-1"></i>Load Ledger';
            }
        });
    }

    function renderLedgerTable(ledger) {
        ledgerTableBody.innerHTML = '';

        ledger.forEach(function(row) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="text-align: center;">${row.date}</td>
                <td style="text-align: center;">${row.ending_date}</td>
                <td style="text-align: left;">${row.particulars}</td>
                <td style="text-align: right;">${parseFloat(row.debit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td style="text-align: right;">${parseFloat(row.credit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td style="text-align: right;">${parseFloat(row.balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td style="text-align: center;">${row.days}</td>
                <td style="text-align: right;">${parseFloat(row.product).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
            `;
            ledgerTableBody.appendChild(tr);
        });
    }

    function showError(message) {
        loadingSpinner.style.display = 'none';
        errorState.style.display = 'block';
        errorMessage.textContent = message;
        ledgerContainer.style.display = 'none';
        emptyState.style.display = 'none';
        memberInfo.style.display = 'none';
    }
});
</script>
@endsection



