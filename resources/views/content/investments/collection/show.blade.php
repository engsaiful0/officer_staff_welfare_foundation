@extends('layouts.contentNavbarLayout')

@section('title', 'View Investment Collection')

@section('content')

<style>
     /* =======================
    RECEIPT BASE STYLE
 ======================= */
     .receipt-container {
         max-width: 1100px;
         margin: auto;
         background: #fff;
         border-radius: 12px;
         box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
         overflow: hidden;
         font-family: 'Segoe UI', sans-serif;
         color: #000 !important;
     }

     /* Ensure all text is black */
     .receipt-container,
     .receipt-container * {
         color: #000 !important;
     }

     /* Header */
     .receipt-header {
         background: #f8fafc;
         border-bottom: 1px solid #e5e7eb;
         text-align: center;
         padding: 16px;
     }

     .receipt-header p {
         margin: 0;
         color: #000 !important;
     }

     /* Cards */
     .info-card {
         border: 1px solid #e5e7eb;
         border-radius: 10px;
         box-shadow: 0 4px 12px rgba(0, 0, 0, .04);
         color: #000 !important;
     }

     .info-card * {
         color: #000 !important;
     }

     .card-header h6 {
         font-weight: 600;
         font-size: 14px;
         color: #000 !important;
     }

     /* Amount highlight */
     .amount-highlight {
         background: #ecfeff;
         border: 1px solid #67e8f9;
         border-radius: 12px;
         padding: 14px;
         text-align: center;
         color: #000 !important;
     }

     .amount-highlight * {
         color: #000 !important;
     }

     /* Tables */
     .table,
     .table td,
     .table th {
         color: #000 !important;
     }

     /* Badge */
     .badge {
         color: #000 !important;
         background-color: #f0f0f0 !important;
         border: 1px solid #000 !important;
     }

     /* Text colors override */
     .text-primary,
     .text-success,
     .text-danger,
     .text-warning,
     .text-info {
         color: #000 !important;
     }

     /* =======================
    PRINT FIX (ONE PAGE)
 ======================= */
     @media print {

         @page {
             size: A4;
             margin: 10mm;
         }

         body {
             background: #fff !important;
             color: #000 !important;
         }

         * {
             color: #000 !important;
         }

         .receipt-container {
             box-shadow: none !important;
             transform: scale(0.94);
             transform-origin: top left;
             page-break-after: avoid;
             color: #000 !important;
         }

         .receipt-container * {
             color: #000 !important;
         }

         .card,
         .info-card,
         table,
         tr,
         td {
             page-break-inside: avoid !important;
             color: #000 !important;
         }

         .card-body {
             padding: 10px !important;
             color: #000 !important;
         }

         table {
             font-size: 13px;
             color: #000 !important;
         }

         table td,
         table th {
             color: #000 !important;
         }

         .text-primary,
         .text-success,
         .text-danger,
         .text-warning,
         .text-info,
         .text-muted {
             color: #000 !important;
         }

         .badge {
             color: #000 !important;
             background-color: #f0f0f0 !important;
             border: 1px solid #000 !important;
         }

         .receipt-header {
             background: #fff !important;
             color: #000 !important;
         }

         .amount-highlight {
             background: #f0f0f0 !important;
             border: 1px solid #000 !important;
             color: #000 !important;
         }

         .no-print {
             display: none !important;
         }

         .print-only {
             display: block !important;
             color: #000 !important;
         }
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


    <div class="receipt-container">

        <!-- HEADER -->
        <div class="receipt-header">
            <p class="fw-semibold fs-5">
                <i class="bx bx-receipt me-1"></i> Payment Receipt
            </p>
            <p>{{ $installment->receipt_number }}</p>
            <p>Investment Collection</p>
        </div>

        <div class="card-body p-3">

            <!-- MAIN ROW -->
            <div class="row g-3">

                <!-- LEFT -->
                <div class="col-6">

                    <!-- COLLECTION INFO -->
                    <div class="card info-card mb-3">
                        <div class="card-header bg-white border-0 pb-1">
                            <h6><i class="bx bx-info-circle text-primary"></i> Collection Info</h6>
                        </div>
                        <div class="card-body pt-1">
                            <table class="table table-sm table-bordered table-borderless mb-0">
                                <tr>
                                    <td>Receipt No</td>
                                    <td class="fw-semibold text-primary">
                                        {{ $installment->receipt_number }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Payment Date</td>
                                    <td>
                                        {{ $installment->paid_date->format('F d, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Installment</td>
                                    <td>
                                        #{{ $installment->installment_number }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Schedule Date</td>
                                    <td>
                                        {{ $installment->schedule_date->format('F d, Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <span class="badge bg-success">Paid</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- MEMBER INFO -->
                    <div class="card info-card">
                        <div class="card-header bg-white border-0 pb-1">
                            <h6><i class="bx bx-user text-info"></i> Member Info</h6>
                        </div>
                        <div class="card-body pt-1">
                            <table class="table table-sm table-bordered table-borderless mb-0">
                                <tr>
                                    <td>Name</td>
                                    <td>
                                        {{ $installment->investment->member->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Member ID</td>
                                    <td>
                                        {{ $installment->investment->member->unique_id ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Account</td>
                                    <td>
                                        {{ $installment->investment->account->account_number ?? 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="col-6">

                    <!-- PAYMENT BREAKDOWN -->
                    <div class="card info-card mb-3">
                        <div class="card-header bg-white border-0 pb-1">
                            <h6><i class="bx bx-money text-success"></i> Payment Breakdown</h6>
                        </div>
                        <div class="card-body pt-1">
                            <table class="table table-bordered table-sm mb-0">
                                <tr>
                                    <td>Principal</td>
                                    <td>
                                    ৳{{ number_format($installment->principal_amount, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Interest</td>
                                    <td>
                                    ৳{{ number_format($installment->rent, 2) }}
                                    </td>
                                </tr>

                                @if($installment->fine_amount > 0)
                                <tr class="text-danger">
                                    <td>Late Fee</td>
                                    <td class="text-end">
                                    ৳{{ number_format($installment->fine_amount, 2) }}
                                    </td>
                                </tr>
                                @endif

                                <tr class="border-top fw-bold">
                                    <td>Total</td>
                                    <td>
                                    ৳{{ number_format($installment->total_amount, 2) }}
                                    </td>
                                </tr>

                                @if($installment->discount_amount > 0)
                                <tr class="text-success">
                                    <td>Discount</td>
                                    <td>
                                        -৳{{ number_format($installment->discount_amount, 2) }}
                                    </td>
                                </tr>
                                @endif
                                <tr class="border-top fw-bold">
                                    <td>Net Amount</td>
                                    <td>
                                    ৳{{ number_format($installment->total_amount - ($installment->discount_amount ?? 0), 2) }}
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>Payment Method</td>
                                    <td>
                                        {{ $installment->paymentMethod->payment_method_name ?? 'N/A' }}
                                    </td>
                                </tr>
                                @if($installment->bank_name)
                                <tr>
                                    <td>Bank</td>
                                    <td>{{ $installment->bank_name }}</td>
                                </tr>
                                @endif
                                @if($installment->transaction_reference)
                                <tr>
                                    <td>Reference</td>
                                    <td>{{ $installment->transaction_reference }}</td>
                                </tr>
                                @endif  
                            </table>
                        </div>
                    </div>


                    <!-- PAYMENT METHOD -->


                </div>
            </div>

            <!-- FOOTER -->
            <div class="row mt-3 print-only" style="display:none">
                <div class="col-12 text-center   small">
                    This is a computer generated receipt.<br>
                    Generated on {{ date('F d, Y h:i A') }}
                </div>
            </div>

            <!-- ACTIONS -->
            <div class="row mt-3 no-print">
                <div class="col-12 d-flex justify-content-between">
                    <a href="{{ route('investments.view-collection') }}" class="btn btn-outline-secondary btn-sm">
                        ← Back
                    </a>
                    <div>
                        <a href="{{ route('investments.collection.edit', $installment) }}" class="btn btn-primary btn-sm">
                            Edit
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="deleteCollection()">
                            Reverse
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Receipt Container -->

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
        Swal.fire({
            title: 'Reverse Payment?',
            html: `<p>Are you sure you want to reverse this payment?</p>
                   <p><strong>Receipt:</strong> {{ $installment->receipt_number }}</p>
                   <p class="text-danger">This action will mark the installment as pending again and reverse all related transactions.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reverse it!',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('{{ route("investments.collection.destroy", $installment) }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to reverse payment');
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(error.message || 'An error occurred while reversing the payment');
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    icon: 'success',
                    title: 'Reversed!',
                    text: result.value.message || 'Payment has been reversed successfully.',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    timer: 2000,
                    showConfirmButton: true
                }).then(() => {
                    window.location.href = '{{ route("investments.view-collection") }}';
                });
            }
        });
    }
</script>
@endsection