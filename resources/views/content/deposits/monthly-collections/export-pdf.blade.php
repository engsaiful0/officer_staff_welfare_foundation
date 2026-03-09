<!DOCTYPE html>
<html>
<head>
    <title>Monthly Deposit Collections Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; }
        .summary { margin-top: 20px; text-align: right; padding: 10px; background-color: #f9f9f9; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Monthly Deposit Collections Report</h2>
        <p>Generated on: {{ date('M d, Y H:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Collection #</th>
                <th>Account #</th>
                <th>Member Name</th>
                <th>Member ID</th>
                <th class="text-right">Amount</th>
                <th>Month</th>
                <th>Description</th>
                <th>Collected By</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($collections as $item)
                @php $total += $item->amount; @endphp
                <tr>
                    <td>{{ $item->collection_date->format('Y-m-d') }}</td>
                    <td>{{ $item->collection_number }}</td>
                    <td>{{ $item->deposit->deposit_account_number ?? 'N/A' }}</td>
                    <td>{{ $item->member->name ?? 'N/A' }}</td>
                    <td>{{ $item->member->unique_id ?? 'N/A' }}</td>
                    <td class="text-right">৳{{ number_format($item->amount, 2) }}</td>
                    <td>{{ $item->month ?? 'N/A' }}</td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td>{{ $item->createdBy->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>Total Collections: {{ $summary['total_collections'] ?? count($collections) }}</strong></p>
        <p><strong>Total Amount: ৳{{ number_format($summary['total_amount'] ?? $total, 2) }}</strong></p>
    </div>

    <div class="footer">
        <p>Page 1</p>
    </div>
</body>
</html>







