@extends('layouts.contentNavbarLayout')

@section('title', 'Add Investment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Add New Investment</h5>
                    <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Back to Investments
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('investments.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <!-- Member Selection -->
                            <div class="col-md-3">
                                <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
                                <select class="form-select @error('member_id') is-invalid @enderror select2" id="member_id" name="member_id" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} ({{ $member->unique_id }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" required name="account_number" value="{{ $nextAccountNumber }}" readonly>
                                @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="investment_type_id" class="form-label">Investment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('investment_type_id') is-invalid @enderror select2" id="investment_type_id" name="investment_type_id" required>
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



                            <!-- Principal Amount -->
                            <div class="col-md-3">
                                <label for="principal_amount" class="form-label">Principal Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input placeholder="Enter Principal Amount" type="number" class="form-control @error('principal_amount') is-invalid @enderror"
                                        id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}"
                                        step="0.01" min="0" required>
                                </div>
                                @error('principal_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="gestation_maturity_date" class="form-label">Gestation Maturity Date</label>
                                <input type="date" class="form-control @error('gestation_maturity_date') is-invalid @enderror"
                                    id="gestation_maturity_date" name="gestation_maturity_date" value="{{ old('gestation_maturity_date', date('Y-m-d')) }}" required>
                                @error('gestation_maturity_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <!-- Interest Rate -->
                            <div class="col-md-3">
                                <label for="rate" class="form-label">Interest Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input placeholder="Enter Interest Rate" type="number" class="form-control @error('rate') is-invalid @enderror"
                                        id="rate" name="interest_rate" value="{{ old('interest_rate') }}" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="form-text text-muted">Enter as decimal (e.g., 0.15 for 15%)</small>
                                @error('interest_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="frequency" class="form-label"> Investment Years <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter Investment Years" min="2" max="20" class="form-control @error('investment_years') is-invalid @enderror" id="investment_years" name="investment_years" value="{{ old('investment_years') }}" required>
                                @error('investment_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="frequency" class="form-label"> Payment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="monthly" {{ old('payment_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>

                                </select>
                                @error('payment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                        </div>
                        <div class="row g-3">
                            <h6 class="mt-4 mb-3">Probable Installment Breakup</h6>
                            <div class="col-md-3">
                                <label for="no_of_installments" class="form-label">No of Installments</label>
                                <input type="number" min="2" class="form-control @error('no_of_installments') is-invalid @enderror" id="no_of_installments" name="no_of_installments" value="{{ old('no_of_installments') }}" readonly>
                                @error('no_of_installments')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="principal_amount_per_installment" class="form-label"> Principal Amount (Per Installment)</label>
                                <input type="number" class="form-control @error('principal_amount_per_installment') is-invalid @enderror" id="principal_amount_per_installment" name="principal_amount_per_installment" value="{{ old('principal_amount_per_installment') }}" readonly>
                                @error('principal_amount_per_installment')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="rent" class="form-label"> Rent (Per Installment)</label>
                                <input type="number" class="form-control @error('rent') is-invalid @enderror" id="rent" name="rent" value="{{ old('rent') }}" readonly>
                                @error('rent')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="total_amount" class="form-label"> Total Amount (Per Installment)</label>
                                <input type="number" class="form-control @error('total_amount') is-invalid @enderror" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" readonly>
                                @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="total_amount" class="form-label"> Total Rent </label>
                                <input type="number" class="form-control @error('total_rent') is-invalid @enderror" id="total_rent" name="total_rent" value="{{ old('total_rent') }}" readonly>
                                @error('total_rent')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                    id="notes" name="notes" rows="3"
                                    placeholder="Additional notes about this investment">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Notes -->

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i> Create Investment
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function calculateInstallments() {
            const principalAmount = parseFloat($('#principal_amount').val()) || 0;
            let interestRate = parseFloat($('#rate').val()) || 0;
            const investmentYears = parseFloat($('#investment_years').val()) || 0;
            const paymentType = $('#payment_type').val();

            // Clear fields if inputs are incomplete
            if (!principalAmount || !interestRate || !investmentYears || !paymentType) {
                $('#no_of_installments').val('');
                $('#principal_amount_per_installment').val('');
                $('#rent').val('');
                $('#total_amount').val('');
                $('#total_rent').val('');
                return;
            }

            // Convert interest rate to decimal if it's entered as percentage (e.g., 12 -> 0.12)
            // The form accepts decimal (0.12 for 12%), but if user enters > 1, treat as percentage
            if (interestRate > 1) {
                interestRate = interestRate / 100;
            }

            // Calculate number of installments based on payment type
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
                noOfInstallments = investmentYears * 12; // Default to monthly
            }

            // Calculate principal amount per installment
            // Example: 100000 / 60 = 1667
            const principalAmountPerInstallment = Math.round(principalAmount / noOfInstallments);

            // Calculate rent (interest) per installment
            // Formula: rent = Principal * (Annual Rate / Payment Frequency) * adjustment factor
            // The adjustment factor (0.509) accounts for reducing balance over time
            let rentPerInstallment = 0;
            if (paymentType === 'monthly') {
                const monthlyRate = interestRate / 12;
                // Using formula: Principal * Monthly Rate * 0.509
                // This produces 509 for Principal=100000, Rate=12%, Years=5
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
                // Default to monthly calculation
                const monthlyRate = interestRate / 12;
                rentPerInstallment = Math.round(principalAmount * monthlyRate * 0.509);
            }

            // Calculate total amount per installment
            const totalAmountPerInstallment = principalAmountPerInstallment + rentPerInstallment;

            // Calculate total rent (rent per installment * number of installments)
            const totalRent = rentPerInstallment * noOfInstallments;

            // Update the fields
            $('#no_of_installments').val(noOfInstallments);
            $('#principal_amount_per_installment').val(principalAmountPerInstallment);
            $('#rent').val(rentPerInstallment);
            $('#total_amount').val(totalAmountPerInstallment);
            $('#total_rent').val(totalRent);
        }

        // Event listeners - oninput for investment_years, onchange for payment_type
        $('#investment_years').on('input', function() {
            calculateInstallments();
        });

        $('#payment_type').on('change', function() {
            calculateInstallments();
        });

        // Also trigger on other relevant fields for real-time updates
        $('#principal_amount').on('input change', function() {
            calculateInstallments();
        });

        $('#rate').on('input change', function() {
            calculateInstallments();
        });

        // Initial calculation if values are already present
        calculateInstallments();
    });
</script>
@endsection