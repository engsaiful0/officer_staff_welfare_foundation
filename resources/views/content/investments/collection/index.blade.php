@extends('layouts.contentNavbarLayout')

@section('title', 'Investment Collection')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <!-- Collection Header Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bx bx-collection me-2"></i>Investment Collection Form
                    </h5>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Account Selection & Installment Details -->
                <div class="col-lg-5 mb-4">
                    <!-- Account Selection Card -->
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bx bx-search me-2"></i>Select Investment Account</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="account_id" class="form-label">
                                    Investment Account <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="account_id" name="account_id" required>
                                    <option value="">-- Select Investment Account --</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" 
                                                data-account-number="{{ $account->account_number ?? 'N/A' }}"
                                                data-member-name="{{ $account->investment->member->name }}"
                                                data-member-id="{{ $account->investment->member->unique_id ?? 'N/A' }}">
                                            {{ $account->account_number ?? 'Account #' . $account->id }} - 
                                            {{ $account->investment->member->name }} 
                                            ({{ $account->investment->member->unique_id ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Select an investment account to view pending installments</small>
                            </div>

                            <!-- Account Information Display -->
                            <div id="account_info" style="display: none;">
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Account Information</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted" style="width: 40%;">Account #:</td>
                                                <td><strong id="display_account_number">-</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Member:</td>
                                                <td><strong id="display_member_name">-</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Member ID:</td>
                                                <td id="display_member_id">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Current Balance:</td>
                                                <td><strong class="text-primary" id="display_balance">-</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Installment Selection -->
                            <div id="installment_selection" style="display: none;" class="mt-3">
                                <label for="installment_id" class="form-label">
                                    Select Installment (Month) <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="installment_id" name="installment_id" required>
                                    <option value="">-- Select Installment --</option>
                                </select>
                                <small class="text-muted">Select the month/installment to collect</small>

                                <!-- Installment Details -->
                                <div id="installment_details" class="card bg-warning text-dark mt-3" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="mb-3">Installment Details</h6>
                                        <table class="table table-sm table-borderless mb-0 text-dark">
                                            <tr>
                                                <td style="width: 50%;">Installment #:</td>
                                                <td><strong id="display_installment_number">-</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Due Date:</td>
                                                <td id="display_due_date">-</td>
                                            </tr>
                                            <tr>
                                                <td>Principal:</td>
                                                <td><strong>$<span id="display_principal">0.00</span></strong></td>
                                            </tr>
                                            <tr>
                                                <td>Interest (Rent):</td>
                                                <td><strong>$<span id="display_rent">0.00</span></strong></td>
                                            </tr>
                                            <tr>
                                                <td>Fine (Estimated):</td>
                                                <td><strong class="text-danger">$<span id="display_fine">0.00</span></strong></td>
                                            </tr>
                                            <tr class="border-top">
                                                <td><strong>Total Due:</strong></td>
                                                <td><strong class="text-primary fs-5">$<span id="display_total">0.00</span></strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Payment Form -->
                <div class="col-lg-7 mb-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bx bx-money me-2"></i>Payment Transaction</h6>
                        </div>
                        <div class="card-body">
                            <form id="collectionForm" action="{{ route('investments.collection.store') }}" method="POST">
                                @csrf
                                <input type="hidden" id="form_account_id" name="account_id" value="">
                                <input type="hidden" id="form_installment_id" name="installment_id" value="">

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
                                        <div class="col-md-6">
                                            <label class="form-label">Base Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" 
                                                       class="form-control bg-light" 
                                                       id="base_amount_display" 
                                                       value="0.00" 
                                                       readonly>
                                            </div>
                                            <small class="text-muted">
                                                Principal + Rent + Fine
                                            </small>
                                        </div>

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

                                        <div class="col-md-12">
                                            <label for="paid_amount" class="form-label">
                                                Net Payment Amount <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text bg-primary text-white">$</span>
                                                <input type="number" 
                                                       class="form-control @error('paid_amount') is-invalid @enderror" 
                                                       id="paid_amount" 
                                                       name="paid_amount" 
                                                       value="0.00" 
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
                                <div class="d-flex justify-content-end pt-3 border-top">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" disabled>
                                        <span class="spinner-border spinner-border-sm d-none me-2" id="submitSpinner" role="status" aria-hidden="true"></span>
                                        <i class="bx bx-check-circle me-1" id="submitIcon"></i> 
                                        <span id="submitText">Process Collection</span>
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
    const accountSelect = document.getElementById('account_id');
    const installmentSelect = document.getElementById('installment_id');
    const paidDateInput = document.getElementById('paid_date');
    const discountAmountInput = document.getElementById('discount_amount');
    const paidAmountInput = document.getElementById('paid_amount');
    const baseAmountDisplay = document.getElementById('base_amount_display');
    const netAmountCalculation = document.getElementById('net_amount_calculation');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');
    const paymentMethodSelect = document.getElementById('payment_method_id');
    const paymentMethodFields = document.getElementById('payment_method_fields');
    const bankNameField = document.getElementById('bank_name_field');
    const checkNumberField = document.getElementById('check_number_field');
    const form = document.getElementById('collectionForm');

    let currentInstallments = [];
    let currentInstallment = null;

    // Account selection change
    accountSelect.addEventListener('change', function() {
        const accountId = this.value;
        const formAccountId = document.getElementById('form_account_id');
        formAccountId.value = accountId;

        if (accountId) {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('display_account_number').textContent = selectedOption.dataset.accountNumber;
            document.getElementById('display_member_name').textContent = selectedOption.dataset.memberName;
            document.getElementById('display_member_id').textContent = selectedOption.dataset.memberId;
            document.getElementById('account_info').style.display = 'block';

            // Load installments
            loadInstallments(accountId);
        } else {
            document.getElementById('account_info').style.display = 'none';
            document.getElementById('installment_selection').style.display = 'none';
            document.getElementById('installment_details').style.display = 'none';
            installmentSelect.innerHTML = '<option value="">-- Select Installment --</option>';
            submitBtn.disabled = true;
        }
    });

    // Load installments for selected account
    function loadInstallments(accountId) {
        installmentSelect.innerHTML = '<option value="">Loading...</option>';
        installmentSelect.disabled = true;

        $.ajax({
            url: '{{ route("investments.collection.get-installments") }}',
            type: 'GET',
            data: { account_id: accountId },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    currentInstallments = response.data.installments;
                    installmentSelect.innerHTML = '<option value="">-- Select Installment --</option>';
                    
                    currentInstallments.forEach(function(inst) {
                        const option = document.createElement('option');
                        option.value = inst.id;
                        option.textContent = `#${inst.installment_number} - ${inst.month_name} (Due: ${inst.schedule_date_formatted})`;
                        option.dataset.installment = JSON.stringify(inst);
                        installmentSelect.appendChild(option);
                    });

                    // Update account balance display
                    document.getElementById('display_balance').textContent = '$' + parseFloat(response.data.account.current_balance).toFixed(2);
                    
                    document.getElementById('installment_selection').style.display = 'block';
                    installmentSelect.disabled = false;
                }
            },
            error: function(xhr) {
                console.error('Error loading installments:', xhr);
                installmentSelect.innerHTML = '<option value="">Error loading installments</option>';
                toastr.error('Failed to load installments');
            }
        });
    }

    // Installment selection change
    installmentSelect.addEventListener('change', function() {
        const installmentId = this.value;
        const formInstallmentId = document.getElementById('form_installment_id');
        formInstallmentId.value = installmentId;

        if (installmentId) {
            const selectedOption = this.options[this.selectedIndex];
            currentInstallment = JSON.parse(selectedOption.dataset.installment);
            updateInstallmentDetails(currentInstallment);
            calculateFine();
            submitBtn.disabled = false;
        } else {
            document.getElementById('installment_details').style.display = 'none';
            resetAmounts();
            submitBtn.disabled = true;
        }
    });

    // Update installment details display
    function updateInstallmentDetails(installment) {
        document.getElementById('display_installment_number').textContent = '#' + installment.installment_number;
        document.getElementById('display_due_date').textContent = installment.schedule_date_formatted;
        document.getElementById('display_principal').textContent = parseFloat(installment.principal_amount).toFixed(2);
        document.getElementById('display_rent').textContent = parseFloat(installment.rent).toFixed(2);
        document.getElementById('display_fine').textContent = parseFloat(installment.fine_amount).toFixed(2);
        document.getElementById('display_total').textContent = parseFloat(installment.total_amount).toFixed(2);
        document.getElementById('installment_details').style.display = 'block';
    }

    // Calculate fine when paid date changes
    paidDateInput.addEventListener('change', function() {
        if (currentInstallment) {
            calculateFine();
        }
    });

    // Calculate net paid amount when discount changes
    discountAmountInput.addEventListener('input', function() {
        calculateNetPaidAmount();
    });

    // Calculate fine
    function calculateFine() {
        if (!currentInstallment || !paidDateInput.value) return;

        $.ajax({
            url: '{{ route("investments.collection.calculate-fine") }}',
            type: 'POST',
            data: {
                installment_id: currentInstallment.id,
                paid_date: paidDateInput.value,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    currentInstallment.fine_amount = data.fine;
                    currentInstallment.total_amount = data.base_amount;
                    
                    document.getElementById('display_fine').textContent = parseFloat(data.fine).toFixed(2);
                    document.getElementById('display_total').textContent = parseFloat(data.base_amount).toFixed(2);
                    
                    baseAmountDisplay.value = parseFloat(data.base_amount).toFixed(2);
                    calculateNetPaidAmount();
                }
            },
            error: function(xhr) {
                console.error('Error calculating fine:', xhr);
            }
        });
    }

    // Calculate net paid amount
    function calculateNetPaidAmount() {
        const baseAmount = parseFloat(baseAmountDisplay.value) || 0;
        const discount = parseFloat(discountAmountInput.value) || 0;
        const netPaid = Math.max(0, baseAmount - discount);
        
        paidAmountInput.value = netPaid.toFixed(2);
        netAmountCalculation.textContent = 
            `$${baseAmount.toFixed(2)} - $${discount.toFixed(2)} = $${netPaid.toFixed(2)}`;
    }

    // Reset amounts
    function resetAmounts() {
        baseAmountDisplay.value = '0.00';
        discountAmountInput.value = '0';
        paidAmountInput.value = '0.00';
        netAmountCalculation.textContent = 'Base Amount - Discount = Net Payment';
    }

    // Payment method change handler
    paymentMethodSelect.addEventListener('change', function() {
        const selectedMethod = this.options[this.selectedIndex].text.toLowerCase();
        paymentMethodFields.style.display = 'block';
        
        if (selectedMethod.includes('check') || selectedMethod.includes('cheque')) {
            bankNameField.style.display = 'block';
            checkNumberField.style.display = 'block';
        } else if (selectedMethod.includes('bank') || selectedMethod.includes('transfer')) {
            bankNameField.style.display = 'block';
            checkNumberField.style.display = 'none';
        } else {
            bankNameField.style.display = 'none';
            checkNumberField.style.display = 'none';
        }
    });

    // Form submission
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
        submitText.textContent = 'Processing...';
        submitBtn.disabled = true;
       // form.querySelectorAll('input, select, textarea, button').forEach(function(field) {
           // field.disabled = true;
        //});

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
                    toastr.success(
                        response.message || 'Collection processed successfully! Receipt: ' + (response.data.receipt_number || 'N/A'),
                        'Success',
                        {
                            timeOut: 2000,
                            progressBar: true,
                            positionClass: 'toast-top-right'
                        }
                    );
                    
                    // Redirect to view page after 1.5 seconds
                    setTimeout(function() {
                        const installmentId = response.data.installment_id || response.data.installment?.id;
                        if (installmentId) {
                            window.location.href = '{{ url("/app/investments/collection") }}/' + installmentId;
                        } else {
                            // Fallback to collections list if no ID
                            window.location.href = '{{ route("investments.view-collection") }}';
                        }
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to process collection');
                    resetForm();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
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
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while processing the collection';
                    toastr.error(errorMessage);
                }
                resetForm();
            }
        });
    });

    function resetForm() {
        submitSpinner.classList.add('d-none');
        submitIcon.classList.remove('d-none');
        submitText.textContent = 'Process Collection';
        submitBtn.disabled = false;
        form.querySelectorAll('input, select, textarea, button').forEach(function(field) {
            field.disabled = false;
        });
    }
});
</script>
@endsection


