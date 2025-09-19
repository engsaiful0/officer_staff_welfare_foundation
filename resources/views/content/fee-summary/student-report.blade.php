@extends('layouts/layoutMaster')

@section('title', 'Student Fee Report')

@section('page-script')
    <script src="{{ asset('assets/js/fee-summary.js') }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Student Fee Report</h5>
        <div class="card-actions">
            <a href="{{ route('fee-summary.print-student-report', $report['student']->id) }}?academic_year_id={{ $report['fee_summary']->academic_year_id }}" 
               class="btn btn-primary btn-sm" target="_blank">
                <i class="ti ti-printer me-1"></i>Print Report
            </a>
            <div class="dropdown">
                <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('fee-summary.export-student-excel', $report['student']->id) }}?academic_year_id={{ $report['fee_summary']->academic_year_id }}">
                            <i class="ti ti-file-excel me-2"></i>Export Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('fee-summary.export-student-pdf', $report['student']->id) }}?academic_year_id={{ $report['fee_summary']->academic_year_id }}">
                            <i class="ti ti-file-pdf me-2"></i>Export PDF
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Student Information -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title">Student Information</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Student ID:</strong> {{ $report['student']->student_unique_id ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Name:</strong> {{ $report['student']->full_name_in_english_block_letter }}
                            </div>
                            <div class="col-md-3">
                                <strong>Father's Name:</strong> {{ $report['student']->father_name_in_english_block_letter }}
                            </div>
                            <div class="col-md-3">
                                <strong>Technology:</strong> {{ $report['student']->technology->technology_name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <strong>Academic Year:</strong> {{ $report['student']->academicYear->academic_year_name ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Phone:</strong> {{ $report['student']->personal_number ?? 'N/A' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Email:</strong> {{ $report['student']->email ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Summary Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Expected</h6>
                        <h3 class="mb-0">{{ number_format($report['fee_summary']->total_fees, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Paid</h6>
                        <h3 class="mb-0">{{ number_format($report['fee_summary']->total_paid, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Due</h6>
                        <h3 class="mb-0">{{ number_format($report['fee_summary']->total_due, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6 class="card-title">Completion</h6>
                        <h3 class="mb-0">{{ $report['fee_summary']->completion_percentage }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Fee Breakdown -->
        <div class="row">
            <!-- Semester Fees -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Semester Fees ({{ $report['fee_summary']->semesters_completed }}/8)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Semester</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['semester_fees'] as $semesterFee)
                                        <tr>
                                            <td>{{ $semesterFee->semester->semester_name ?? 'N/A' }}</td>
                                            <td>{{ number_format($semesterFee->amount, 2) }}</td>
                                            <td>{{ $semesterFee->payment_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($semesterFee->is_paid)
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-danger">Unpaid</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <strong>Total Semester Fees:</strong> 
                            {{ number_format($report['fee_summary']->paid_semester_fees, 2) }} / 
                            {{ number_format($report['fee_summary']->total_semester_fees, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Fees -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Monthly Fees ({{ $report['fee_summary']->months_completed }}/48)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['monthly_fees'] as $monthlyFee)
                                        <tr>
                                            <td>{{ $monthlyFee->month->month_name ?? 'N/A' }}</td>
                                            <td>{{ number_format($monthlyFee->amount, 2) }}</td>
                                            <td>{{ $monthlyFee->payment_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($monthlyFee->is_paid)
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-danger">Unpaid</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <strong>Total Monthly Fees:</strong> 
                            {{ number_format($report['fee_summary']->paid_monthly_fees, 2) }} / 
                            {{ number_format($report['fee_summary']->total_monthly_fees, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Collections History -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">Fee Collection History</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Collected By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['fee_collections'] as $index => $collection)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $collection->date }}</td>
                                            <td>{{ number_format($collection->total_amount, 2) }}</td>
                                            <td>{{ $collection->paymentMethod->payment_method_name ?? 'N/A' }}</td>
                                            <td>{{ $collection->user->name ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('app-collect-fee.receipt', $collection->id) }}" 
                                                   class="btn btn-sm btn-text-primary" target="_blank">
                                                    <i class="ti ti-eye"></i> Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
