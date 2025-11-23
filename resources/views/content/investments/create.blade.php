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
                            <div class="col-md-6">
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



                            <!-- Principal Amount -->
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <!-- Interest Rate -->
                            <div class="col-md-6">
                                <label for="rate" class="form-label">Interest Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input placeholder="Enter Interest Rate" type="number" class="form-control @error('rate') is-invalid @enderror"
                                        id="rate" name="rate" value="{{ old('rate') }}"
                                        step="0.0001" min="0" max="1" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="form-text text-muted">Enter as decimal (e.g., 0.15 for 15%)</small>
                                @error('rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="frequency" class="form-label"> Investment Years <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter Investment Years" min="2" class="form-control @error('investment_years') is-invalid @enderror" id="investment_years" name="investment_years" value="{{ old('investment_years') }}" required>
                                @error('investment_years')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
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
                                <label for="frequency" class="form-label">No of Installments</label>
                                <input type="number" min="2" class="form-control @error('no_of_installments') is-invalid @enderror" id="no_of_installments" name="no_of_installments" value="{{ old('no_of_installments') }}" required>
                                @error('no_of_installments')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="frequency" class="form-label"> Principal Amount</label>
                                <input type="number" class="form-control @error('principal_amount') is-invalid @enderror" id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}" required>
                                @error('principal_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="frequency" class="form-label"> Rent</label>
                                <input type="number" class="form-control @error('rent') is-invalid @enderror" id="rent" name="rent" value="{{ old('rent') }}" required>
                                @error('rent')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="frequency" class="form-label"> Total Amount</label>
                                <input type="number" class="form-control @error('total_amount') is-invalid @enderror" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" required>
                                @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                id="notes" name="notes" rows="3"
                                placeholder="Additional notes about this investment">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-calculate expiry date when term months changes
        const startDateInput = document.getElementById('start_date');
        const termMonthsInput = document.getElementById('term_months');

        function calculateExpiryDate() {
            if (startDateInput.value && termMonthsInput.value) {
                const startDate = new Date(startDateInput.value);
                const expiryDate = new Date(startDate);
                expiryDate.setMonth(expiryDate.getMonth() + parseInt(termMonthsInput.value));

                // You can display this in a read-only field or alert
                console.log('Expiry Date:', expiryDate.toISOString().split('T')[0]);
            }
        }

        startDateInput.addEventListener('change', calculateExpiryDate);
        termMonthsInput.addEventListener('input', calculateExpiryDate);
    });
</script>
@endsection