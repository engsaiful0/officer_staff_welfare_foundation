<!DOCTYPE html>
<html>
<head>
    <title>Investment Account Ledger - {{ $investment->account->account_number ?? $investment->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18pt;
        }
        .info {
            margin-bottom: 15px;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info td {
            padding: 3px 5px;
        }
        .info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        td {
            text-align: right;
        }
        td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(7) {
            text-align: center;
        }
        td:nth-child(3) {
            text-align: left;
        }
        tfoot td {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">Print</button>
        <a href="{{ route('investments.account-ledger') }}" style="padding: 10px 20px; font-size: 14px; text-decoration: none; background: #6c757d; color: white; border-radius: 4px; display: inline-block; margin-left: 10px;">Back</a>
    </div>

    <div class="header">
        <h2>Investment Account Ledger</h2>
    </div>

    <div class="info">
        <table>
            <tr>
                <td>Account Number:</td>
                <td>{{ $investment->account->account_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Member Name:</td>
                <td>{{ $investment->member->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Member ID:</td>
                <td>{{ $investment->member->unique_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Start Date:</td>
                <td>{{ $investment->start_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Principal Amount:</td>
                <td>{{ number_format($investment->principal_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Generated On:</td>
                <td>{{ date('d/m/Y h:i A') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Ending date</th>
                <th>Particulars</th>
                <th>Dr</th>
                <th>Cr</th>
                <th>Balance</th>
                <th>Days</th>
                <th>Product</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledger as $row)
            <tr>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['ending_date'] }}</td>
                <td>{{ $row['particulars'] }}</td>
                <td>{{ number_format($row['debit'], 2) }}</td>
                <td>{{ number_format($row['credit'], 2) }}</td>
                <td>{{ number_format($row['balance'], 2) }}</td>
                <td>{{ $row['days'] }}</td>
                <td>{{ number_format($row['product'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Total Product:</strong></td>
                <td colspan="3" style="text-align: right;">
                    <strong>{{ number_format($totalProduct, 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Generated on {{ date('d/m/Y h:i A') }}</p>
    </div>
</body>
</html>





