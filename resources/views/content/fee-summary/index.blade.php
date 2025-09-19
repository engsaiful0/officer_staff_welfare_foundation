@extends('layouts/layoutMaster')

@section('title', 'Fee Summary Report')

@section('page-script')
    <script src="{{ asset('assets/js/fee-summary.js') }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Fee Summary Report</h5>
        <div class="card-actions">
            <button type="button" class="btn btn-success btn-sm" onclick="exportAllStudents('excel')">
                <i class="ti ti-file-excel me-1"></i>Export Excel
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="exportAllStudents('pdf')">
                <i class="ti ti-file-pdf me-1"></i>Export PDF
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter Section -->
        <form method="GET" action="{{ route('fee-summary.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-select select2">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" {{ $selectedAcademicYear == $academicYear->id ? 'selected' : '' }}>
                                {{ $academicYear->academic_year_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('fee-summary.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Students</h6>
                        <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-title">Complete Fees</h6>
                        <h3 class="mb-0">{{ $stats['students_with_complete_fees'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-title">Partial Fees</h6>
                        <h3 class="mb-0">{{ $stats['students_with_partial_fees'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-title">Collection %</h6>
                        <h3 class="mb-0">{{ $stats['collection_percentage'] }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Summary Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Technology</th>
                        <th>Semester Fees</th>
                        <th>Monthly Fees</th>
                        <th>Total Paid</th>
                        <th>Total Due</th>
                        <th>Completion %</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeSummaries as $index => $summary)
                        @php
                            $student = $summary->student;
                            $completionPercentage = $summary->total_fees > 0 ? 
                                round(($summary->total_paid / $summary->total_fees) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->student_unique_id ?? 'N/A' }}</td>
                            <td>{{ $student->full_name_in_english_block_letter }}</td>
                            <td>{{ $student->technology->technology_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $summary->semesters_completed }}/8</span>
                                <br>
                                <small class="text-muted">{{ number_format($summary->paid_semester_fees, 2) }}/{{ number_format($summary->total_semester_fees, 2) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $summary->months_completed }}/48</span>
                                <br>
                                <small class="text-muted">{{ number_format($summary->paid_monthly_fees, 2) }}/{{ number_format($summary->total_monthly_fees, 2) }}</small>
                            </td>
                            <td class="text-success">{{ number_format($summary->total_paid, 2) }}</td>
                            <td class="text-danger">{{ number_format($summary->total_due, 2) }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $completionPercentage }}%"
                                         aria-valuenow="{{ $completionPercentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $completionPercentage }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($summary->all_fees_paid)
                                    <span class="badge bg-success">Complete</span>
                                @elseif($summary->total_paid > 0)
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">No Payment</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-inline-block">
                                    <a href="{{ route('fee-summary.student-report', $student->id) }}?academic_year_id={{ $selectedAcademicYear }}" 
                                       class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="View Report">
                                        <i class="ti ti-eye ti-md"></i>
                                    </a>
                                    <a href="{{ route('fee-summary.print-student-report', $student->id) }}?academic_year_id={{ $selectedAcademicYear }}" 
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon" title="Print Report" target="_blank">
                                        <i class="ti ti-printer ti-md"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-download ti-md"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('fee-summary.export-student-excel', $student->id) }}?academic_year_id={{ $selectedAcademicYear }}">
                                                    <i class="ti ti-file-excel me-2"></i>Export Excel
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('fee-summary.export-student-pdf', $student->id) }}?academic_year_id={{ $selectedAcademicYear }}">
                                                    <i class="ti ti-file-pdf me-2"></i>Export PDF
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No fee data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function exportAllStudents(format) {
    const academicYearId = document.getElementById('academic_year_id').value;
    let url = '';
    
    if (format === 'excel') {
        url = '{{ route("fee-summary.export-all-excel") }}';
    } else if (format === 'pdf') {
        url = '{{ route("fee-summary.export-all-pdf") }}';
    }
    
    if (academicYearId) {
        url += '?academic_year_id=' + academicYearId;
    }
    
    window.open(url, '_blank');
}
</script>
@endsection
