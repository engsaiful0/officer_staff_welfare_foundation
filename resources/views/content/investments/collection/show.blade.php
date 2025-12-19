@extends('layouts.contentNavbarLayout')

@section('title', 'View Investment Collection')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bx bx-receipt me-2"></i>Investment Collection Details
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('investments.view-collection') }}" class="btn btn-light btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Back to Collections
                        </a>
                        <a href="{{ route('investments.collection.edit', $installment) }}" class="btn btn-warning btn-sm">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                            <i class="bx bx-printer me-1"></i> Print
                        </button>
                        <a href="{{ route('investments.collection.export', ['type' => 'pdf', 'installment_id' => $installment->id]) }}" 
                           class="btn btn-danger btn-sm" target="_blank">
                            <i class="bx bxs-file-pdf me-1"></i> Export PDF
                        </a>
                        <a href="{{ route('investments.collection.export', ['type' => 'print', 'installment_id' => $installment->id]) }}" 
                           class="btn btn-info btn-sm" target="_blank">
                            <i class="bx bx-printer me-1"></i> Print Receipt
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column: Collection Details -->
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="bx bx-receipt me-2"></i>Collection Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" style="width: 40%;"><strong>Receipt Number:</strong></td>
                                            <td><strong class="text-primary">{{ $installment->receipt_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Payment Date:</strong></td>
                                            <td>{{ $installment->paid_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Installment Number:</strong></td>
                                            <td><strong>#{{ $installment->installment_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Schedule Date:</strong></td>
                                            <td>{{ $installment->schedule_date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Status:</strong></td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="bx bx-money me-2"></i>Payment Details</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" style="width: 40%;"><strong>Principal Amount:</strong></td>
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
                                        <tr>
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
                                            <td class="text-muted"><strong>Net Amount Paid:</strong></td>
                                            <td class="text-end"><strong class="text-primary fs-5">${{ number_format($installment->total_amount - ($installment->discount_amount ?? 0), 2) }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Member & Investment Info -->
                        <div class="col-lg-6">
                            <!-- Member Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bx bx-user me-2"></i>Member Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" style="width: 40%;"><strong>Member Name:</strong></td>
                                            <td><strong>{{ $installment->investment->member->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Member ID:</strong></td>
                                            <td>{{ $installment->investment->member->unique_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"><strong>Account Number:</strong></td>
                                            <td><strong>{{ $installment->investment->account->account_number ?? 'N/A' }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="bx bx-credit-card me-2"></i>Payment Method</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted" style="width: 40%;"><strong>Method:</strong></td>
                                            <td>{{ $installment->paymentMethod->payment_method_name ?? 'N/A' }}</td>
                                        </tr>
                                        @if($installment->bank_name)
                                        <tr>
                                            <td class="text-muted"><strong>Bank Name:</strong></td>
                                            <td>{{ $installment->bank_name }}</td>
                                        </tr>
                                        @endif
                                        @if($installment->check_number)
                                        <tr>
                                            <td class="text-muted"><strong>Check Number:</strong></td>
                                            <td>{{ $installment->check_number }}</td>
                                        </tr>
                                        @endif
                                        @if($installment->transaction_reference)
                                        <tr>
                                            <td class="text-muted"><strong>Transaction Reference:</strong></td>
                                            <td>{{ $installment->transaction_reference }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            @if($installment->notes)
                            <div class="card mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0"><i class="bx bx-note me-2"></i>Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $installment->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4 no-print">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('investments.view-collection') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-1"></i> Back to Collections
                                </a>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('investments.collection.edit', $installment) }}" class="btn btn-primary">
                                        <i class="bx bx-edit-alt me-1"></i> Edit Collection
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="deleteCollection()">
                                        <i class="bx bx-trash me-1"></i> Reverse Payment
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    body { background: white !important; }
    .card-header { background: #f8f9fa !important; color: #333 !important; }
}
</style>

<script>
function deleteCollection() {
    if (confirm('Are you sure you want to reverse this payment?\n\nReceipt: {{ $installment->receipt_number }}\n\nThis action will mark the installment as pending again and reverse all related transactions.')) {
        fetch('{{ route("investments.collection.destroy", $installment) }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Collection payment reversed successfully');
                setTimeout(() => {
                    window.location.href = '{{ route("investments.view-collection") }}';
                }, 1500);
            } else {
                toastr.error(data.message || 'Failed to reverse payment');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while reversing the payment');
        });
    }
}
</script>
@endsection

