@extends('layouts.contentNavbarLayout')

@section('title', 'Investments')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Investment Management</h5>
                            <p class="mb-4">Manage member investments, track interest accruals, and generate reports.</p>
                            <a href="{{ route('investments.add-investment') }}" class="btn btn-sm btn-outline-primary">Add New Investment</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('investments.view-investments') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="member_id" class="form-label">Member</label>
                                <select class="form-select" id="member_id" name="member_id">
                                    <option value="">All Members</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }} ({{ $member->member_unique_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="matured" {{ request('status') == 'matured' ? 'selected' : '' }}>Matured</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Investments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Investments</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('investments.import') }}" class="btn btn-outline-info btn-sm">
                            <i class="bx bx-import me-1"></i> Import
                        </a>
                        <a href="{{ route('investments.reports') }}" class="btn btn-outline-success btn-sm">
                            <i class="bx bx-bar-chart me-1"></i> Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($investments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Account #</th>
                                        <th>Member</th>
                                        <th>Product</th>
                                        <th>Principal</th>
                                        <th>Current Balance</th>
                                        <th>Installments</th>
                                        <th>Rate</th>
                                        <th>Start Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($investments as $investment)
                                        <tr>
                                            <td>
                                                @if($investment->account)
                                                    <span class="fw-semibold">{{ $investment->account->account_number ?: 'N/A' }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $investment->member->name }}</span>
                                                    <small class="text-muted">{{ $investment->member->unique_id }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $investment->product_name ?: 'N/A' }}</td>
                                            <td>৳{{ number_format($investment->principal_amount, 2) }}</td>
                                            <td>
                                                @if($investment->account)
                                                    ৳{{ number_format($investment->account->current_balance, 2) }}
                                                @else
                                                    ৳{{ number_format($investment->principal_amount, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($investment->account)
                                                    <small>
                                                        Paid: {{ $investment->account->installments_paid_count }} / 
                                                        Total: {{ $investment->account->installments_paid_count + $investment->account->installments_pending_count }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($investment->rate_percentage, 2) }}%</td>
                                            <td>{{ $investment->start_date->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $investment->status === 'active' ? 'success' : ($investment->status === 'matured' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($investment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('investments.show', $investment) }}">
                                                            <i class="bx bx-show me-1"></i> View
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('investments.edit', $investment) }}">
                                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('ledger-entries.add-entry', ['investment_id' => $investment->id]) }}">
                                                            <i class="bx bx-plus me-1"></i> Add Entry
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <form action="{{ route('investments.destroy', $investment) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this investment?')">
                                                                <i class="bx bx-trash me-1"></i> Delete
                                                            </button>
                                                        </form>
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
                            {{ $investments->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" alt="No investments" class="img-fluid" style="max-height: 200px;">
                            <h5 class="mt-3">No investments found</h5>
                            <p class="text-muted">Start by adding your first investment.</p>
                            <a href="{{ route('investments.add-investment') }}" class="btn btn-primary">Add Investment</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
