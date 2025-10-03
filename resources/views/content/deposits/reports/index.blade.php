@extends('layouts/contentNavbarLayout')

@section('title', 'Deposit Reports')

@section('content')
<div class="row">
  <!-- Statistics Cards -->
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-muted mb-1">Total Deposits</h6>
            <h4 class="mb-0">{{ number_format($stats['total_deposits']) }}</h4>
          </div>
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="bx bx-wallet"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-muted mb-1">Active Deposits</h6>
            <h4 class="mb-0">{{ number_format($stats['active_deposits']) }}</h4>
          </div>
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-success">
              <i class="bx bx-check-circle"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-muted mb-1">Total Balance</h6>
            <h4 class="mb-0">৳{{ number_format($stats['total_current_balance'], 2) }}</h4>
          </div>
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-info">
              <i class="bx bx-dollar"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="card-title text-muted mb-1">Interest Accrued</h6>
            <h4 class="mb-0">৳{{ number_format($stats['total_interest_accrued'], 2) }}</h4>
          </div>
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="bx bx-trending-up"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Report Options -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Available Reports</h5>
      </div>
      
      <div class="card-body">
        <div class="row">
          <!-- Portfolio Report -->
          <div class="col-md-4 mb-4">
            <div class="card h-100 border">
              <div class="card-body text-center">
                <div class="avatar mx-auto mb-3">
                  <span class="avatar-initial rounded bg-label-primary">
                    <i class="bx bx-pie-chart-alt-2"></i>
                  </span>
                </div>
                <h5 class="card-title">Portfolio Report</h5>
                <p class="card-text text-muted">Comprehensive overview of all deposits with current balances and status.</p>
                <a href="{{ route('deposits.reports.portfolio') }}" class="btn btn-primary">
                  <i class="bx bx-show me-1"></i> View Report
                </a>
              </div>
            </div>
          </div>
          
          <!-- Interest Report -->
          <div class="col-md-4 mb-4">
            <div class="card h-100 border">
              <div class="card-body text-center">
                <div class="avatar mx-auto mb-3">
                  <span class="avatar-initial rounded bg-label-success">
                    <i class="bx bx-trending-up"></i>
                  </span>
                </div>
                <h5 class="card-title">Interest Report</h5>
                <p class="card-text text-muted">Detailed tracking of interest accruals and payments across all deposits.</p>
                <a href="{{ route('deposits.reports.interest') }}" class="btn btn-success">
                  <i class="bx bx-show me-1"></i> View Report
                </a>
              </div>
            </div>
          </div>
          
          <!-- Maturity Report -->
          <div class="col-md-4 mb-4">
            <div class="card h-100 border">
              <div class="card-body text-center">
                <div class="avatar mx-auto mb-3">
                  <span class="avatar-initial rounded bg-label-warning">
                    <i class="bx bx-time"></i>
                  </span>
                </div>
                <h5 class="card-title">Maturity Report</h5>
                <p class="card-text text-muted">Track deposits approaching or past maturity dates with alerts.</p>
                <a href="{{ route('deposits.reports.maturity') }}" class="btn btn-warning">
                  <i class="bx bx-show me-1"></i> View Report
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Additional Statistics -->
<div class="row mt-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Deposit Status Distribution</h5>
      </div>
      
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Active Deposits</span>
          <div class="d-flex align-items-center">
            <div class="progress me-2" style="width: 100px; height: 8px;">
              <div class="progress-bar bg-success" style="width: {{ $stats['total_deposits'] > 0 ? ($stats['active_deposits'] / $stats['total_deposits']) * 100 : 0 }}%"></div>
            </div>
            <span class="fw-semibold">{{ $stats['active_deposits'] }}</span>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Matured Deposits</span>
          <div class="d-flex align-items-center">
            <div class="progress me-2" style="width: 100px; height: 8px;">
              <div class="progress-bar bg-warning" style="width: {{ $stats['total_deposits'] > 0 ? ($stats['matured_deposits'] / $stats['total_deposits']) * 100 : 0 }}%"></div>
            </div>
            <span class="fw-semibold">{{ $stats['matured_deposits'] }}</span>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
          <span>Overdue Deposits</span>
          <div class="d-flex align-items-center">
            <div class="progress me-2" style="width: 100px; height: 8px;">
              <div class="progress-bar bg-danger" style="width: {{ $stats['total_deposits'] > 0 ? ($stats['overdue_deposits'] / $stats['total_deposits']) * 100 : 0 }}%"></div>
            </div>
            <span class="fw-semibold">{{ $stats['overdue_deposits'] }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Financial Summary</h5>
      </div>
      
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Total Deposit Amount</span>
          <span class="fw-semibold">৳{{ number_format($stats['total_deposit_amount'], 2) }}</span>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Current Total Balance</span>
          <span class="fw-semibold text-success">৳{{ number_format($stats['total_current_balance'], 2) }}</span>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span>Total Interest Accrued</span>
          <span class="fw-semibold text-info">৳{{ number_format($stats['total_interest_accrued'], 2) }}</span>
        </div>
        
        <hr>
        
        <div class="d-flex justify-content-between align-items-center">
          <span>Net Growth</span>
          <span class="fw-semibold {{ ($stats['total_current_balance'] - $stats['total_deposit_amount']) >= 0 ? 'text-success' : 'text-danger' }}">
            ৳{{ number_format($stats['total_current_balance'] - $stats['total_deposit_amount'], 2) }}
          </span>
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
        <h5 class="mb-0">Quick Actions</h5>
      </div>
      
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('deposits.reports.export-excel') }}" class="btn btn-outline-success">
            <i class="bx bx-download me-1"></i> Export Excel
          </a>
          
          <a href="{{ route('deposits.reports.export-pdf') }}" class="btn btn-outline-danger">
            <i class="bx bx-file me-1"></i> Export PDF
          </a>
          
          <a href="{{ route('deposits.import') }}" class="btn btn-outline-info">
            <i class="bx bx-import me-1"></i> Import Data
          </a>
          
          <a href="{{ route('deposits.add-deposit') }}" class="btn btn-outline-primary">
            <i class="bx bx-plus me-1"></i> Add Deposit
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
