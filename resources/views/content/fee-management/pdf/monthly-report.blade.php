<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Fee Report - {{ $monthName }} {{ $request->year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header h2 {
            margin: 10px 0 0 0;
            font-size: 18px;
            color: #666;
        }
        .header p {
            margin: 5px 0;
            color: #888;
        }
        .summary-stats {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .stat-row {
            display: table-row;
        }
        .stat-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            background-color: #f8f9fa;
        }
        .stat-cell strong {
            color: #333;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .report-table th,
        .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .report-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-overdue {
            color: #dc3545;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .financial-summary {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .financial-summary h3 {
            margin-top: 0;
            color: #333;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-label {
            display: table-cell;
            padding: 5px;
            font-weight: bold;
            width: 70%;
        }
        .summary-value {
            display: table-cell;
            padding: 5px;
            text-align: right;
            width: 30%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Monthly Fee Due Report</h1>
        <h2>{{ $monthName }} {{ $request->year }}</h2>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-row">
            <div class="stat-cell">
                <strong>Total Students</strong><br>
                {{ $stats['total_students'] }}
            </div>
            <div class="stat-cell">
                <strong>Paid</strong><br>
                <span class="status-paid">{{ $stats['paid_count'] }}</span>
            </div>
            <div class="stat-cell">
                <strong>Unpaid</strong><br>
                <span class="status-pending">{{ $stats['unpaid_count'] }}</span>
            </div>
            <div class="stat-cell">
                <strong>Overdue</strong><br>
                <span class="status-overdue">{{ $stats['overdue_count'] }}</span>
            </div>
            <div class="stat-cell">
                <strong>Collection Rate</strong><br>
                {{ $stats['collection_rate'] }}%
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="financial-summary">
        <h3>Financial Summary</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-label">Total Fee Amount:</div>
                <div class="summary-value">৳{{ $stats['total_fee_amount'] }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Fine Amount:</div>
                <div class="summary-value">৳{{ $stats['total_fine_amount'] }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Collected:</div>
                <div class="summary-value status-paid">৳{{ $stats['total_collected'] }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Total Pending:</div>
                <div class="summary-value status-overdue">৳{{ $stats['total_pending'] }}</div>
            </div>
        </div>
    </div>

    <!-- Payment Records Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th>SL</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Fee Amount</th>
                <th>Fine Amount</th>
                <th>Total Amount</th>
                <th>Due Date</th>
                <th>Payment Date</th>
                <th>Status</th>
                <th>Days Overdue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $index => $payment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $payment->student->student_unique_id ?? 'N/A' }}</td>
                <td>{{ $payment->student->full_name_in_english_block_letter ?? 'N/A' }}</td>
                <td class="text-right">৳{{ number_format($payment->fee_amount, 2) }}</td>
                <td class="text-right">৳{{ number_format($payment->fine_amount, 2) }}</td>
                <td class="text-right">৳{{ number_format($payment->total_amount, 2) }}</td>
                <td class="text-center">{{ $payment->due_date->format('d-m-Y') }}</td>
                <td class="text-center">
                    {{ $payment->payment_date ? $payment->payment_date->format('d-m-Y') : '-' }}
                </td>
                <td class="text-center">
                    @if($payment->is_paid)
                        <span class="status-paid">Paid</span>
                    @elseif($payment->is_overdue)
                        <span class="status-overdue">Overdue</span>
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </td>
                <td class="text-center">
                    {{ $payment->days_overdue > 0 ? $payment->days_overdue : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($payments->count() == 0)
        <div style="text-align: center; padding: 50px; color: #666;">
            <h3>No payment records found</h3>
            <p>No payment records were found for the selected criteria.</p>
        </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated report. Generated by Polytechnic Management System on {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>Report Status Filter: {{ ucfirst($request->status ?? 'all') }} | Total Records: {{ $payments->count() }}</p>
    </div>
</body>
</html>
