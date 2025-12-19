@extends('layouts.contentNavbarLayout')

@section('title', 'View Investment Collection')

@section('content')
<style>
    @media print {
        .no-print { 
            display: none !important; 
        }
        body { 
            background: white !important; 
            padding: 0 !important;
        }
        .container-xxl {
            max-width: 100% !important;
            padding: 0 !important;
        }
        .card { 
            box-shadow: none !important; 
            border: 1px solid #ddd !important; 
            margin-bottom: 15px !important;
        }
        .card-header { 
            background: #f8f9fa !important; 
            color: #333 !important;
            border-bottom: 2px solid #333 !important;
        }
        .bg-primary, .bg-info, .bg-success, .bg-warning, .bg-secondary {
            background-color: #f8f9fa !important;
            color: #333 !important;
        }
        .text-primary, .text-success, .text-danger {
            color: #000 !important;
        }
        .badge {
            background-color: #333 !important;
            color: white !important;
        }
        .btn {
            display: none !important;
        }
        @page {
            margin: 1cm;
        }
    }
    
    .receipt-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .receipt-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 8px 8px 0 0;
        text-align: center;
    }
    
    .info-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .info-card.border-primary { border-left-color: #667eea; }
    .info-card.border-success { border-left-color: #10b981; }
    .info-card.border-warning { border-left-color: #f59e0b; }
    .info-card.border-info { border-left-color: #3b82f6; }
    
    .amount-highlight {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin: 20px 0;
    }
    
    .detail-row {
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 0.9rem;
    }
    
    .detail-value {
        font-weight: 500;
        color: #111827;
        font-size: 1rem;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Action Buttons (Hidden on Print) -->
    <div class="row mb-3 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('investments.view-collection') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to Collections
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('investments.collection.edit', $installment) }}" class="btn btn-warning">
                        <i class="bx bx-edit-alt me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="bx bx-printer me-1"></i> Print
                    </button>
                    <a href="{{ route('investments.collection.export', ['type' => 'pdf', 'installment_id' => $installment->id]) }}" 
                       class="btn btn-danger" target="_blank">
                        <i class="bx bxs-file-pdf me-1"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Container -->
    <div class="receipt-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <h2 class="mb-2"><i class="bx bx-receipt me-2"></i>Payment Receipt</h2>
            <h4 class="mb-0">{{ $installment->receipt_number }}</h4>
            <p class="mb-0 mt-2" style="opacity: 0.9;">Investment Collection</p>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-6">
                    <!-- Collection Information -->
                    <div class="card info-card border-primary mb-4">
                        <div class="card-header bg-transparent border-0 pb-2">
                            <h5 class="mb-0"><i class="bx bx-info-circle me-2 text-primary"></i>Collection Information</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="detail-row">
                                <div class="detail-label">Receipt Number</div>
                                <div class="detail-value text-primary fs-5">{{ $installment->receipt_number }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Payment Date</div>
                                <div class="detail-value">{{ $installment->paid_date->format('F d, Y') }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Installment Number</div>
                                <div class="detail-value"><strong>#{{ $installment->installment_number }}</strong></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Schedule Date</div>
                                <div class="detail-value">{{ $installment->schedule_date->format('F d, Y') }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Status</div>
                                <div class="detail-value">
                                    <span class="badge bg-success">Paid</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Information -->
                    <div class="card info-card border-info mb-4">
                        <div class="card-header bg-transparent border-0 pb-2">
                            <h5 class="mb-0"><i class="bx bx-user me-2 text-info"></i>Member Information</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="detail-row">
                                <div class="detail-label">Member Name</div>
                                <div class="detail-value"><strong>{{ $installment->investment->member->name }}</strong></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Member ID</div>
                                <div class="detail-value">{{ $installment->investment->member->unique_id ?? 'N/A' }}</div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Account Number</div>
                                <div class="detail-value"><strong>{{ $installment->investment->account->account_number ?? 'N/A' }}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-6">
                    <!-- Payment Details -->
                    <div class="card info-card border-success mb-4">
                        <div class="card-header bg-transparent border-0 pb-2">
                            <h5 class="mb-0"><i class="bx bx-money me-2 text-success"></i>Payment Breakdown</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="detail-row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="detail-label">Principal Amount</span>
                                    <span class="detail-value">${{ number_format($installment->principal_amount, 2) }}</span>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="detail-label">Interest (Rent)</span>
                                    <span class="detail-value">${{ number_format($installment->rent, 2) }}</span>
                                </div>
                            </div>
                            @if($installment->fine_amount > 0)
                            <div class="detail-row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="detail-label">Late Fee</span>
                                    <span class="detail-value text-danger">${{ number_format($installment->fine_amount, 2) }}</span>
                                </div>
                            </div>
                            @endif
                            <div class="detail-row border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="detail-label"><strong>Gross Amount</strong></span>
                                    <span class="detail-value"><strong>${{ number_format($installment->total_amount, 2) }}</strong></span>
                                </div>
                            </div>
                            @if($installment->discount_amount > 0)
                            <div class="detail-row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="detail-label">Discount</span>
                                    <span class="detail-value text-success">-${{ number_format($installment->discount_amount, 2) }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Net Amount Highlight -->
                    <div class="amount-highlight">
                        <div class="mb-2" style="opacity: 0.9;">Net Amount Paid</div>
                        <div class="fs-1 fw-bold">${{ number_format($installment->total_amount - ($installment->discount_amount ?? 0), 2) }}</div>
                    </div>

                    <!-- Payment Method -->
                    <div class="card info-card border-warning mb-4">
                        <div class="card-header bg-transparent border-0 pb-2">
                            <h5 class="mb-0"><i class="bx bx-credit-card me-2 text-warning"></i>Payment Method</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="detail-row">
                                <div class="detail-label">Method</div>
                                <div class="detail-value">{{ $installment->paymentMethod->payment_method_name ?? 'N/A' }}</div>
                            </div>
                            @if($installment->bank_name)
                            <div class="detail-row">
                                <div class="detail-label">Bank Name</div>
                                <div class="detail-value">{{ $installment->bank_name }}</div>
                            </div>
                            @endif
                            @if($installment->check_number)
                            <div class="detail-row">
                                <div class="detail-label">Check Number</div>
                                <div class="detail-value">{{ $installment->check_number }}</div>
                            </div>
                            @endif
                            @if($installment->transaction_reference)
                            <div class="detail-row">
                                <div class="detail-label">Transaction Reference</div>
                                <div class="detail-value">{{ $installment->transaction_reference }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            @if($installment->notes)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card info-card border-secondary">
                        <div class="card-header bg-transparent border-0 pb-2">
                            <h5 class="mb-0"><i class="bx bx-note me-2"></i>Additional Notes</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="mb-0">{{ $installment->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Footer (Hidden on Print) -->
            <div class="row mt-4 no-print">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
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

            <!-- Print Footer -->
            <div class="row mt-4 print-only" style="display: none;">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">
                        <small>This is a computer generated receipt. No signature required.</small><br>
                        <small>Generated on: {{ date('F d, Y \a\t h:i A') }}</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .print-only {
            display: block !important;
        }
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
                toastr.success(
                    data.message || 'Collection payment reversed successfully',
                    'Success',
                    {
                        timeOut: 2000,
                        progressBar: true
                    }
                );
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
