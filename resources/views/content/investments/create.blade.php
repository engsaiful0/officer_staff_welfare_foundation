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
                    <form id="investmentForm" action="{{ route('investments.store') }}" method="POST">
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
                                <div class="invalid-feedback"></div>
                                @error('member_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Product Name -->
                            <div class="col-md-6">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror" 
                                       id="product_name" name="product_name" value="{{ old('product_name') }}" 
                                       placeholder="e.g., Fixed Deposit, Savings Account">
                                <div class="invalid-feedback"></div>
                                @error('product_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Principal Amount -->
                            <div class="col-md-6">
                                <label for="principal_amount" class="form-label">Principal Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('principal_amount') is-invalid @enderror" 
                                           id="principal_amount" name="principal_amount" value="{{ old('principal_amount') }}" 
                                           step="0.01" min="0" required>
                                </div>
                                <div class="invalid-feedback"></div>
                                @error('principal_amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                <div class="invalid-feedback"></div>
                                @error('start_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Term Months -->
                            <div class="col-md-6">
                                <label for="term_months" class="form-label">Term (Months) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('term_months') is-invalid @enderror" 
                                       id="term_months" name="term_months" value="{{ old('term_months') }}" 
                                       min="1" required>
                                <div class="invalid-feedback"></div>
                                @error('term_months')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Interest Rate -->
                            <div class="col-md-6">
                                <label for="rate" class="form-label">Interest Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('rate') is-invalid @enderror" 
                                           id="rate" name="rate" value="{{ old('rate') }}" 
                                           step="0.0001" min="0" max="1" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="form-text text-muted">Enter as decimal (e.g., 0.15 for 15%)</small>
                                <div class="invalid-feedback"></div>
                                @error('rate')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Rate Period -->
                            <div class="col-md-6">
                                <label for="rate_period" class="form-label">Rate Period <span class="text-danger">*</span></label>
                                <select class="form-select @error('rate_period') is-invalid @enderror" id="rate_period" name="rate_period" required>
                                    <option value="">Select Period</option>
                                    <option value="annual" {{ old('rate_period') == 'annual' ? 'selected' : '' }}>Annual</option>
                                    <option value="monthly" {{ old('rate_period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                @error('rate_period')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Frequency -->
                            <div class="col-md-6">
                                <label for="frequency" class="form-label">Accrual Frequency <span class="text-danger">*</span></label>
                                <select class="form-select @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                                    <option value="">Select Frequency</option>
                                    <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                @error('frequency')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Additional notes about this investment">{{ old('notes') }}</textarea>
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
                                    <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">Cancel</a>
                                    <button type="submit" id="submitBtn" class="btn btn-primary">
                                        <span class="spinner-border spinner-border-sm d-none me-2" id="submitSpinner" role="status" aria-hidden="true"></span>
                                        <i class="bx bx-save me-1" id="submitIcon"></i> 
                                        <span id="submitText">Create Investment</span>
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
