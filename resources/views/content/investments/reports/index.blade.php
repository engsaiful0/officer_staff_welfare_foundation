@extends('layouts.contentNavbarLayout')

@section('title', 'Investment Reports')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Investment Reports</h5>
                            <p class="mb-4">Generate comprehensive reports for investment portfolio, interest accruals, and maturity tracking.</p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" height="140" alt="Reports">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $stats['total_investments'] }}</h3>
                    <p class="card-text">Total Investments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $stats['active_investments'] }}</h3>
                    <p class="card-text">Active Investments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="card-title">${{ number_format($stats['total_current_balance'], 2) }}</h3>
                    <p class="card-text">Total Current Balance</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="card-title">{{ $stats['overdue_investments'] }}</h3>
                    <p class="card-text">Overdue Investments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Types -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-pie-chart-alt-2"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Portfolio Report</h5>
                    <p class="card-text">Comprehensive overview of all investments with current balances and performance metrics.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('investments.reports.portfolio') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                        <a href="{{ route('investments.reports.export-pdf', ['report_type' => 'portfolio']) }}" class="btn btn-outline-success btn-sm">PDF</a>
                        <a href="{{ route('investments.reports.export-excel', ['report_type' => 'portfolio']) }}" class="btn btn-outline-info btn-sm">Excel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="bx bx-trending-up"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Interest Report</h5>
                    <p class="card-text">Detailed breakdown of interest accruals, payments, and net interest calculations.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('investments.reports.interest') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                        <a href="{{ route('investments.reports.export-pdf', ['report_type' => 'interest']) }}" class="btn btn-outline-success btn-sm">PDF</a>
                        <a href="{{ route('investments.reports.export-excel', ['report_type' => 'interest']) }}" class="btn btn-outline-info btn-sm">Excel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="bx bx-time-five"></i>
                        </span>
                    </div>
                    <h5 class="card-title">Maturity Report</h5>
                    <p class="card-text">Track investments approaching maturity, overdue investments, and renewal opportunities.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('investments.reports.maturity') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                        <a href="{{ route('investments.reports.export-pdf', ['report_type' => 'maturity']) }}" class="btn btn-outline-success btn-sm">PDF</a>
                        <a href="{{ route('investments.reports.export-excel', ['report_type' => 'maturity']) }}" class="btn btn-outline-info btn-sm">Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Investment Status Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-success">{{ $stats['active_investments'] }}</h4>
                                <p class="text-muted mb-0">Active</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-warning">{{ $stats['matured_investments'] }}</h4>
                                <p class="text-muted mb-0">Matured</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-danger">{{ $stats['overdue_investments'] }}</h4>
                            <p class="text-muted mb-0">Overdue</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Financial Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">${{ number_format($stats['total_principal'], 2) }}</h4>
                                <p class="text-muted mb-0">Total Principal</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">${{ number_format($stats['total_interest_accrued'], 2) }}</h4>
                            <p class="text-muted mb-0">Interest Accrued</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-primary">
                            <i class="bx bx-list-ul me-1"></i> View All Investments
                        </a>
                        <a href="{{ route('investments.add-investment') }}" class="btn btn-outline-success">
                            <i class="bx bx-plus me-1"></i> Add New Investment
                        </a>
                        <a href="{{ route('investments.import') }}" class="btn btn-outline-info">
                            <i class="bx bx-import me-1"></i> Import Data
                        </a>
                        <a href="{{ route('ledger-entries.view-entries') }}" class="btn btn-outline-warning">
                            <i class="bx bx-receipt me-1"></i> View Ledger Entries
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
