@extends('layouts.contentNavbarLayout')

@section('title', 'Payment Investment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bx bx-money me-2"></i>Payment Investment
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <form method="GET" action="{{ route('investments.payments.payment-investment') }}">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="member_id" class="form-label">Member</label>
                                                <select class="form-select" id="member_id" name="member_id">
                                                    <option value="">All Members</option>
                                                    @foreach($members as $member)
                                                        <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                                            {{ $member->name }} ({{ $member->unique_id ?? 'N/A' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="status" class="form-label">Investment Status</label>
                                                <select class="form-select" id="status" name="status">
                                                    <option value="">All Status</option>
                                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="matured" {{ request('status') == 'matured' ? 'selected' : '' }}>Matured</option>
                                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">Filter</button>
                                                    <a href="{{ route('investments.payments.payment-investment') }}" class="btn btn-outline-secondary">Clear</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Investments with Pending Payments -->
                    @if($investments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Account #</th>
                                        <th>Member</th>
                                        <th>Product</th>
                                        <th>Principal</th>
                                        <th>Current Balance</th>
                                        <th>Pending Installments</th>
                                        <th>Overdue</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($investments as $investment)
                                        <tr>
                                            <td>
                                                @if($investment->account && $investment->account->account_number)
                                                    <span class="fw-semibold">{{ $investment->account->account_number }}</span>
                                                @else
                                                    <span class="text-muted">#{{ $investment->id }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $investment->member->name }}</span>
                                                    <small class="text-muted">{{ $investment->member->unique_id ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $investment->product_name ?: 'N/A' }}</td>
                                            <td>${{ number_format($investment->principal_amount, 2) }}</td>
                                            <td>
                                                @if($investment->account)
                                                    ${{ number_format($investment->account->current_balance, 2) }}
                                                @else
                                                    ${{ number_format($investment->principal_amount, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $investment->pending_count ?? 0 }} pending</span>
                                            </td>
                                            <td>
                                                @if(isset($investment->overdue_count) && $investment->overdue_count > 0)
                                                    <span class="badge bg-danger">{{ $investment->overdue_count }} overdue</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $investment->status === 'active' ? 'success' : ($investment->status === 'matured' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($investment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('investments.payments.pay-investment', $investment) }}" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="bx bx-money me-1"></i> Pay Investment
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $investments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-check-circle display-1 text-success mb-3"></i>
                            <h5 class="mt-3">No Investments with Pending Payments</h5>
                            <p class="text-muted">All investment installments have been paid.</p>
                            <a href="{{ route('investments.view-investments') }}" class="btn btn-primary mt-3">
                                <i class="bx bx-arrow-back me-1"></i> View All Investments
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



