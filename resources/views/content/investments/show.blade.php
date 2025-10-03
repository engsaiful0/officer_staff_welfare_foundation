@extends('layouts.contentNavbarLayout')

@section('title', 'Investment Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Investment Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('investments.edit', $investment) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Investment Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Investment Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Investment ID:</strong></td>
                                    <td>{{ $investment->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Member:</strong></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $investment->member->name }}</span>
                                            <small class="text-muted">{{ $investment->member->member_unique_id }}</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Product Name:</strong></td>
                                    <td>{{ $investment->product_name ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Principal Amount:</strong></td>
                                    <td>${{ number_format($investment->principal_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current Balance:</strong></td>
                                    <td class="text-success fw-semibold">${{ number_format($investment->current_balance, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Terms & Conditions</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Interest Rate:</strong></td>
                                    <td>{{ number_format($investment->rate_percentage, 2) }}% ({{ ucfirst($investment->rate_period) }})</td>
                                </tr>
                                <tr>
                                    <td><strong>Accrual Frequency:</strong></td>
                                    <td>{{ ucfirst($investment->frequency) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Start Date:</strong></td>
                                    <td>{{ $investment->start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Expiry Date:</strong></td>
                                    <td>{{ $investment->expiry_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Term:</strong></td>
                                    <td>{{ $investment->term_months }} months</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $investment->status === 'active' ? 'success' : ($investment->status === 'matured' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($investment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">${{ number_format($investment->total_interest_accrued, 2) }}</h5>
                                    <p class="card-text">Total Interest Accrued</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">${{ number_format($investment->total_payments, 2) }}</h5>
                                    <p class="card-text">Total Payments</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $investment->ledgerEntries->count() }}</h5>
                                    <p class="card-text">Total Transactions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $investment->isMatured() ? 'Yes' : 'No' }}</h5>
                                    <p class="card-text">Matured</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($investment->notes)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted">Notes</h6>
                                <div class="alert alert-light">
                                    {{ $investment->notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Quick Actions</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('ledger-entries.add-entry', ['investment_id' => $investment->id]) }}" class="btn btn-outline-primary">
                                    <i class="bx bx-plus me-1"></i> Add Ledger Entry
                                </a>
                                <a href="{{ route('ledger-entries.create-accrual', ['investment_id' => $investment->id]) }}" class="btn btn-outline-success">
                                    <i class="bx bx-calculator me-1"></i> Create Accrual
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="window.print()">
                                    <i class="bx bx-printer me-1"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Entries -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Ledger Entries</h5>
                    <a href="{{ route('ledger-entries.add-entry', ['investment_id' => $investment->id]) }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Add Entry
                    </a>
                </div>
                <div class="card-body">
                    @if($ledgerEntries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Interest</th>
                                        <th>Principal</th>
                                        <th>Balance After</th>
                                        <th>Description</th>
                                        <th>Created By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ledgerEntries as $entry)
                                        <tr>
                                            <td>{{ $entry->entry_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $entry->type === 'accrual' ? 'success' : ($entry->type === 'payment' ? 'danger' : 'info') }}">
                                                    {{ ucfirst($entry->type) }}
                                                </span>
                                            </td>
                                            <td>${{ number_format($entry->amount, 2) }}</td>
                                            <td>{{ $entry->interest_amount ? '$' . number_format($entry->interest_amount, 2) : '-' }}</td>
                                            <td>{{ $entry->principal_amount ? '$' . number_format($entry->principal_amount, 2) : '-' }}</td>
                                            <td class="fw-semibold">${{ number_format($entry->balance_after, 2) }}</td>
                                            <td>{{ $entry->description }}</td>
                                            <td>{{ $entry->createdBy->name }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('ledger-entries.show', $entry) }}">
                                                            <i class="bx bx-show me-1"></i> View
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('ledger-entries.edit', $entry) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        @if($entry->type !== 'principal')
                                                            <div class="dropdown-divider"></div>
                                                            <form action="{{ route('ledger-entries.destroy', $entry) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this entry?')">
                                                                    <i class="bx bx-trash me-1"></i> Delete
                                                                </button>
                                                            </form>
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
                        <div class="d-flex justify-content-center mt-4">
                            {{ $ledgerEntries->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-receipt display-4 text-muted"></i>
                            <h5 class="mt-3">No ledger entries found</h5>
                            <p class="text-muted">Start by adding the first ledger entry.</p>
                            <a href="{{ route('ledger-entries.add-entry', ['investment_id' => $investment->id]) }}" class="btn btn-primary">Add Entry</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
