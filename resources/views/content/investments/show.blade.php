@extends('layouts.contentNavbarLayout')

@section('title', 'Investment Details')

@section('content')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        .print-break {
            page-break-after: always;
        }
        body {
            background: white;
        }
        .card {
            border: none;
            box-shadow: none;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Action Buttons (Hidden on Print) -->
    <div class="row mb-3 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Investments
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('investments.payments.pay-investment', $investment) }}" class="btn btn-success">
                        <i class="bx bx-money me-1"></i> Pay Investment
                    </a>
                    <a href="{{ route('investments.payments.index', $investment) }}" class="btn btn-outline-success">
                        <i class="bx bx-list-ul me-1"></i> View Payments
                    </a>
                    <a href="{{ route('investments.edit', $investment) }}" class="btn btn-outline-primary">
                        <i class="bx bx-edit me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bx bx-printer me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information (Printable) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Investment Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm table-bordered table-hover">
                                <tr>
                                    <td width="40%"><strong>Account Number:</strong></td>
                                    <td>
                                        @if($investment->account && $investment->account->account_number)
                                            <span class="fw-bold text-primary">{{ $investment->account->account_number }}</span>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Member Name:</strong></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $investment->member->name }}</span>
                                            <small class="text-muted">ID: {{ $investment->member->unique_id }}</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Investment Type:</strong></td>
                                    <td>{{ $investment->product_name ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Principal Amount:</strong></td>
                                    <td class="fw-bold">৳{{ number_format($investment->principal_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Interest Rate:</strong></td>
                                    <td>{{ number_format($investment->rate_percentage, 2) }}% per annum</td>
                                </tr>
                                <tr>
                                    <td><strong>Investment Period:</strong></td>
                                    <td>{{ $investment->investment_years ?? ($investment->term_months / 12) }} Years</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm table-bordered table-hover">
                                <tr>
                                    <td width="40%"><strong>Account Opening Date:</strong></td>
                                    <td>
                                        @if($investment->account)
                                            {{ $investment->account->account_opening_date->format('d M, Y') }}
                                        @else
                                            {{ $investment->start_date->format('d M, Y') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Current Balance:</strong></td>
                                    <td class="fw-bold text-success">
                                        @if($investment->account)
                                            ৳{{ number_format($investment->account->current_balance, 2) }}
                                        @else
                                            ৳{{ number_format($investment->principal_amount, 2) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Principal Paid:</strong></td>
                                    <td>
                                        @if($investment->account)
                                            ৳{{ number_format($investment->account->total_principal_paid, 2) }}
                                        @else
                                            ৳0.00
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Rent Received:</strong></td>
                                    <td class="text-success">
                                        @if($investment->account)
                                            ৳{{ number_format($investment->account->total_rent_received, 2) }}
                                        @else
                                            ৳0.00
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Installments Paid:</strong></td>
                                    <td>
                                        @if($investment->account)
                                            {{ $investment->account->installments_paid_count }} / 
                                            {{ $investment->account->installments_paid_count + $investment->account->installments_pending_count }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Account Status:</strong></td>
                                    <td>
                                        @if($investment->account)
                                            <span class="badge bg-{{ $investment->account->account_status === 'active' ? 'success' : ($investment->account->account_status === 'matured' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($investment->account->account_status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Schedule (Printable) -->
    @if($investment->installments && $investment->installments->count() > 0)
    <div class="row print-break">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Schedule Date</th>
                                    <th>Beginning Balance</th>
                                    <th>Principal</th>
                                    <th>Rent</th>
                                    <th>Total Amount</th>
                                    <th>Ending Balance</th>
                                    <th>Cumulative Rent</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($investment->installments as $installment)
                                <tr>
                                    <td>{{ $installment->installment_number }}</td>
                                    <td>{{ $installment->schedule_date->format('d M, Y') }}</td>
                                    <td>৳{{ number_format($installment->beginning_balance, 2) }}</td>
                                    <td>৳{{ number_format($installment->principal_amount, 2) }}</td>
                                    <td>৳{{ number_format($installment->rent, 2) }}</td>
                                    <td class="fw-bold">৳{{ number_format($installment->total_amount, 2) }}</td>
                                    <td>৳{{ number_format($installment->ending_balance, 2) }}</td>
                                    <td>৳{{ number_format($installment->cumulative_rent, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $installment->status === 'paid' ? 'success' : ($installment->status === 'overdue' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($installment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">Total:</th>
                                    <th>৳{{ number_format($investment->principal_amount, 2) }}</th>
                                    <th>৳{{ number_format($investment->installments->sum('principal_amount'), 2) }}</th>
                                    <th>৳{{ number_format($investment->installments->sum('rent'), 2) }}</th>
                                    <th>৳{{ number_format($investment->installments->sum('total_amount'), 2) }}</th>
                                    <th>-</th>
                                    <th>৳{{ number_format($investment->installments->max('cumulative_rent'), 2) }}</th>
                                    <th>-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Notes (Hidden on Print) -->
    @if($investment->notes)
    <div class="row mb-4 no-print">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    <p>{{ $investment->notes }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
