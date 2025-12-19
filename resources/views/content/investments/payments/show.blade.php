@extends('layouts.contentNavbarLayout')

@section('title', 'Investment Payment - Banking System')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <!-- Payment Header Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bx bx-credit-card me-2"></i>Investment Payment Processing
                    </h5>
                    <a href="{{ route('investments.payments.index', $investment) }}" class="btn btn-light btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Back to Payments
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Account & Installment Details -->
                <div class="col-lg-4 mb-4">
                    <!-- Account Information Card -->
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bx bx-user me-2"></i>Account Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block">Investment Account</small>
                                <strong class="fs-6">
                                    @if($investment->account && $investment->account->account_number)
                                        {{ $investment->account->account_number }}
                                    @else
                                        #{{ $investment->getKey() }}
                                    @endif
                                </strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Member Name</small>
                                <strong>{{ $investment->member->name }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Member ID</small>
                                <strong>{{ $investment->member->unique_id ?? 'N/A' }}</strong>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Interest Rate</small>
                                <strong>{{ number_format($investment->rate_percentage, 2) }}%</strong>
                            </div>
                            @if($investment->account)
                            <div class="mb-0">
                                <small class="text-muted d-block">Current Balance</small>
                                <strong class="text-primary fs-5">৳{{ number_format($investment->account->current_balance, 2) }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Installment Details Card -->
                    <div class="card mt-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="bx bx-calendar me-2"></i>Installment Details</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Installment #</td>
                                    <td class="text-end"><strong>#{{ $installment->installment_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Due Date</td>
                                    <td class="text-end">
                                        <strong>{{ $installment->schedule_date->format('M d, Y') }}</strong>
                                        @if($daysLate > 0)
                                            <br><small class="text-danger">({{ $daysLate }} days overdue)</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Principal</td>
                                    <td class="text-end"><strong>৳{{ number_format($installment->principal_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Interest (Rent)</td>
                                    <td class="text-end"><strong>৳{{ number_format($installment->rent, 2) }}</strong></td>
                                </tr>
                                @if($fine > 0)
                                <tr>
                                    <td class="text-muted">Late Fee</td>
                                    <td class="text-end"><strong class="text-danger">৳<span id="fine_display">{{ number_format($fine, 2) }}</span></strong></td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="text-muted"><strong>Total Due</strong></td>
                                    <td class="text-end"><strong class="text-primary fs-5">৳<span id="total_due_display">{{ number_format($installment->principal_amount + $installment->rent + $fine, 2) }}</span></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Payment Form -->
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bx bx-money me-2"></i>Payment Transaction</h6>
                        </div>
                        <div class="card-body">
                            <form id="paymentForm" action="{{ route('investments.payments.store', [$investment, $installment->id]) }}" method="POST">
                                @csrf
                                
                                <!-- Payment Method Section -->
                                <div class="mb-4">
                                    <h6 class="mb-3 text-muted border-bottom pb-2">
                                        <i class="bx bx-credit-card me-2"></i>Payment Method
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="payment_method_id" class="form-label">
                                                Payment Method <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('payment_method_id') is-invalid @enderror" 
                                                    id="payment_method_id" 
                                                    name="payment_method_id" 
                                                    required>
                                                <option value="">-- Select Payment Method --</option>
                                                @foreach($paymentMethods as $method)
                                                    <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                                        {{ $method->payment_method_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback"></div>
                                            @error('payment_method_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Conditional Fields Based on Payment Method -->
                                    <div id="payment_method_fields" class="mt-3" style="display: none;">
                                        <div class="row g-3">
                                            <!-- Bank Name (for Check/Bank Transfer) -->
                                            <div class="col-md-6" id="bank_name_field" style="display: none;">
                                                <label for="bank_name" class="form-label">Bank Name</label>
                                                <input type="text" 
                                                       class="form-control @error('bank_name') is-invalid @enderror" 
                                                       id="bank_name" 
                                                       name="bank_name" 
                                                       value="{{ old('bank_name') }}" 
                                                       placeholder="Enter bank name">
                                                <div class="invalid-feedback"></div>
                                                @error('bank_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Check Number (for Check payments) -->
                                            <div class="col-md-6" id="check_number_field" style="display: none;">
                                                <label for="check_number" class="form-label">Check Number</label>
                                                <input type="text" 
                                                       class="form-control @error('check_number') is-invalid @enderror" 
                                                       id="check_number" 
                                                       name="check_number" 
                                                       value="{{ old('check_number') }}" 
                                                       placeholder="Enter check number">
                                                <div class="invalid-feedback"></div>
                                                @error('check_number')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transaction Details Section -->
                                <div class="mb-4">
                                    <h6 class="mb-3 text-muted border-bottom pb-2">
                                        <i class="bx bx-receipt me-2"></i>Transaction Details
                                    </h6>
                                    <div class="row g-3">
                                        <!-- Payment Date -->
                                        <div class="col-md-6">
                                            <label for="paid_date" class="form-label">
                                                Payment Date <span class="text-danger">*</span>
                                            </label>
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

                                        <!-- Transaction Reference -->
                                        <div class="col-md-6">
                                            <label for="transaction_reference" class="form-label">
                                                Transaction Reference
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('transaction_reference') is-invalid @enderror" 
                                                   id="transaction_reference" 
                                                   name="transaction_reference" 
                                                   value="{{ old('transaction_reference') }}" 
                                                   placeholder="e.g., TXN-123456789">
                                            <small class="text-muted">Optional: Bank transaction ID, reference number, etc.</small>
                                            <div class="invalid-feedback"></div>
                                            @error('transaction_reference')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Amount Section -->
                                <div class="mb-4">
                                    <h6 class="mb-3 text-muted border-bottom pb-2">
                                        <i class="bx bx-calculator me-2"></i>Payment Amount
                                    </h6>
                                    <div class="row g-3">
                                        <!-- Base Amount (Read-only) -->
                                        <div class="col-md-6">
                                            <label class="form-label">Base Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">৳</span>
                                                <input type="text" 
                                                       class="form-control bg-light" 
                                                       id="base_amount_display" 
                                                       value="{{ number_format($installment->principal_amount + $installment->rent + $fine, 2) }}" 
                                                       readonly>
                                            </div>
                                            <small class="text-muted">
                                                Principal: ৳{{ number_format($installment->principal_amount, 2) }} + 
                                                Rent: ৳{{ number_format($installment->rent, 2) }} + 
                                                Fine: ৳<span id="fine_in_base">{{ number_format($fine, 2) }}</span>
                                            </small>
                                        </div>

                                        <!-- Discount Amount -->
                                        <div class="col-md-6">
                                            <label for="discount_amount" class="form-label">Discount Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">৳</span>
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
                                        <div class="col-md-12">
                                            <label for="paid_amount" class="form-label">
                                                Net Payment Amount <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-primary text-white">৳</span>
                                                <input type="number" 
                                                       class="form-control @error('paid_amount') is-invalid @enderror" 
                                                       id="paid_amount" 
                                                       name="paid_amount" 
                                                       value="{{ old('paid_amount', number_format($installment->principal_amount + $installment->rent + $fine, 2)) }}" 
                                                       step="0.01" 
                                                       min="0" 
                                                       required
                                                       readonly
                                                       style="background-color: #e7f3ff; font-size: 1.25rem; font-weight: bold;">
                                            </div>
                                            <small class="text-muted">
                                                <span id="net_amount_calculation">Base Amount - Discount = Net Payment</span>
                                            </small>
                                            <div class="invalid-feedback"></div>
                                            @error('paid_amount')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Receipt Preview -->
                                <div class="mb-4">
                                    <div class="alert alert-info d-flex align-items-center">
                                        <i class="bx bx-info-circle me-2 fs-4"></i>
                                        <div>
                                            <strong>Receipt Number:</strong> Will be auto-generated upon payment confirmation
                                            <br><small>Format: RCP-YYYYMMDD-XXXXXX</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Notes -->
                                <div class="mb-4">
                                    <label for="notes" class="form-label">
                                        <i class="bx bx-note me-2"></i>Additional Notes
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" 
                                              name="notes" 
                                              rows="3" 
                                              placeholder="Enter any additional notes or remarks about this payment...">{{ old('notes') }}</textarea>
                                    <div class="invalid-feedback"></div>
                                    @error('notes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Form Actions -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="{{ route('investments.payments.index', $investment) }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-x me-1"></i> Cancel
                                    </a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                        <span class="spinner-border spinner-border-sm d-none me-2" id="submitSpinner" role="status" aria-hidden="true"></span>
                                        <i class="bx bx-check-circle me-1" id="submitIcon"></i> 
                                        <span id="submitText">Process Payment</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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
    const fineDisplay = document.getElementById('fine_display');
    const fineInBase = document.getElementById('fine_in_base');
    const baseAmountDisplay = document.getElementById('base_amount_display');
    const totalDueDisplay = document.getElementById('total_due_display');
    const netAmountCalculation = document.getElementById('net_amount_calculation');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');
    const paymentMethodSelect = document.getElementById('payment_method_id');
    const paymentMethodFields = document.getElementById('payment_method_fields');
    const bankNameField = document.getElementById('bank_name_field');
    const checkNumberField = document.getElementById('check_number_field');

    const scheduleDate = '{{ $installment->schedule_date->format("Y-m-d") }}';
    const principalAmount = {{ $installment->principal_amount }};
    const rent = {{ $installment->rent }};
    const interestRate = {{ $investment->rate }};

    // Payment method change handler
    paymentMethodSelect.addEventListener('change', function() {
        const selectedMethod = this.options[this.selectedIndex].text.toLowerCase();
        paymentMethodFields.style.display = 'block';
        
        // Show/hide fields based on payment method
        if (selectedMethod.includes('check') || selectedMethod.includes('cheque')) {
            bankNameField.style.display = 'block';
            checkNumberField.style.display = 'block';
            document.getElementById('bank_name').required = true;
            document.getElementById('check_number').required = true;
        } else if (selectedMethod.includes('bank') || selectedMethod.includes('transfer')) {
            bankNameField.style.display = 'block';
            checkNumberField.style.display = 'none';
            document.getElementById('bank_name').required = true;
            document.getElementById('check_number').required = false;
        } else {
            bankNameField.style.display = 'none';
            checkNumberField.style.display = 'none';
            document.getElementById('bank_name').required = false;
            document.getElementById('check_number').required = false;
        }
    });

    // Calculate fine when paid date changes
    paidDateInput.addEventListener('change', function() {
        calculateFine();
    });

    // Calculate net paid amount when discount changes
    discountAmountInput.addEventListener('input', function() {
        calculateNetPaidAmount();
    });

    // Calculate net paid amount
    function calculateNetPaidAmount() {
        const baseAmount = parseFloat(baseAmountDisplay.value.replace(/,/g, '')) || 0;
        const discount = parseFloat(discountAmountInput.value) || 0;
        const netPaid = Math.max(0, baseAmount - discount);
        
        paidAmountInput.value = netPaid.toFixed(2);
        netAmountCalculation.textContent = 
            `$${baseAmount.toFixed(2)} - $${discount.toFixed(2)} = $${netPaid.toFixed(2)}`;
    }

    // Calculate fine based on paid date
    function calculateFine() {
        const paidDate = paidDateInput.value;
        
        if (!paidDate) {
            return;
        }

        // Show loading
        if (fineDisplay) fineDisplay.textContent = 'Calculating...';
        if (fineInBase) fineInBase.textContent = 'Calculating...';

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
                    
                    if (fineDisplay) fineDisplay.textContent = fine.toFixed(2);
                    if (fineInBase) fineInBase.textContent = fine.toFixed(2);
                    
                    // Update base amount (principal + rent + fine)
                    const baseAmount = parseFloat(data.base_amount);
                    baseAmountDisplay.value = baseAmount.toFixed(2);
                    if (totalDueDisplay) totalDueDisplay.textContent = baseAmount.toFixed(2);
                    
                    // Recalculate net paid amount with current discount
                    calculateNetPaidAmount();
                }
            },
            error: function(xhr) {
                console.error('Error calculating fine:', xhr);
                if (fineDisplay) fineDisplay.textContent = '0.00';
                if (fineInBase) fineInBase.textContent = '0.00';
            }
        });
    }

    // Form submission with AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous validation errors
        form.querySelectorAll('.form-control, .form-select').forEach(function(field) {
            field.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
            feedback.textContent = '';
        });

        // Show spinner and disable form
        submitSpinner.classList.remove('d-none');
        submitIcon.classList.add('d-none');
        submitText.textContent = 'Processing Payment...';
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
                    toastr.success(response.message || 'Payment processed successfully! Receipt: ' + (response.data.receipt_number || 'N/A'));
                    
                    // Redirect to payments list after 2 seconds
                    setTimeout(function() {
                        window.location.href = '{{ route("investments.payments.index", $investment) }}';
                    }, 2000);
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
