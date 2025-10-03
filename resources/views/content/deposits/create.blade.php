@extends('layouts/contentNavbarLayout')

@section('title', 'Deposits - Add New')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add New Deposit</h5>
        <a href="{{ route('deposits.view-deposits') }}" class="btn btn-outline-secondary">
          <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
      </div>
      
      <div class="card-body">
        <form action="{{ route('deposits.store') }}" method="POST" id="depositForm">
          @csrf
          
          <div class="row">
            <!-- Member Selection -->
            <div class="col-md-6 mb-3">
              <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
              <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                <option value="">Select Member</option>
                @foreach($members as $member)
                  <option value="{{ $member->id }}" {{ (old('member_id') == $member->id || ($member && $member->id == $member->id)) ? 'selected' : '' }}>
                    {{ $member->name }} ({{ $member->member_unique_id }})
                  </option>
                @endforeach
              </select>
              @error('member_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Product Name -->
            <div class="col-md-6 mb-3">
              <label for="product_name" class="form-label">Product Name</label>
              <input type="text" class="form-control @error('product_name') is-invalid @enderror" 
                     id="product_name" name="product_name" value="{{ old('product_name') }}" 
                     placeholder="e.g., Savings Account, Fixed Deposit">
              @error('product_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Deposit Amount -->
            <div class="col-md-6 mb-3">
              <label for="deposit_amount" class="form-label">Deposit Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('deposit_amount') is-invalid @enderror" 
                       id="deposit_amount" name="deposit_amount" value="{{ old('deposit_amount') }}" 
                       step="0.01" min="0" required placeholder="0.00">
              </div>
              @error('deposit_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Deposit Type -->
            <div class="col-md-6 mb-3">
              <label for="deposit_type" class="form-label">Deposit Type <span class="text-danger">*</span></label>
              <select class="form-select @error('deposit_type') is-invalid @enderror" id="deposit_type" name="deposit_type" required>
                <option value="">Select Type</option>
                <option value="savings" {{ old('deposit_type') == 'savings' ? 'selected' : '' }}>Savings</option>
                <option value="fixed" {{ old('deposit_type') == 'fixed' ? 'selected' : '' }}>Fixed Deposit</option>
                <option value="recurring" {{ old('deposit_type') == 'recurring' ? 'selected' : '' }}>Recurring Deposit</option>
              </select>
              @error('deposit_type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Start Date -->
            <div class="col-md-6 mb-3">
              <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                     id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
              @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Maturity Date -->
            <div class="col-md-6 mb-3">
              <label for="maturity_date" class="form-label">Maturity Date</label>
              <input type="date" class="form-control @error('maturity_date') is-invalid @enderror" 
                     id="maturity_date" name="maturity_date" value="{{ old('maturity_date') }}">
              @error('maturity_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">Leave empty for savings accounts</div>
            </div>

            <!-- Interest Rate -->
            <div class="col-md-6 mb-3">
              <label for="rate" class="form-label">Interest Rate (%)</label>
              <div class="input-group">
                <input type="number" class="form-control @error('rate') is-invalid @enderror" 
                       id="rate" name="rate" value="{{ old('rate') }}" 
                       step="0.01" min="0" max="100" placeholder="0.00">
                <span class="input-group-text">%</span>
              </div>
              @error('rate')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">Enter as percentage (e.g., 8 for 8%)</div>
            </div>

            <!-- Notes -->
            <div class="col-12 mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control @error('notes') is-invalid @enderror" 
                        id="notes" name="notes" rows="3" 
                        placeholder="Additional notes about this deposit">{{ old('notes') }}</textarea>
              @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Form Actions -->
          <div class="row">
            <div class="col-12">
              <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('deposits.view-deposits') }}" class="btn btn-outline-secondary">
                  Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-save me-1"></i> Create Deposit
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  // Auto-calculate maturity date based on deposit type
  $('#deposit_type, #start_date').on('change', function() {
    const depositType = $('#deposit_type').val();
    const startDate = $('#start_date').val();
    
    if (depositType && startDate) {
      const start = new Date(startDate);
      let maturityDate = new Date(start);
      
      switch(depositType) {
        case 'savings':
          // No maturity date for savings
          $('#maturity_date').val('');
          break;
        case 'fixed':
          // 1 year for fixed deposits
          maturityDate.setFullYear(maturityDate.getFullYear() + 1);
          break;
        case 'recurring':
          // 2 years for recurring deposits
          maturityDate.setFullYear(maturityDate.getFullYear() + 2);
          break;
      }
      
      if (depositType !== 'savings') {
        $('#maturity_date').val(maturityDate.toISOString().split('T')[0]);
      }
    }
  });

  // Form validation
  $('#depositForm').on('submit', function(e) {
    const depositAmount = parseFloat($('#deposit_amount').val());
    const rate = parseFloat($('#rate').val());
    
    if (depositAmount <= 0) {
      e.preventDefault();
      alert('Deposit amount must be greater than 0');
      return false;
    }
    
    if (rate < 0 || rate > 100) {
      e.preventDefault();
      alert('Interest rate must be between 0 and 100');
      return false;
    }
  });
});
</script>
@endpush
