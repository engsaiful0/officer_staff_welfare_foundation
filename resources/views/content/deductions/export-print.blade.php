<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Deduction List</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0 0 6px; }
        .filters { margin-bottom: 12px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f2f2f2; font-weight: bold; }
        .text-end { text-align: right; }
        .summary { margin-top: 16px; text-align: right; font-weight: bold; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $appSettings->app_name ?? config('app.name') }}</h2>
        <h3 style="margin: 0;">Monthly Deduction List</h3>
        <p style="margin: 8px 0 0;">Generated on: {{ date('M d, Y h:i A') }}</p>
    </div>

    @if(!empty($filterSummary))
        <div class="filters"><strong>Filters:</strong> {{ $filterSummary }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member</th>
                <th>Member ID</th>
                <th>Account Number</th>
                <th>Mobile</th>
                <th>Designation</th>
                <th>Period</th>
                <th class="text-end">Deposit</th>
                <th class="text-end">Investment</th>
                <th class="text-end">Qard</th>
                <th class="text-end">Profit</th>
                <th class="text-end">Compensation</th>
                <th class="text-end">Total Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deductions as $index => $d)
                @php
                    $member = $d->member;
                    $accountNumber = $resolveAccountNumber($member);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $member?->name ?? '—' }}</td>
                    <td>{{ $member?->member_unique_id ?? '—' }}</td>
                    <td>{{ $accountNumber }}</td>
                    <td>{{ $member?->mobile ?? '—' }}</td>
                    <td>{{ $member?->designation?->designation_name ?? '—' }}</td>
                    <td>{{ date('F', mktime(0, 0, 0, (int) $d->month, 1)) }} {{ $d->year }}</td>
                    <td class="text-end">{{ number_format($d->monthly_deposit_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($d->monthly_investment_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($d->monthly_qard_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($d->profit_on_deposit_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($d->compensation_on_investment_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($d->total_amount, 2) }}</td>
                    <td>{{ $d->deduction_date?->format('Y-m-d') ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p>Total records: {{ $summary['count'] }}</p>
        <p>Grand total amount: {{ number_format($summary['total_amount'], 2) }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 24px;">
        <button type="button" onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print</button>
        <button type="button" onclick="window.close()" style="padding: 8px 16px; cursor: pointer; margin-left: 8px;">Close</button>
    </div>
</body>
</html>
