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
        <form action="{{ route('deposits.store') }}" method="POST" id="depositForm" novalidate>
          @csrf
          
          <div class="row">
            <!-- Member Selection -->
            <div class="col-md-6 mb-3">
              <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
              <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                <option value="">Select Member</option>
                @foreach($members as $member)
                  <option value="{{ $member->id }}" {{ (old('member_id') == $member->id || ($member && $member->id == $member->id)) ? 'selected' : '' }}>
                    {{ $member->name }} ({{ $member->unique_id }})
                  </option>
                @endforeach
              </select>
              @error('member_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Product Name -->
            <div class="col-md-6 mb-3">
              <label for="deposit_account_number" class="form-label">Account Number</label>
              <input type="text" value="{{ $nextAccountNumber }}" class="form-control @error('deposit_account_number') is-invalid @enderror" 
                     id="deposit_account_number" name="deposit_account_number" readonly required>
            </div>
            <!-- Monthly Deposit Amount -->
            <div class="col-md-6 mb-3">
              <label for="monthly_deposit_amount" class="form-label">Monthly Deposit Amount</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('monthly_deposit_amount') is-invalid @enderror" 
                       id="monthly_deposit_amount" name="monthly_deposit_amount" value="{{ old('monthly_deposit_amount') }}" 
                       step="0.01" min="0" placeholder="0.00">
              </div>
              @error('monthly_deposit_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">Fixed amount to deposit every month (leave empty if not applicable)</div>
            </div>

            <!-- Deposit Day of Month -->
            <div class="col-md-6 mb-3" id="deposit_day_container" style="display: none;">
              <label for="deposit_day_of_month" class="form-label">Deposit Day of Month</label>
              <input type="number" class="form-control @error('deposit_day_of_month') is-invalid @enderror" 
                     id="deposit_day_of_month" name="deposit_day_of_month" value="{{ old('deposit_day_of_month', 1) }}" 
                     min="1" max="31">
              @error('deposit_day_of_month')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">Day of month when monthly deposit should be processed (1-31)</div>
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
                <button type="button" class="btn btn-primary" id="submitBtn">
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                  <i class="bx bx-save me-1" id="submitIcon"></i> 
                  <span id="submitText">Create Deposit</span>
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

@section('page-script')
<script>
// Ensure jQuery is loaded
if (typeof jQuery === 'undefined') {
  console.error('jQuery is not loaded!');
} else {
  console.log('jQuery version:', jQuery.fn.jquery);
}

jQuery(document).ready(function($) {
  console.log('Document ready - initializing deposit form handlers');
  const monthlyDepositInput = $('#monthly_deposit_amount');
  const depositDayContainer = $('#deposit_day_container');
  const depositDayInput = $('#deposit_day_of_month');

  // Show/hide deposit day field based on monthly deposit amount
  monthlyDepositInput.on('input', function() {
    if (this.value && parseFloat(this.value) > 0) {
      depositDayContainer.show();
      depositDayInput.attr('required', 'required');
    } else {
      depositDayContainer.hide();
      depositDayInput.removeAttr('required');
      depositDayInput.val('1');
    }
  });

  // Initialize on page load
  if (monthlyDepositInput.val() && parseFloat(monthlyDepositInput.val()) > 0) {
    depositDayContainer.show();
    depositDayInput.attr('required', 'required');
  }

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

  // Function to handle form submission via AJAX
  function submitFormViaAjax() {
    console.log('submitFormViaAjax called');
    const form = $('#depositForm');
    const submitBtn = $('#submitBtn');
    const submitSpinner = $('#submitSpinner');
    const submitIcon = $('#submitIcon');
    
    // Basic validation
    if (!form[0].checkValidity()) {
      form[0].reportValidity();
      return false;
    }
    
    // Disable button and show spinner
    submitBtn.prop('disabled', true);
    submitSpinner.removeClass('d-none');
    submitIcon.addClass('d-none');
    
    // Clear previous error messages
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.alert').remove();
    
    // Get form data including CSRF token
    const formData = form.serialize();
    console.log('Form data:', formData);
    console.log('Form action:', form.attr('action'));
    
    // Make AJAX request
    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: formData,
      dataType: 'json',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response.success) {
          // Show success message
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Deposit created successfully');
          } else {
            alert(response.message || 'Deposit created successfully');
          }
          
          // Redirect to show page or index
          setTimeout(function() {
            if (response.data && response.data.id) {
              window.location.href = '{{ url("/app/deposits") }}/' + response.data.id;
            } else {
              window.location.href = '{{ route("deposits.view-deposits") }}';
            }
          }, 1000);
        } else {
          // Show error message
          if (typeof toastr !== 'undefined') {
            toastr.error(response.message || 'Failed to create deposit');
          } else {
            alert(response.message || 'Failed to create deposit');
          }
          
          // Re-enable button
          submitBtn.prop('disabled', false);
          submitSpinner.addClass('d-none');
          submitIcon.removeClass('d-none');
        }
      },
      error: function(xhr, status, error) {
        // Re-enable button
        submitBtn.prop('disabled', false);
        submitSpinner.addClass('d-none');
        submitIcon.removeClass('d-none');
        
        // Check if response is JSON
        let responseJSON = null;
        try {
          responseJSON = xhr.responseJSON;
        } catch (e) {
          // Response is not JSON
        }
        
        if (xhr.status === 422 && responseJSON && responseJSON.errors) {
          // Validation errors
          const errors = responseJSON.errors;
          let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
          errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
          errorHtml += '<strong>Please fix the following errors:</strong><ul class="mb-0 mt-2">';
          
          // Display validation errors
          $.each(errors, function(field, messages) {
            const fieldElement = $('[name="' + field + '"]');
            fieldElement.addClass('is-invalid');
            
            let errorMessage = '';
            $.each(messages, function(index, message) {
              errorMessage += message + ' ';
            });
            
            // Remove existing invalid feedback for this field
            fieldElement.siblings('.invalid-feedback').remove();
            fieldElement.after('<div class="invalid-feedback">' + errorMessage.trim() + '</div>');
            errorHtml += '<li>' + errorMessage.trim() + '</li>';
          });
          
          errorHtml += '</ul></div>';
          form.prepend(errorHtml);
          
          // Scroll to top to show errors
          $('html, body').animate({
            scrollTop: 0
          }, 500);
        } else {
          // Other errors
          const errorMessage = (responseJSON && responseJSON.message) 
            ? responseJSON.message 
            : 'An error occurred while creating the deposit. Please try again.';
          
          if (typeof toastr !== 'undefined') {
            toastr.error(errorMessage);
          } else {
            alert(errorMessage);
          }
        }
      }
    });
  }

  // Handle button click - primary method
  $('#submitBtn').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('Submit button clicked');
    submitFormViaAjax();
    return false;
  });

  // Handle form submission - prevent default form submission (backup)
  $('#depositForm').on('submit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('Form submit event triggered');
    submitFormViaAjax();
    return false;
  });
  
  console.log('Deposit form AJAX handlers initialized');
});
</script>
@endsection
