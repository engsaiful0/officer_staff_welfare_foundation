<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt - {{ $installment->receipt_number }}</title>
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
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 18px;
        }
        .receipt-info {
            margin: 20px 0;
        }
        .receipt-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-info td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .receipt-info td:first-child {
            width: 40%;
            font-weight: bold;
            color: #666;
        }
        .amount-section {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total-row {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAYMENT RECEIPT</h1>
        <h2>Investment Collection</h2>
        <p>Receipt Number: <strong>{{ $installment->receipt_number }}</strong></p>
    </div>

    <div class="receipt-info">
        <table>
            <tr>
                <td>Payment Date:</td>
                <td>{{ $installment->paid_date->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td>Member Name:</td>
                <td><strong>{{ $installment->investment->member->name }}</strong></td>
            </tr>
            <tr>
                <td>Member ID:</td>
                <td>{{ $installment->investment->member->unique_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Account Number:</td>
                <td>{{ $installment->investment->account->account_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Installment Number:</td>
                <td>#{{ $installment->installment_number }}</td>
            </tr>
            <tr>
                <td>Schedule Date:</td>
                <td>{{ $installment->schedule_date->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td>{{ $installment->paymentMethod->payment_method_name ?? 'N/A' }}</td>
            </tr>
            @if($installment->transaction_reference)
            <tr>
                <td>Transaction Reference:</td>
                <td>{{ $installment->transaction_reference }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="amount-section">
        <div class="amount-row">
            <span>Principal Amount:</span>
            <span>${{ number_format($installment->principal_amount, 2) }}</span>
        </div>
        <div class="amount-row">
            <span>Interest (Rent):</span>
            <span>${{ number_format($installment->rent, 2) }}</span>
        </div>
        @if($installment->fine_amount > 0)
        <div class="amount-row">
            <span>Late Fee:</span>
            <span class="text-danger">${{ number_format($installment->fine_amount, 2) }}</span>
        </div>
        @endif
        <div class="amount-row">
            <span>Gross Amount:</span>
            <span><strong>${{ number_format($installment->total_amount, 2) }}</strong></span>
        </div>
        @if($installment->discount_amount > 0)
        <div class="amount-row">
            <span>Discount:</span>
            <span class="text-success">-${{ number_format($installment->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="amount-row total-row">
            <span>Net Amount Paid:</span>
            <span>${{ number_format($installment->total_amount - ($installment->discount_amount ?? 0), 2) }}</span>
        </div>
    </div>

    @if($installment->notes)
    <div style="margin-top: 20px;">
        <strong>Notes:</strong>
        <p>{{ $installment->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer generated receipt. No signature required.</p>
        <p>Generated on: {{ date('M d, Y H:i A') }}</p>
    </div>
</body>
</html>

