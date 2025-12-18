<!DOCTYPE html>
<html>
<head>
    <title>Print Investment Collections</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: none !important; }
        }
        body { padding: 20px; }
        .print-header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary">Print Now</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="print-header">
        <h1>Investment Collection Report</h1>
        <p>Date Range: {{ request('date_from', 'Start') }} to {{ request('date_to', 'End') }}</p>
        <p>Generated on: {{ date('M d, Y H:i A') }}</p>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Receipt #</th>
                <th>Account #</th>
                <th>Member</th>
                <th>Inst #</th>
                <th>Gross</th>
                <th>Discount</th>
                <th>Net Paid</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGross = 0; $totalDisc = 0; $totalNet = 0; @endphp
            @foreach($collections as $item)
                @php 
                    $net = $item->total_amount - ($item->discount_amount ?? 0);
                    $totalGross += $item->total_amount;
                    $totalDisc += ($item->discount_amount ?? 0);
                    $totalNet += $net;
                @endphp
                <tr>
                    <td>{{ $item->paid_date->format('Y-m-d') }}</td>
                    <td>{{ $item->receipt_number }}</td>
                    <td>{{ $item->investment->account->account_number ?? 'N/A' }}</td>
                    <td>{{ $item->investment->member->name }}</td>
                    <td>#{{ $item->installment_number }}</td>
                    <td>${{ number_format($item->total_amount, 2) }}</td>
                    <td>${{ number_format($item->discount_amount ?? 0, 2) }}</td>
                    <td><strong>${{ number_format($net, 2) }}</strong></td>
                    <td>{{ $item->paymentMethod->payment_method_name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="table-light">
            <tr>
                <th colspan="5" class="text-end">Totals:</th>
                <th>${{ number_format($totalGross, 2) }}</th>
                <th>${{ number_format($totalDisc, 2) }}</th>
                <th>${{ number_format($totalNet, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

