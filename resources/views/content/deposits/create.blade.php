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
            <div class="col-md-12 mb-3">
              <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
              <select class="select2 form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
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
   
            <!-- Monthly Deposit Amount -->
            <div class="col-md-12 mb-3">
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
              <div class="form-text">Fixed amount to deposit every month. When you select a member, the last installment amount from their monthly deposit settings is filled automatically so they can pay that amount.</div>
            </div>


    

            <!-- Start Date -->
            <div class="col-md-12 mb-3">
              <label for="deposit_date" class="form-label">Deposit Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                     id="deposit_date" name="deposit_date" value="{{ old('deposit_date', date('Y-m-d')) }}" required>
              @error('deposit_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
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
              <div class="d-flex gap-2">
             
                <button type="button" class="btn btn-primary" id="submitBtn">
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                  <i class="bx bx-save me-1" id="submitIcon"></i> 
                  <span id="submitText">Create Deposit</span>
                </button>

                <a href="{{ route('deposits.view-deposits') }}" class="btn btn-outline-secondary">
                  Cancel
                </a>
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
// URL template to fetch last installment amount (replace __ID__ with member_id)
window.lastInstallmentAmountUrl = @json(route('members.monthly-deposit-installment-settings.last-amount', ['memberId' => '__ID__']));
</script>
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
  const memberSelect = $('#member_id');

  // On member change: fetch last installment amount and populate Monthly Deposit Amount
  memberSelect.on('change', function() {
    const memberId = $(this).val();
    if (!memberId) {
      monthlyDepositInput.val('');
      return;
    }
    const url = window.lastInstallmentAmountUrl.replace('__ID__', memberId);
    $.ajax({
      url: url,
      type: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(data) {
        if (data.installment_amount != null && data.installment_amount !== '') {
          monthlyDepositInput.val(parseFloat(data.installment_amount));
          if (depositDayContainer.length && parseFloat(data.installment_amount) > 0) {
            depositDayContainer.show();
            depositDayInput.attr('required', 'required');
          }
        } else {
          monthlyDepositInput.val('');
        }
      },
      error: function() {
        monthlyDepositInput.val('');
      }
    });
  });

  // On load, if a member is already selected, fetch last installment amount
  if (memberSelect.val()) {
    memberSelect.trigger('change');
  }

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
  // Note: This logic may need to be adjusted based on your deposit type names
  $('#deposit_type_id, #start_date').on('change', function() {
    const depositTypeId = $('#deposit_type_id').val();
    const depositTypeName = $('#deposit_type_id option:selected').text().toLowerCase();
    const startDate = $('#start_date').val();
    
    if (depositTypeId && startDate) {
      const start = new Date(startDate);
      let maturityDate = new Date(start);
      
      // Check deposit type name (case-insensitive)
      if (depositTypeName.includes('savings')) {
        // No maturity date for savings
        $('#maturity_date').val('');
      } else if (depositTypeName.includes('fixed')) {
        // 1 year for fixed deposits
        maturityDate.setFullYear(maturityDate.getFullYear() + 1);
        $('#maturity_date').val(maturityDate.toISOString().split('T')[0]);
      } else if (depositTypeName.includes('recurring')) {
        // 2 years for recurring deposits
        maturityDate.setFullYear(maturityDate.getFullYear() + 2);
        $('#maturity_date').val(maturityDate.toISOString().split('T')[0]);
      } else {
        // Default: 1 year for other types
        maturityDate.setFullYear(maturityDate.getFullYear() + 1);
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
