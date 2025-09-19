@extends('layouts/layoutMaster')

@section('title', 'Polytechnic Analytics Dashboard')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/chartjs/chartjs.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
@endsection

@section('page-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/cards-advance.css') }}">
  <style>
    .timeline {
      position: relative;
      padding-left: 30px;
    }
    
    .timeline-item {
      position: relative;
      margin-bottom: 20px;
    }
    
    .timeline-item:not(:last-child)::before {
      content: '';
      position: absolute;
      left: -20px;
      top: 20px;
      width: 2px;
      height: calc(100% + 10px);
      background: #e7e7e7;
    }
    
    .timeline-marker {
      position: absolute;
      left: -25px;
      top: 0;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 8px;
      color: white;
    }
    
    .timeline-marker-success {
      background-color: #28a745;
    }
    
    .timeline-marker-primary {
      background-color: #007bff;
    }
    
    .timeline-marker-warning {
      background-color: #ffc107;
    }
    
    .timeline-content {
      padding-left: 10px;
    }
  </style>
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
  <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
@endsection

@section('content')

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
  <!-- Total Students -->
  <div class="col-xl-3 col-sm-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Total Students</h5>
            <small class="text-muted">All Departments</small>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-label-primary rounded">
              <i class="ti ti-users ti-md"></i>
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <h3 class="mb-0 me-2">{{ number_format($studentStats['total']) }}</h3>
          <small class="text-success fw-medium">
            <i class="ti ti-arrow-up"></i>
            +{{ $studentStats['this_month'] }} this month
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Teachers -->
  <div class="col-xl-3 col-sm-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Total Teachers</h5>
            <small class="text-muted">Faculty Members</small>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-label-info rounded">
              <i class="ti ti-school ti-md"></i>
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <h3 class="mb-0 me-2">{{ number_format($teacherStats['total']) }}</h3>
          <small class="text-success fw-medium">
            <i class="ti ti-arrow-up"></i>
            +{{ $teacherStats['this_month'] }} this month
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Employees -->
  <div class="col-xl-3 col-sm-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Total Employees</h5>
            <small class="text-muted">Staff Members</small>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-label-warning rounded">
              <i class="ti ti-user-check ti-md"></i>
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <h3 class="mb-0 me-2">{{ number_format($employeeStats['total']) }}</h3>
          <small class="text-success fw-medium">
            <i class="ti ti-arrow-up"></i>
            +{{ $employeeStats['this_month'] }} this month
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Net Profit -->
  <div class="col-xl-3 col-sm-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-title mb-0">
            <h5 class="mb-1">Net Profit</h5>
            <small class="text-muted">This Month</small>
          </div>
          <div class="avatar">
            <div class="avatar-initial bg-label-success rounded">
              <i class="ti ti-currency-dollar ti-md"></i>
            </div>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <h3 class="mb-0 me-2">৳{{ number_format($feeCollection['this_month'] - $expenseAnalytics['this_month']) }}</h3>
          <small class="text-success fw-medium">
            <i class="ti ti-arrow-up"></i>
            {{ $feeGrowth > 0 ? '+' : '' }}{{ number_format($feeGrowth, 1) }}%
          </small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Fee Collection & Expense Analytics -->
<div class="row g-4 mb-4">
  <!-- Fee Collection -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Fee Collection Analytics</h5>
        <small class="text-muted">Revenue Overview</small>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-primary rounded">
                  <i class="ti ti-calendar-event ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">Today</h6>
                <small class="text-muted">৳{{ number_format($feeCollection['today']) }}</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-info rounded">
                  <i class="ti ti-calendar-week ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">This Week</h6>
                <small class="text-muted">৳{{ number_format($feeCollection['this_week']) }}</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-success rounded">
                  <i class="ti ti-calendar-month ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">This Month</h6>
                <small class="text-muted">৳{{ number_format($feeCollection['this_month']) }}</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-warning rounded">
                  <i class="ti ti-trending-up ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">Growth</h6>
                <small class="text-{{ $feeGrowth >= 0 ? 'success' : 'danger' }}">
                  {{ $feeGrowth >= 0 ? '+' : '' }}{{ number_format($feeGrowth, 1) }}%
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Expense Analytics -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Expense Analytics</h5>
        <small class="text-muted">Cost Overview</small>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-danger rounded">
                  <i class="ti ti-calendar-event ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">Today</h6>
                <small class="text-muted">৳{{ number_format($expenseAnalytics['today']) }}</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-warning rounded">
                  <i class="ti ti-calendar-week ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">This Week</h6>
                <small class="text-muted">৳{{ number_format($expenseAnalytics['this_week']) }}</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-secondary rounded">
                  <i class="ti ti-calendar-month ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">This Month</h6>
                <small class="text-muted">৳{{ number_format($expenseAnalytics['this_month']) }}</small>
              </div>
            </div>
          </div>
          <div class="col-6">
            <div class="d-flex align-items-center">
              <div class="avatar flex-shrink-0 me-3">
                <div class="avatar-initial bg-label-info rounded">
                  <i class="ti ti-trending-down ti-sm"></i>
                </div>
              </div>
              <div>
                <h6 class="mb-0">Growth</h6>
                <small class="text-{{ $expenseGrowth >= 0 ? 'danger' : 'success' }}">
                  {{ $expenseGrowth >= 0 ? '+' : '' }}{{ number_format($expenseGrowth, 1) }}%
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
  <!-- Department-wise Student Distribution -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Department-wise Student Distribution</h5>
        <small class="text-muted">Students by Technology/Department</small>
      </div>
      <div class="card-body">
        <canvas id="departmentChart" height="300"></canvas>
      </div>
    </div>
  </div>

  <!-- Monthly Fee Collection Trend -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Monthly Fee Collection Trend</h5>
        <small class="text-muted">Last 6 Months</small>
      </div>
      <div class="card-body">
        <canvas id="feeTrendChart" height="300"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Additional Charts Row -->
<div class="row g-4 mb-4">
  <!-- Monthly Expense Trend -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Monthly Expense Trend</h5>
        <small class="text-muted">Last 6 Months</small>
      </div>
      <div class="card-body">
        <canvas id="expenseTrendChart" height="300"></canvas>
      </div>
    </div>
  </div>

  <!-- Expense by Category -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Expense by Category</h5>
        <small class="text-muted">Expense Distribution</small>
      </div>
      <div class="card-body">
        <canvas id="expenseCategoryChart" height="300"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activities -->
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Recent Activities</h5>
        <small class="text-muted">Latest System Activities</small>
      </div>
      <div class="card-body">
        <div class="timeline">
          @forelse($recentActivities as $activity)
          <div class="timeline-item">
            <div class="timeline-marker timeline-marker-{{ $activity['color'] }}">
              <i class="{{ $activity['icon'] }}"></i>
            </div>
            <div class="timeline-content">
              <h6 class="mb-1">{{ $activity['message'] }}</h6>
              <small class="text-muted">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</small>
            </div>
          </div>
          @empty
          <div class="text-center py-4">
            <p class="text-muted">No recent activities</p>
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-0">Quick Stats</h5>
        <small class="text-muted">Key Metrics</small>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h6 class="mb-0">Total Revenue</h6>
            <small class="text-muted">All Time</small>
          </div>
          <h4 class="mb-0 text-success">৳{{ number_format($feeCollection['this_month']) }}</h4>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h6 class="mb-0">Total Expenses</h6>
            <small class="text-muted">All Time</small>
          </div>
          <h4 class="mb-0 text-danger">৳{{ number_format($expenseAnalytics['this_month']) }}</h4>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h6 class="mb-0">Net Profit</h6>
            <small class="text-muted">This Month</small>
          </div>
          <h4 class="mb-0 text-primary">৳{{ number_format($feeCollection['this_month'] - $expenseAnalytics['this_month']) }}</h4>
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-0">Profit Margin</h6>
            <small class="text-muted">This Month</small>
          </div>
          <h4 class="mb-0 text-info">
            {{ $feeCollection['this_month'] > 0 ? number_format((($feeCollection['this_month'] - $expenseAnalytics['this_month']) / $feeCollection['this_month']) * 100, 1) : 0 }}%
          </h4>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// Department Chart Data
const departmentData = @json($departmentWiseStudents);
const departmentLabels = departmentData.map(item => item.department);
const departmentCounts = departmentData.map(item => item.count);

// Fee Trend Data
const feeTrendData = @json($monthlyFeeTrend);
const feeLabels = feeTrendData.map(item => item.month);
const feeAmounts = feeTrendData.map(item => item.amount);

// Expense Trend Data
const expenseTrendData = @json($monthlyExpenseTrend);
const expenseLabels = expenseTrendData.map(item => item.month);
const expenseAmounts = expenseTrendData.map(item => item.amount);

// Expense Category Data
const expenseCategoryData = @json($expenseByCategory);
const categoryLabels = expenseCategoryData.map(item => item.category);
const categoryAmounts = expenseCategoryData.map(item => item.amount);

// Department Chart
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
new Chart(departmentCtx, {
  type: 'doughnut',
  data: {
    labels: departmentLabels,
    datasets: [{
      data: departmentCounts,
      backgroundColor: [
        '#FF6384',
        '#36A2EB',
        '#FFCE56',
        '#4BC0C0',
        '#9966FF',
        '#FF9F40'
      ]
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom'
      }
    }
  }
});

// Fee Trend Chart
const feeTrendCtx = document.getElementById('feeTrendChart').getContext('2d');
new Chart(feeTrendCtx, {
  type: 'line',
  data: {
    labels: feeLabels,
    datasets: [{
      label: 'Fee Collection',
      data: feeAmounts,
      borderColor: '#36A2EB',
      backgroundColor: 'rgba(54, 162, 235, 0.1)',
      tension: 0.4,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return '৳' + value.toLocaleString();
          }
        }
      }
    }
  }
});

// Expense Trend Chart
const expenseTrendCtx = document.getElementById('expenseTrendChart').getContext('2d');
new Chart(expenseTrendCtx, {
  type: 'line',
  data: {
    labels: expenseLabels,
    datasets: [{
      label: 'Expenses',
      data: expenseAmounts,
      borderColor: '#FF6384',
      backgroundColor: 'rgba(255, 99, 132, 0.1)',
      tension: 0.4,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return '৳' + value.toLocaleString();
          }
        }
      }
    }
  }
});

// Expense Category Chart
const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
new Chart(expenseCategoryCtx, {
  type: 'bar',
  data: {
    labels: categoryLabels,
    datasets: [{
      label: 'Amount',
      data: categoryAmounts,
      backgroundColor: [
        '#FF6384',
        '#36A2EB',
        '#FFCE56',
        '#4BC0C0',
        '#9966FF',
        '#FF9F40'
      ]
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return '৳' + value.toLocaleString();
          }
        }
      }
    }
  }
});
</script>
@endpush