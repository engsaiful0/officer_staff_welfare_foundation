@extends('layouts.contentNavbarLayout')

@section('title', 'Investment Payments')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Payment Schedule - Investment #{{ $investment->id }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('investments.show', $investment) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back to Investment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Investment Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Investment Information</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Member:</strong></td>
                                    <td>{{ $investment->member->name }} ({{ $investment->member->member_unique_id }})</td>
                                </tr>
                                <tr>
                                    <td><strong>Principal Amount:</strong></td>
                                    <td>${{ number_format($investment->principal_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Interest Rate:</strong></td>
                                    <td>{{ number_format($investment->rate_percentage, 2) }}%</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Payment Summary</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Total Installments:</strong></td>
                                    <td>{{ $installments->count() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid:</strong></td>
                                    <td><span class="badge bg-success">{{ $installments->where('status', 'paid')->count() }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Pending:</strong></td>
                                    <td><span class="badge bg-warning">{{ $installments->where('status', 'pending')->count() }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Overdue:</strong></td>
                                    <td><span class="badge bg-danger">{{ $installments->where('status', 'overdue')->count() }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Installments Table -->
                    @if($installments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Schedule Date</th>
                                        <th>Principal</th>
                                        <th>Rent</th>
                                        <th>Fine</th>
                                        <th>Discount</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Paid Date</th>
                                        <th>Days Late</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($installments as $installment)
                                        @php
                                            $daysLate = $installment->getDaysLate();
                                            $isOverdue = $installment->isOverdue();
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                            <td>{{ $installment->installment_number }}</td>
                                            <td>{{ $installment->schedule_date->format('Y-m-d') }}</td>
                                            <td>৳{{ number_format($installment->principal_amount, 2) }}</td>
                                            <td>৳{{ number_format($installment->rent, 2) }}</td>
                                            <td>
                                                @if($installment->status === 'paid' && $installment->fine_amount > 0)
                                                    <span class="text-danger">${{ number_format($installment->fine_amount, 2) }}</span>
                                                @elseif($isOverdue)
                                                    <span class="text-warning">${{ number_format($installment->calculateFine(), 2) }}</span>
                                                @else
                                                    ৳0.00
                                                @endif
                                            </td>
                                            <td>
                                                @if($installment->status === 'paid' && isset($installment->discount_amount) && $installment->discount_amount > 0)
                                                    <span class="text-success">${{ number_format($installment->discount_amount, 2) }}</span>
                                                @else
                                                    ৳0.00
                                                @endif
                                            </td>
                                            <td>
                                                @if($installment->status === 'paid')
                                                    ৳{{ number_format($installment->total_amount - ($installment->discount_amount ?? 0), 2) }}
                                                @else
                                                    ৳{{ number_format($installment->principal_amount + $installment->rent + ($isOverdue ? $installment->calculateFine() : 0), 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($installment->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($isOverdue)
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $installment->paid_date ? $installment->paid_date->format('Y-m-d') : '-' }}
                                            </td>
                                            <td>
                                                @if($daysLate > 0)
                                                    <span class="text-danger">{{ $daysLate }} days</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($installment->status !== 'paid')
                                                    <a href="{{ route('investments.payments.show', [$investment, $installment->id]) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bx bx-money me-1"></i> Pay
                                                    </a>
                                                @else
                                                    <span class="text-muted">Paid</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-receipt display-4 text-muted"></i>
                            <h5 class="mt-3">No installments found</h5>
                            <p class="text-muted">Installments will be generated when the investment is created.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

