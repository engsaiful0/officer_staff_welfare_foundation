<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fee Report - {{ $report['student']->student_unique_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #666;
        }
        .student-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        .student-info h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 12px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 3px;
            text-align: center;
            flex: 1;
            margin: 0 3px;
        }
        .summary-card h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
        }
        .summary-card .amount {
            font-size: 14px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            background-color: #333;
            color: white;
            padding: 8px;
            margin: 0 0 10px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-unpaid {
            color: #dc3545;
            font-weight: bold;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Fee Report</h1>
        <h2>{{ $report['student']->student_unique_id ?? 'N/A' }}</h2>
    </div>

    <div class="student-info">
        <h3>Student Information</h3>
        <div class="info-row">
            <div class="info-label">Student ID:</div>
            <div>{{ $report['student']->student_unique_id ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Student Name:</div>
            <div>{{ $report['student']->full_name_in_english_block_letter }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Father's Name:</div>
            <div>{{ $report['student']->father_name_in_english_block_letter }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Technology:</div>
            <div>{{ $report['student']->technology->technology_name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Academic Year:</div>
            <div>{{ $report['student']->academicYear->academic_year_name ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <h4>Total Expected</h4>
            <div class="amount">{{ number_format($report['fee_summary']->total_fees, 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Paid</h4>
            <div class="amount">{{ number_format($report['fee_summary']->total_paid, 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Total Due</h4>
            <div class="amount">{{ number_format($report['fee_summary']->total_due, 2) }}</div>
        </div>
        <div class="summary-card">
            <h4>Completion</h4>
            <div class="amount">{{ $report['fee_summary']->completion_percentage }}%</div>
        </div>
    </div>

    <div class="section">
        <h3>Semester Fees Details ({{ $report['fee_summary']->semesters_completed }}/8)</h3>
        <table>
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
                        <td class="{{ $semesterFee->is_paid ? 'status-paid' : 'status-unpaid' }}">
                            {{ $semesterFee->is_paid ? 'Paid' : 'Unpaid' }}
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>Total</strong></td>
                    <td><strong>{{ number_format($report['fee_summary']->paid_semester_fees, 2) }} / {{ number_format($report['fee_summary']->total_semester_fees, 2) }}</strong></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Monthly Fees Details ({{ $report['fee_summary']->months_completed }}/48)</h3>
        <table>
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
                        <td class="{{ $monthlyFee->is_paid ? 'status-paid' : 'status-unpaid' }}">
                            {{ $monthlyFee->is_paid ? 'Paid' : 'Unpaid' }}
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td><strong>Total</strong></td>
                    <td><strong>{{ number_format($report['fee_summary']->paid_monthly_fees, 2) }} / {{ number_format($report['fee_summary']->total_monthly_fees, 2) }}</strong></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Fee Collection History</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Collected By</th>
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Report generated on {{ date('Y-m-d H:i:s') }}</p>
        <p>This is a computer generated report.</p>
    </div>
</body>
</html>
