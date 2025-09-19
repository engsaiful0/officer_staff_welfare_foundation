<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Students Fee Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            color: #666;
        }
        .summary-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .summary-section h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 16px;
        }
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-card h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
        }
        .stat-card .number {
            font-size: 18px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-complete {
            color: #28a745;
            font-weight: bold;
        }
        .status-partial {
            color: #ffc107;
            font-weight: bold;
        }
        .status-none {
            color: #dc3545;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .progress-bar {
            background-color: #e9ecef;
            height: 15px;
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-fill {
            background-color: #007bff;
            height: 100%;
            text-align: center;
            color: white;
            font-size: 8px;
            line-height: 15px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>All Students Fee Summary Report</h1>
        @if($academicYear)
            <h2>Academic Year: {{ $academicYear->academic_year_name }}</h2>
        @endif
    </div>

    <div class="summary-section">
        <h3>Fee Collection Statistics</h3>
        <div class="summary-stats">
            <div class="stat-card">
                <h4>Total Students</h4>
                <div class="number">{{ $stats['total_students'] }}</div>
            </div>
            <div class="stat-card">
                <h4>Complete Fees</h4>
                <div class="number">{{ $stats['students_with_complete_fees'] }}</div>
            </div>
            <div class="stat-card">
                <h4>Partial Fees</h4>
                <div class="number">{{ $stats['students_with_partial_fees'] }}</div>
            </div>
            <div class="stat-card">
                <h4>Collection Rate</h4>
                <div class="number">{{ $stats['collection_percentage'] }}%</div>
            </div>
        </div>
        
        <table style="margin-top: 15px;">
            <tr>
                <td><strong>Total Expected Fees:</strong></td>
                <td class="text-right"><strong>{{ number_format($stats['total_expected_fees'], 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Total Collected Fees:</strong></td>
                <td class="text-right"><strong>{{ number_format($stats['total_collected_fees'], 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Total Due Fees:</strong></td>
                <td class="text-right"><strong>{{ number_format($stats['total_due_fees'], 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
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
                        {{ $summary->semesters_completed }}/8
                        <br>
                        <small>{{ number_format($summary->paid_semester_fees, 2) }}/{{ number_format($summary->total_semester_fees, 2) }}</small>
                    </td>
                    <td>
                        {{ $summary->months_completed }}/48
                        <br>
                        <small>{{ number_format($summary->paid_monthly_fees, 2) }}/{{ number_format($summary->total_monthly_fees, 2) }}</small>
                    </td>
                    <td class="text-right">{{ number_format($summary->total_paid, 2) }}</td>
                    <td class="text-right">{{ number_format($summary->total_due, 2) }}</td>
                    <td class="text-center">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $completionPercentage }}%">
                                {{ $completionPercentage }}%
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        @if($summary->all_fees_paid)
                            <span class="status-complete">Complete</span>
                        @elseif($summary->total_paid > 0)
                            <span class="status-partial">Partial</span>
                        @else
                            <span class="status-none">No Payment</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No fee data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Report generated on {{ date('Y-m-d H:i:s') }}</p>
        <p>This report shows the complete fee collection status for all students including 8 semester fees and 48 monthly fees over 4 years.</p>
        <p>This is a computer generated report.</p>
    </div>
</body>
</html>
