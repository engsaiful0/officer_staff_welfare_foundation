<!DOCTYPE html>
<html>
<head>
    <title>Monthly Deposit Collection Receipt</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .receipt { max-width: 800px; margin: 0 auto; padding: 20px; border: 2px solid #000; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .details { margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #ddd; }
        .detail-label { font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; border-top: 2px solid #000; padding-top: 20px; }
        .amount { font-size: 24px; font-weight: bold; color: #28a745; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>Monthly Deposit Collection Receipt</h1>
            <p>Receipt Number: <strong>{{ $collection->collection_number }}</strong></p>
        </div>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Collection Date:</span>
                <span>{{ $collection->collection_date->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Deposit Account Number:</span>
                <span>{{ $collection->deposit->deposit_account_number ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Member Name:</span>
                <span>{{ $collection->member->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Member ID:</span>
                <span>{{ $collection->member->unique_id ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Month:</span>
                <span>{{ $collection->month ?? 'N/A' }}</span>
            </div>
            @if($collection->description)
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span>{{ $collection->description }}</span>
            </div>
            @endif
        </div>

        <div class="amount">
            Amount: ৳{{ number_format($collection->amount, 2) }}
        </div>

        <div class="footer">
            <p>Collected By: {{ $collection->createdBy->name ?? 'N/A' }}</p>
            <p>Generated on: {{ date('M d, Y H:i A') }}</p>
        </div>
    </div>
</body>
</html>


