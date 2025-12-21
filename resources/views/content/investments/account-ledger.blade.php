@extends('layouts/contentNavbarLayout')

@section('title', 'Investment Account Ledger')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-book me-2"></i>Investment Account Ledger
                    </h5>
                    <div id="exportButtons" style="display: none;">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportLedger('print')">
                            <i class="bx bx-printer me-1"></i> Print
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="exportLedger('excel')">
                            <i class="bx bx-file me-1"></i> Excel
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="exportLedger('pdf')">
                            <i class="bx bx-file-blank me-1"></i> PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Investment Account Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="investment_select" class="form-label">Select Investment Account <span class="text-danger">*</span></label>
                            <select id="investment_select" class="form-select">
                                <option value="">-- Select Investment Account --</option>
                                @foreach($investments as $investment)
                                    <option value="{{ $investment['id'] }}" 
                                        data-account="{{ $investment['account_number'] }}"
                                        data-member="{{ $investment['member_name'] }}"
                                        data-member-id="{{ $investment['member_id'] }}">
                                        {{ $investment['display'] }}
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
                        <p class="mt-3 text-muted">Loading investment ledger...</p>
                    </div>

                    <!-- Investment Account Info -->
                    <div id="investmentInfo" class="row mb-4" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6 class="mb-1"><strong>Investment Account Information</strong></h6>
                                <p class="mb-0">
                                    <strong>Account Number:</strong> <span id="account_number"></span> | 
                                    <strong>Member:</strong> <span id="member_name"></span> (<span id="member_id"></span>) |
                                    <strong>Start Date:</strong> <span id="start_date"></span> |
                                    <strong>Principal Amount:</strong> <span id="principal_amount"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Ledger Table -->
                    <div id="ledgerContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="ledgerTable" style="border: 2px solid #ddd;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="text-align: center; width: 10%;">Date</th>
                                        <th style="text-align: center; width: 10%;">Ending date</th>
                                        <th style="text-align: left; width: 25%;">Particulars</th>
                                        <th style="text-align: right; width: 10%;">Dr</th>
                                        <th style="text-align: right; width: 10%;">Cr</th>
                                        <th style="text-align: right; width: 10%;">Balance</th>
                                        <th style="text-align: center; width: 8%;">Days</th>
                                        <th style="text-align: right; width: 12%;">Product</th>
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
                        <p class="text-muted">Select an investment account and click "Load Ledger" to view investment ledger.</p>
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
@endsection

@section('page-script')
<script>
let currentInvestmentId = null;

document.addEventListener('DOMContentLoaded', function() {
    const investmentSelect = document.getElementById('investment_select');
    const loadLedgerBtn = document.getElementById('loadLedgerBtn');

    investmentSelect.addEventListener('change', function() {
        currentInvestmentId = this.value;
        loadLedgerBtn.disabled = !currentInvestmentId;
        
        if (!currentInvestmentId) {
            hideAllSections();
        }
    });

    loadLedgerBtn.addEventListener('click', function() {
        if (currentInvestmentId) {
            loadLedger(currentInvestmentId);
        }
    });
});

function loadLedger(investmentId) {
    // Show loading, hide others
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('ledgerContainer').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('errorState').style.display = 'none';
    document.getElementById('investmentInfo').style.display = 'none';
    document.getElementById('exportButtons').style.display = 'none';

    const url = '{{ url("/app/investments/account-ledger") }}/' + investmentId;
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingSpinner').style.display = 'none';

        if (data.success) {
            displayLedger(data.data);
        } else {
            showError(data.message || 'Failed to load ledger');
        }
    })
    .catch(error => {
        document.getElementById('loadingSpinner').style.display = 'none';
        showError('An error occurred while loading the ledger');
        console.error('Error:', error);
    });
}

function displayLedger(data) {
    const investment = data.investment;
    const ledger = data.ledger;
    const totalProduct = data.total_product;
    const totalBalance = data.total_balance;

    // Show investment info
    document.getElementById('account_number').textContent = investment.account_number;
    document.getElementById('member_name').textContent = investment.member_name;
    document.getElementById('member_id').textContent = investment.member_id;
    document.getElementById('start_date').textContent = investment.start_date;
    document.getElementById('principal_amount').textContent = formatNumber(investment.principal_amount);
    document.getElementById('investmentInfo').style.display = 'block';

    // Populate ledger table
    const tbody = document.getElementById('ledgerTableBody');
    tbody.innerHTML = '';

    if (ledger.length === 0) {
        document.getElementById('emptyState').style.display = 'block';
        return;
    }

    ledger.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align: center;">${row.date}</td>
            <td style="text-align: center;">${row.ending_date}</td>
            <td style="text-align: left;">${row.particulars}</td>
            <td style="text-align: right;">${formatNumber(row.debit)}</td>
            <td style="text-align: right;">${formatNumber(row.credit)}</td>
            <td style="text-align: right;">${formatNumber(row.balance)}</td>
            <td style="text-align: center;">${row.days}</td>
            <td style="text-align: right;">${formatNumber(row.product)}</td>
        `;
        tbody.appendChild(tr);
    });

    // Update total product
    document.getElementById('totalProduct').textContent = formatNumber(totalProduct);

    // Show ledger container and export buttons
    document.getElementById('ledgerContainer').style.display = 'block';
    document.getElementById('exportButtons').style.display = 'block';
}

function hideAllSections() {
    document.getElementById('ledgerContainer').style.display = 'none';
    document.getElementById('emptyState').style.display = 'block';
    document.getElementById('errorState').style.display = 'none';
    document.getElementById('investmentInfo').style.display = 'none';
    document.getElementById('exportButtons').style.display = 'none';
}

function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorState').style.display = 'block';
}

function formatNumber(value) {
    if (value === 0) return '0.00';
    return parseFloat(value).toFixed(2);
}

function exportLedger(type) {
    if (!currentInvestmentId) {
        alert('Please select an investment account first');
        return;
    }

    const baseUrl = '{{ url("/app/investments/account-ledger") }}';
    const url = `${baseUrl}/${currentInvestmentId}/export?type=${type}`;
    
    if (type === 'print') {
        window.open(url, '_blank');
    } else {
        window.location.href = url;
    }
}
</script>
@endsection

