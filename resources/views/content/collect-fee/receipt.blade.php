@php
    $student = $feeCollect->student;
    $academicYear = $feeCollect->academic_year;
    $semester = $feeCollect->semester;
    $paymentMethod = $feeCollect->payment_method;
    $user = $feeCollect->user;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <h2><strong>{{ $app_setting->school_name }}</strong></h2>
                <p>{{ $app_setting->school_address }}</p>
                <h4>Fee Receipt</h4>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6">
                <p><strong>Receipt No:</strong> {{ $feeCollect->id }}</p>
                <p><strong>Student Name:</strong> {{ $student->full_name_in_english_block_letter }}</p>
                <p><strong>Admission No:</strong> {{ $student->admission_id }}</p>
                <p><strong>Academic Year:</strong> {{ $academicYear->academic_year_name }}</p>
                <p><strong>Year:</strong> {{ $feeCollect->year ?? 'N/A' }}</p>
            </div>
            <div class="col-6 text-right">
                <p><strong>Date:</strong> {{ $feeCollect->date}}</p>
                <p><strong>Semester:</strong> {{ $semester->semester_name }}</p>
                @if(isset($summary))
                <p><strong>Course Progress:</strong> {{ $summary->semesters_completed }}/8 semesters, {{ $summary->months_completed }}/48 months</p>
                <p><strong>Completion:</strong> {{ $summary->completion_percentage }}%</p>
                @endif
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fee Head</th>
                            <th class="text-right">Amount</th>
                            @if($feeCollect->fine_amount > 0)
                            <th class="text-right">Fine Amount</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $fineDetails = $feeCollect->fine_details ? json_decode($feeCollect->fine_details, true) : [];
                            $fineDetailsByFeeHead = [];
                            foreach($fineDetails as $fine) {
                                $fineDetailsByFeeHead[$fine['fee_head_id']] = $fine;
                            }
                        @endphp
                        @foreach ($fee_heads as $key => $fee_head)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $fee_head->name }}</td>
                                <td class="text-right">{{ number_format($fee_head->amount, 2) }}</td>
                                @if($feeCollect->fine_amount > 0)
                                <td class="text-right">
                                    @if(isset($fineDetailsByFeeHead[$fee_head->id]))
                                        <span class="text-danger">{{ number_format($fineDetailsByFeeHead[$fee_head->id]['fine_amount'], 2) }}</span>
                                    @else
                                        <span class="text-muted">0.00</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
            </div>
            <div class="col-6">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td><strong>Sub Total:</strong></td>
                            <td class="text-right">{{ number_format($feeCollect->total_amount + $feeCollect->discount - $feeCollect->fine_amount, 2) }}</td>
                        </tr>
                        @if($feeCollect->fine_amount > 0)
                        <tr>
                            <td><strong>Fine Amount ({{ $feeCollect->overdue_days }} days overdue):</strong></td>
                            <td class="text-right text-danger">{{ number_format($feeCollect->fine_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td><strong>Discount:</strong></td>
                            <td class="text-right">{{ number_format($feeCollect->discount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Payable:</strong></td>
                            <td class="text-right"><strong>{{ number_format($feeCollect->total_amount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
        </div>
        @if($feeCollect->fine_amount > 0 && $feeCollect->fine_details)
        <div class="row mt-3">
            <div class="col-12">
                <h5>Fine Details</h5>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Fee Head</th>
                            <th>Overdue Days</th>
                            <th>Fine Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(json_decode($feeCollect->fine_details, true) as $fine)
                        <tr>
                            <td>{{ $fine['month_name'] }}</td>
                            <td>{{ $fine['fee_head_name'] }}</td>
                            <td>{{ $fine['overdue_days'] }} days</td>
                            <td class="text-right">{{ number_format($fine['fine_amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        <div class="row mt-4">
            <div class="col-6">
                <p><strong>Payment Method:</strong> {{ $paymentMethod->payment_method_name }}</p>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-6">
                <p>_________________________</p>
                <p>Student's Signature</p>
            </div>
            <div class="col-6 text-right">
                <p>_________________________</p>
                <p>Officer's Signature</p>
            </div>
        </div>
        <div class="row mt-5 no-print">
            <div class="col-12 text-center">
                <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
                <a href="{{ route('app-collect-fee.create') }}" class="btn btn-secondary">Back to Collect Fee</a>
            </div>
        </div>
    </div>
</body>

</html>
