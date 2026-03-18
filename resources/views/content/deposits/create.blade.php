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
              <label for="deposit_amount" class="form-label">Monthly Deposit Amount</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('deposit_amount') is-invalid @enderror" 
                       id="deposit_amount" name="deposit_amount" value="{{ old('deposit_amount') }}" 
                       step="0.01" min="0" placeholder="0.00">
              </div>
              @error('deposit_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">Amount to deposit.</div>
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
  const depositAmountInput = $('#deposit_amount');
  const memberSelect = $('#member_id');

  // On member change: fetch last installment amount and populate Monthly Deposit Amount
  memberSelect.on('change', function() {
    const memberId = $(this).val();
    if (!memberId) {
      depositAmountInput.val('');
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
          depositAmountInput.val(parseFloat(data.installment_amount));
        } else {
          depositAmountInput.val('');
        }
      },
      error: function() {
        depositAmountInput.val('');
      }
    });
  });

  // On load, if a member is already selected, fetch last installment amount
  if (memberSelect.val()) {
    memberSelect.trigger('change');
  }

  // Show/hide deposit day field based on monthly deposit amount
  depositAmountInput.on('input', function() {
    if (this.value && parseFloat(this.value) > 0) {
      depositAmountInput.attr('required', 'required');
    } else {
      depositAmountInput.removeAttr('required');
    }
  });

  // Initialize on page load
  if (depositAmountInput.val() && parseFloat(depositAmountInput.val()) > 0) {
    depositAmountInput.attr('required', 'required');
  }

  // Auto-calculate maturity date based on deposit type
  // Note: This logic may need to be adjusted based on your deposit type names

  // Reset submit button to ready state (used after error)
  function resetSubmitButton() {
    $('#submitBtn').prop('disabled', false);
    $('#submitSpinner').addClass('d-none');
    $('#submitIcon').removeClass('d-none');
    $('#submitText').text('Create Deposit');
  }

  // Function to handle form submission via AJAX
  function submitFormViaAjax() {
    const form = $('#depositForm');
    const submitBtn = $('#submitBtn');
    const submitSpinner = $('#submitSpinner');
    const submitIcon = $('#submitIcon');
    const submitText = $('#submitText');

    // Basic validation
    if (!form[0].checkValidity()) {
      form[0].reportValidity();
      return false;
    }

    // Prevent double submit
    if (submitBtn.prop('disabled')) {
      return false;
    }

    // Disable button, show spinner, change text
    submitBtn.prop('disabled', true);
    submitSpinner.removeClass('d-none');
    submitIcon.addClass('d-none');
    submitText.text('Saving…');

    // Clear previous error messages
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('.alert').remove();

    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: form.serialize(),
      dataType: 'json',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(response) {
        if (response.success) {
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Deposit created successfully');
          } else {
            alert(response.message || 'Deposit created successfully');
          }
          // Keep spinner visible until redirect
          setTimeout(function() {
            if (response.data && response.data.id) {
              window.location.href = '{{ route("deposits.view-deposits") }}';
            } else {
              window.location.href = '{{ route("deposits.view-deposits") }}';
            }
          }, 800);
        } else {
          if (typeof toastr !== 'undefined') {
            toastr.error(response.message || 'Failed to create deposit');
          } else {
            alert(response.message || 'Failed to create deposit');
          }
          resetSubmitButton();
        }
      },
      error: function(xhr, status, error) {
        resetSubmitButton();

        var responseJSON = null;
        try {
          responseJSON = xhr.responseJSON;
        } catch (e) {}

        if (xhr.status === 422 && responseJSON && responseJSON.errors) {
          var errors = responseJSON.errors;
          var errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
          errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
          errorHtml += '<strong>Please fix the following errors:</strong><ul class="mb-0 mt-2">';

          $.each(errors, function(field, messages) {
            var fieldElement = $('[name="' + field + '"]');
            fieldElement.addClass('is-invalid');
            var msg = Array.isArray(messages) ? messages.join(' ') : messages;
            fieldElement.siblings('.invalid-feedback').remove();
            fieldElement.after('<div class="invalid-feedback">' + msg.trim() + '</div>');
            errorHtml += '<li>' + msg.trim() + '</li>';
          });
          errorHtml += '</ul></div>';
          form.prepend(errorHtml);
          $('html, body').animate({ scrollTop: 0 }, 500);
        } else {
          var errorMessage = (responseJSON && responseJSON.message)
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
