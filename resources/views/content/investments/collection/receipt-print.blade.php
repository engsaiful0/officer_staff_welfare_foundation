<!DOCTYPE html>
<html>
<head>
    <title>Print Receipt - {{ $installment->receipt_number }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
        body { 
            padding: 20px; 
            font-family: Arial, sans-serif;
        }
        .print-header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .print-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .print-header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 18px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print mb-4">
        <button onclick="window.print()" class="btn btn-primary">Print Now</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>

    <div class="print-header">
        <h1>PAYMENT RECEIPT</h1>
        <h2>Investment Collection</h2>
        <p><strong>Receipt Number: {{ $installment->receipt_number }}</strong></p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Payment Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 50%;"><strong>Payment Date:</strong></td>
                            <td>{{ $installment->paid_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Installment #:</strong></td>
                            <td>#{{ $installment->installment_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Schedule Date:</strong></td>
                            <td>{{ $installment->schedule_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Payment Method:</strong></td>
                            <td>{{ $installment->paymentMethod->payment_method_name ?? 'N/A' }}</td>
                        </tr>
                        @if($installment->transaction_reference)
                        <tr>
                            <td class="text-muted"><strong>Transaction Ref:</strong></td>
                            <td>{{ $installment->transaction_reference }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Member Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 50%;"><strong>Member Name:</strong></td>
                            <td><strong>{{ $installment->investment->member->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Member ID:</strong></td>
                            <td>{{ $installment->investment->member->unique_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><strong>Account Number:</strong></td>
                            <td>{{ $installment->investment->account->account_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">Payment Amount Details</h6>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <td class="text-muted" style="width: 50%;"><strong>Principal Amount:</strong></td>
                    <td class="text-end">${{ number_format($installment->principal_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Interest (Rent):</strong></td>
                    <td class="text-end">${{ number_format($installment->rent, 2) }}</td>
                </tr>
                @if($installment->fine_amount > 0)
                <tr>
                    <td class="text-muted"><strong>Late Fee:</strong></td>
                    <td class="text-end text-danger">${{ number_format($installment->fine_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="border-top">
                    <td class="text-muted"><strong>Gross Amount:</strong></td>
                    <td class="text-end"><strong>${{ number_format($installment->total_amount, 2) }}</strong></td>
                </tr>
                @if($installment->discount_amount > 0)
                <tr>
                    <td class="text-muted"><strong>Discount:</strong></td>
                    <td class="text-end text-success">-${{ number_format($installment->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="border-top">
                    <td class="text-muted"><strong class="fs-5">Net Amount Paid:</strong></td>
                    <td class="text-end"><strong class="text-primary fs-4">${{ number_format($installment->total_amount - ($installment->discount_amount ?? 0), 2) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    @if($installment->notes)
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h6 class="mb-0">Notes</h6>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $installment->notes }}</p>
        </div>
    </div>
    @endif

    <div class="text-center mt-4" style="border-top: 1px solid #ddd; padding-top: 15px;">
        <p class="text-muted mb-0">This is a computer generated receipt. No signature required.</p>
        <p class="text-muted mb-0">Generated on: {{ date('M d, Y H:i A') }}</p>
    </div>
</body>
</html>

