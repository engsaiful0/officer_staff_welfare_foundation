@extends('layouts.contentNavbarLayout')

@section('title', 'Select Installment to Pay')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bx bx-list-ul me-2"></i>Select Installment to Pay
                    </h5>
                    <a href="{{ route('investments.view-investments') }}" class="btn btn-light btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to Investments
                    </a>
                </div>
                <div class="card-body">
                    <!-- Investment Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">Investment Information</h6>
                                    <table class="table table-borderless table-sm mb-0">
                                        <tr>
                                            <td class="text-muted" style="width: 40%;">Account #:</td>
                                            <td>
                                                <strong>
                                                    @if($investment->account && $investment->account->account_number)
                                                        {{ $investment->account->account_number }}
                                                    @else
                                                        #{{ $investment->id }}
                                                    @endif
                                                </strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Member:</td>
                                            <td><strong>{{ $investment->member->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Member ID:</td>
                                            <td>{{ $investment->member->unique_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Principal:</td>
                                            <td><strong>${{ number_format($investment->principal_amount, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Interest Rate:</td>
                                            <td>{{ number_format($investment->rate_percentage, 2) }}%</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="mb-3">Payment Summary</h6>
                                    <table class="table table-borderless table-sm mb-0 text-white">
                                        <tr>
                                            <td style="width: 50%;">Total Installments:</td>
                                            <td><strong>{{ $investment->installments->count() }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Pending Installments:</td>
                                            <td><strong>{{ $pendingInstallments->count() }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Paid Installments:</td>
                                            <td><strong>{{ $investment->installments->where('status', 'paid')->count() }}</strong></td>
                                        </tr>
                                        @if($investment->account)
                                        <tr>
                                            <td>Current Balance:</td>
                                            <td><strong>${{ number_format($investment->account->current_balance, 2) }}</strong></td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Installments -->
                    <div class="mb-4">
                        <h6 class="mb-3">
                            <i class="bx bx-calendar-check me-2"></i>Pending Installments
                            <span class="badge bg-warning">{{ $pendingInstallments->count() }}</span>
                        </h6>
                        
                        @if($pendingInstallments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Installment #</th>
                                            <th>Due Date</th>
                                            <th>Principal</th>
                                            <th>Interest (Rent)</th>
                                            <th>Fine (Estimated)</th>
                                            <th>Total Amount</th>
                                            <th>Days Late</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingInstallments as $installment)
                                            @php
                                                $daysLate = $installment->getDaysLate();
                                                $isOverdue = $installment->isOverdue();
                                                $estimatedFine = $isOverdue ? $installment->calculateFine() : 0;
                                                $totalAmount = $installment->principal_amount + $installment->rent + $estimatedFine;
                                            @endphp
                                            <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>#{{ $installment->installment_number }}</strong>
                                                </td>
                                                <td>
                                                    {{ $installment->schedule_date->format('M d, Y') }}
                                                    @if($isOverdue)
                                                        <br><small class="text-danger">Overdue</small>
                                                    @endif
                                                </td>
                                                <td>${{ number_format($installment->principal_amount, 2) }}</td>
                                                <td>${{ number_format($installment->rent, 2) }}</td>
                                                <td>
                                                    @if($estimatedFine > 0)
                                                        <span class="text-danger">${{ number_format($estimatedFine, 2) }}</span>
                                                    @else
                                                        $0.00
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong class="text-primary">${{ number_format($totalAmount, 2) }}</strong>
                                                </td>
                                                <td>
                                                    @if($daysLate > 0)
                                                        <span class="badge bg-danger">{{ $daysLate }} days</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($isOverdue)
                                                        <span class="badge bg-danger">Overdue</span>
                                                    @else
                                                        <span class="badge bg-warning">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('investments.payments.show', [$investment, $installment->id]) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bx bx-money me-1"></i> Pay Now
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                No pending installments found. All installments have been paid.
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('investments.payments.index', $investment) }}" class="btn btn-outline-secondary">
                            <i class="bx bx-list-ul me-1"></i> View All Payments
                        </a>
                        <a href="{{ route('investments.show', $investment) }}" class="btn btn-outline-info">
                            <i class="bx bx-show me-1"></i> View Investment Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



