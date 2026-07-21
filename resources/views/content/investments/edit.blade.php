@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Investment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Edit Investment</h5>
                    <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Back to Investments
                    </a>
                </div>
                <div class="card-body">
                    <form id="investment-form" action="{{ route('investments.update', $investment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Member Selection -->
                            <div class="col-md-3">
                                <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
                                <select class="form-select @error('member_id') is-invalid @enderror select2" id="member_id" name="member_id" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id', $investment->member_id) == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} ({{ $member->unique_id }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number"
                                    value="{{ $investment->account->account_number ?? 'N/A' }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="investment_type_id" class="form-label">Investment Type</label>
                                <select class="form-select @error('investment_type_id') is-invalid @enderror select2" id="investment_type_id" name="investment_type_id">
                                    <option value="">Select Investment Type</option>
                                    @foreach($investmentTypes as $investmentType)
                                    <option value="{{ $investmentType->id }}" {{ old('investment_type_id') == $investmentType->id ? 'selected' : '' }}>
                                        {{ $investmentType->investment_type_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('investment_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 hpsm-only">
                                <label for="calculation_method" class="form-label">
                                    Calculation Method <span class="text-danger">*</span>
                                </label>

                                <select class="form-select" id="calculation_method" name="calculation_method">
                                    <option value="">Select Calculation Method</option>
                                    <option {{ old('calculation_method', $investment->calculation_method) == 'annuity' ? 'selected' : '' }} value="annuity">Annuity</option>
                                    <option {{ old('calculation_method', $investment->calculation_method) == 'reducing' ? 'selected' : '' }}      value="reducing">Reducing Balance</option>
                                </select>
                            </div>

                            <!-- Principal Amount -->
                            <div class="col-md-3">
                                <label for="principal_amount" class="form-label">Principal Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input placeholder="Enter Principal Amount" type="number" class="form-control @error('principal_amount') is-invalid @enderror"
                                        id="principal_amount" name="principal_amount" value="{{ old('principal_amount', $investment->principal_amount) }}"
                                        step="0.01" min="0" required>
                                </div>
                                @error('principal_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-3">
                                <label for="account_opening_date" class="form-label">Account Opening Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('account_opening_date') is-invalid @enderror"
                                    id="account_opening_date" name="account_opening_date"
                                    value="{{ old('account_opening_date', $investment->account->account_opening_date ?? $investment->start_date) }}" required>
                                @error('account_opening_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date" value="{{ old('start_date', $investment->start_date->format('Y-m-d')) }}" required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="gestation_maturity_date" class="form-label">Gestation Maturity Date</label>
                                <input type="date" class="form-control @error('gestation_maturity_date') is-invalid @enderror"
                                    id="gestation_maturity_date" name="gestation_maturity_date"
                                    value="{{ old('gestation_maturity_date', $investment->account->account_closing_date ?? '') }}">
                                @error('gestation_maturity_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Interest Rate -->
                            <div class="col-md-3">
                                <label for="rate" class="form-label">Interest Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input placeholder="Enter Interest Rate" type="number" class="form-control @error('rate') is-invalid @enderror"
                                        id="rate" name="interest_rate" value="{{ old('interest_rate', $investment->rate_percentage) }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="form-text text-muted">Enter as decimal (e.g., 0.15 for 15%)</small>
                                @error('interest_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="investment_years" class="form-label">Investment Years <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter Investment Years" min="2" max="20"
                                    class="form-control @error('investment_years') is-invalid @enderror"
                                    id="investment_years" name="investment_years"
                                    value="{{ old('investment_years', $investmentYears) }}" required>
                                @error('investment_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="monthly" {{ old('payment_type', $paymentType) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('payment_type', $paymentType) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="yearly" {{ old('payment_type', $paymentType) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    <option value="daily" {{ old('payment_type', $paymentType) == 'daily' ? 'selected' : '' }}>Daily</option>
                                </select>
                                @error('payment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', $investment->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="matured" {{ old('status', $investment->status) == 'matured' ? 'selected' : '' }}>Matured</option>
                                    <option value="closed" {{ old('status', $investment->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <h6 class="mt-4 mb-3">Probable Installment Breakup</h6>
                            <div class="col-md-3">
                                <label for="no_of_installments" class="form-label">No of Installments</label>
                                <input type="number" min="2" class="form-control" id="no_of_installments" name="no_of_installments"
                                    value="{{ old('no_of_installments', $noOfInstallments) }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="principal_amount_per_installment" class="form-label">Principal Amount (Per Installment)</label>
                                <input type="number" class="form-control" id="principal_amount_per_installment"
                                    name="principal_amount_per_installment"
                                    value="{{ old('principal_amount_per_installment', $principalPerInstallment) }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="rent" class="form-label">Rent (Per Installment)</label>
                                <input type="number" class="form-control" id="rent" name="rent"
                                    value="{{ old('rent', $rentPerInstallment) }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="total_amount" class="form-label">Total Amount (Per Installment)</label>
                                <input type="number" class="form-control" id="total_amount" name="total_amount"
                                    value="{{ old('total_amount', $totalAmountPerInstallment) }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="total_rent" class="form-label">Total Rent</label>
                                <input type="number" class="form-control" id="total_rent" name="total_rent"
                                    value="{{ old('total_rent', $totalRent) }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                    id="notes" name="notes" rows="3"
                                    placeholder="Additional notes about this investment">{{ old('notes', $investment->notes) }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" id="submit-btn" class="btn btn-primary">
                                        <span id="submit-text">
                                            <i class="bx bx-save me-1"></i> Update Investment
                                        </span>
                                        <span id="submit-spinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                            Updating...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Success/Error Messages -->
                    <div id="form-messages" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Same calculation logic as create form
        function calculateInstallments() {
            const principalAmount = parseFloat($('#principal_amount').val()) || 0;
            let interestRate = parseFloat($('#rate').val()) || 0;
            const investmentYears = parseFloat($('#investment_years').val()) || 0;
            const paymentType = $('#payment_type').val();

            if (!principalAmount || !interestRate || !investmentYears || !paymentType) {
                return;
            }

            if (interestRate > 1) {
                interestRate = interestRate / 100;
            }

            let noOfInstallments = 0;
            if (paymentType === 'monthly') {
                noOfInstallments = investmentYears * 12;
            } else if (paymentType === 'quarterly') {
                noOfInstallments = investmentYears * 4;
            } else if (paymentType === 'yearly') {
                noOfInstallments = investmentYears;
            } else if (paymentType === 'daily') {
                noOfInstallments = investmentYears * 365;
            } else {
                noOfInstallments = investmentYears * 12;
            }

            const principalAmountPerInstallment = Math.round(principalAmount / noOfInstallments);
            let rentPerInstallment = 0;

            if (paymentType === 'monthly') {
                const monthlyRate = interestRate / 12;
                rentPerInstallment = Math.round(principalAmount * monthlyRate * 0.509);
            } else if (paymentType === 'quarterly') {
                const quarterlyRate = interestRate / 4;
                rentPerInstallment = Math.round(principalAmount * quarterlyRate * 0.509);
            } else if (paymentType === 'yearly') {
                rentPerInstallment = Math.round(principalAmount * interestRate * 0.509);
            } else if (paymentType === 'daily') {
                const dailyRate = interestRate / 365;
                rentPerInstallment = Math.round(principalAmount * dailyRate * 0.509);
            } else {
                const monthlyRate = interestRate / 12;
                rentPerInstallment = Math.round(principalAmount * monthlyRate * 0.509);
            }

            const totalAmountPerInstallment = principalAmountPerInstallment + rentPerInstallment;
            const totalRent = rentPerInstallment * noOfInstallments;

            $('#no_of_installments').val(noOfInstallments);
            $('#principal_amount_per_installment').val(principalAmountPerInstallment);
            $('#rent').val(rentPerInstallment);
            $('#total_amount').val(totalAmountPerInstallment);
            $('#total_rent').val(totalRent);
        }

        $('#investment_years').on('input', calculateInstallments);
        $('#payment_type').on('change', calculateInstallments);
        $('#principal_amount').on('input change', calculateInstallments);
        $('#rate').on('input change', calculateInstallments);

        // AJAX Form Submission
        $('#investment-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = form.serialize();
            const submitBtn = $('#submit-btn');
            const submitText = $('#submit-text');
            const submitSpinner = $('#submit-spinner');
            const messages = $('#form-messages');

            submitBtn.prop('disabled', true);
            submitText.addClass('d-none');
            submitSpinner.removeClass('d-none');
            messages.html('');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    submitText.removeClass('d-none');
                    submitSpinner.addClass('d-none');

                    if (response.success) {
                        messages.html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<strong>Success!</strong> ' + (response.message || 'Investment updated successfully.') +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');

                        setTimeout(function() {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.href = '{{ route("investments.view-investments") }}';
                            }
                        }, 2000);
                    } else {
                        submitBtn.prop('disabled', false);
                        messages.html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong>Error!</strong> ' + (response.message || 'Failed to update investment.') +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                    }
                },
                error: function(xhr) {
                    submitText.removeClass('d-none');
                    submitSpinner.addClass('d-none');
                    submitBtn.prop('disabled', false);

                    let errorMessage = 'An error occurred while updating the investment.';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            let errorList = '<ul class="mb-0">';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorList += '<li>' + value[0] + '</li>';
                            });
                            errorList += '</ul>';
                            errorMessage = errorList;
                        }
                    }

                    messages.html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<strong>Error!</strong> ' + errorMessage +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                }
            });
        });
    });
</script>
@endsection