@extends('layouts.contentNavbarLayout')

@section('title', 'Process Payment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Process Payment - Installment #{{ $installment->installment_number }}</h5>
                    <a href="{{ route('investments.payments.index', $investment) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to Payments
                    </a>
                </div>
                <div class="card-body">
                    <!-- Installment Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Installment Details</h6>
                            <table class="table table-borderless table-sm table-bordered table-hover">
                                <tr>
                                    <td><strong>Installment Number:</strong></td>
                                    <td>#{{ $installment->installment_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Schedule Date:</strong></td>
                                    <td>{{ $installment->schedule_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Principal Amount:</strong></td>
                                    <td>${{ number_format($installment->principal_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Rent (Interest):</strong></td>
                                    <td>${{ number_format($installment->rent, 2) }}</td>
                                </tr>
                                @if($daysLate > 0)
                                    <tr>
                                        <td><strong>Days Late:</strong></td>
                                        <td><span class="text-danger">{{ $daysLate }} days</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fine Amount:</strong></td>
                                        <td><span class="text-danger">${{ number_format($fine, 2) }}</span></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td class="fw-semibold">${{ number_format($installment->principal_amount + $installment->rent + $fine, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Investment Information</h6>
                            <table class="table table-borderless table-sm table-bordered table-hover">
                                <tr>
                                    <td><strong>Investment ID:</strong></td>
                                    <td>#{{ $investment->getKey() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Member:</strong></td>
                                    <td>{{ $investment->member->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Interest Rate:</strong></td>
                                    <td>{{ number_format($investment->rate_percentage, 2) }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="paymentForm" action="{{ route('investments.payments.store', [$investment, $installment->id]) }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <!-- Paid Date -->
                            <div class="col-md-6">
                                <label for="paid_date" class="form-label">Paid Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('paid_date') is-invalid @enderror" 
                                       id="paid_date" 
                                       name="paid_date" 
                                       value="{{ old('paid_date', date('Y-m-d')) }}" 
                                       required>
                                <div class="invalid-feedback"></div>
                                @error('paid_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Base Amount Display (Principal + Rent + Fine) -->
                            <div class="col-md-6">
                                <label class="form-label">Base Amount (Principal + Rent + Fine)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="base_amount_display" 
                                           value="{{ number_format($installment->principal_amount + $installment->rent + $fine, 2) }}" 
                                           readonly 
                                           style="background-color: #f8f9fa;">
                                </div>
                                <small class="form-text text-muted">
                                    Principal: ${{ number_format($installment->principal_amount, 2) }} + 
                                    Rent: ${{ number_format($installment->rent, 2) }} + 
                                    Fine: $<span id="fine_in_base">{{ number_format($fine, 2) }}</span>
                                </small>
                            </div>

                            <!-- Fine Display (Auto-calculated) -->
                            <div class="col-md-6">
                                <label class="form-label">Fine Amount (Auto-calculated)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="fine_amount_display" 
                                           value="{{ number_format($fine, 2) }}" 
                                           readonly 
                                           style="background-color: #f8f9fa;">
                                </div>
                                <small class="form-text text-muted" id="days_late_display">
                                    @if($daysLate > 0)
                                        <span class="text-danger">{{ $daysLate }} days late</span>
                                    @else
                                        <span class="text-success">No fine (payment on time)</span>
                                    @endif
                                </small>
                            </div>

                            <!-- Discount Amount -->
                            <div class="col-md-6">
                                <label for="discount_amount" class="form-label">Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control @error('discount_amount') is-invalid @enderror" 
                                           id="discount_amount" 
                                           name="discount_amount" 
                                           value="{{ old('discount_amount', 0) }}" 
                                           step="0.01" 
                                           min="0" 
                                           placeholder="0.00">
                                </div>
                                <div class="invalid-feedback"></div>
                                @error('discount_amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Net Paid Amount (Auto-calculated) -->
                            <div class="col-md-6">
                                <label for="paid_amount" class="form-label">Net Paid Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control @error('paid_amount') is-invalid @enderror" 
                                           id="paid_amount" 
                                           name="paid_amount" 
                                           value="{{ old('paid_amount', number_format($installment->principal_amount + $installment->rent + $fine, 2)) }}" 
                                           step="0.01" 
                                           min="0" 
                                           required
                                           readonly
                                           style="background-color: #e7f3ff;">
                                </div>
                                <small class="form-text text-muted">
                                    <span id="net_amount_calculation">Base Amount - Discount = Net Paid</span>
                                </small>
                                <div class="invalid-feedback"></div>
                                @error('paid_amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="Additional notes about this payment">{{ old('notes') }}</textarea>
                                <div class="invalid-feedback"></div>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('investments.payments.index', $investment) }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary">
                                        <span class="spinner-border spinner-border-sm d-none me-2" id="submitSpinner" role="status" aria-hidden="true"></span>
                                        <i class="bx bx-money me-1" id="submitIcon"></i> 
                                        <span id="submitText">Process Payment</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paymentForm');
    const paidDateInput = document.getElementById('paid_date');
    const paidAmountInput = document.getElementById('paid_amount');
    const discountAmountInput = document.getElementById('discount_amount');
    const fineDisplay = document.getElementById('fine_amount_display');
    const fineInBase = document.getElementById('fine_in_base');
    const baseAmountDisplay = document.getElementById('base_amount_display');
    const daysLateDisplay = document.getElementById('days_late_display');
    const netAmountCalculation = document.getElementById('net_amount_calculation');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');

    const scheduleDate = '{{ $installment->schedule_date->format("Y-m-d") }}';
    const principalAmount = {{ $installment->principal_amount }};
    const rent = {{ $installment->rent }};
    const interestRate = {{ $investment->rate }};

    // Calculate fine when paid date changes
    paidDateInput.addEventListener('change', function() {
        calculateFine();
    });

    // Calculate net paid amount when discount changes
    discountAmountInput.addEventListener('input', function() {
        calculateNetPaidAmount();
    });

    // Also calculate when fine is updated
    function calculateNetPaidAmount() {
        const baseAmount = parseFloat(baseAmountDisplay.value.replace(/,/g, '')) || 0;
        const discount = parseFloat(discountAmountInput.value) || 0;
        const netPaid = Math.max(0, baseAmount - discount);
        
        paidAmountInput.value = netPaid.toFixed(2);
        netAmountCalculation.textContent = 
            `$${baseAmount.toFixed(2)} - $${discount.toFixed(2)} = $${netPaid.toFixed(2)}`;
    }

    function calculateFine() {
        const paidDate = paidDateInput.value;
        
        if (!paidDate) {
            return;
        }

        // Show loading
        fineDisplay.value = 'Calculating...';
        daysLateDisplay.innerHTML = '<span class="text-muted">Calculating...</span>';

        $.ajax({
            url: '{{ route("investments.payments.calculate-fine", [$investment, $installment->id]) }}',
            type: 'POST',
            data: {
                paid_date: paidDate,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const fine = parseFloat(data.fine);
                    fineDisplay.value = '$' + fine.toFixed(2);
                    fineInBase.textContent = fine.toFixed(2);
                    
                    if (data.days_late > 0) {
                        daysLateDisplay.innerHTML = '<span class="text-danger">' + data.days_late + ' days late</span>';
                    } else {
                        daysLateDisplay.innerHTML = '<span class="text-success">No fine (payment on time)</span>';
                    }

                    // Update base amount (principal + rent + fine)
                    const baseAmount = parseFloat(data.base_amount);
                    baseAmountDisplay.value = baseAmount.toFixed(2);
                    
                    // Recalculate net paid amount with current discount
                    calculateNetPaidAmount();
                }
            },
            error: function(xhr) {
                console.error('Error calculating fine:', xhr);
                fineDisplay.value = '$0.00';
                fineInBase.textContent = '0.00';
                daysLateDisplay.innerHTML = '<span class="text-danger">Error calculating fine</span>';
            }
        });
    }

    // Form submission with AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous validation errors
        form.querySelectorAll('.form-control').forEach(function(field) {
            field.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
            feedback.textContent = '';
        });

        // Show spinner and disable form
        submitSpinner.classList.remove('d-none');
        submitIcon.classList.add('d-none');
        submitText.textContent = 'Processing...';
        submitBtn.disabled = true;
        form.querySelectorAll('input, select, textarea, button').forEach(function(field) {
            field.disabled = true;
        });

        const formData = new FormData(form);

        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Payment processed successfully');
                    
                    // Redirect to payments list after 1 second
                    setTimeout(function() {
                        window.location.href = '{{ route("investments.payments.index", $investment) }}';
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to process payment');
                    resetForm();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        const input = document.getElementById(field);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentElement.querySelector('.invalid-feedback') || 
                                          input.nextElementSibling;
                            if (feedback) {
                                feedback.textContent = messages[0];
                            }
                        }
                    });
                    toastr.error('Please fix the validation errors and try again.');
                } else {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while processing the payment';
                    toastr.error(errorMessage);
                }
                resetForm();
            }
        });
    });

    function resetForm() {
        submitSpinner.classList.add('d-none');
        submitIcon.classList.remove('d-none');
        submitText.textContent = 'Process Payment';
        submitBtn.disabled = false;
        form.querySelectorAll('input, select, textarea, button').forEach(function(field) {
            field.disabled = false;
        });
    }
});
</script>
@endsection

