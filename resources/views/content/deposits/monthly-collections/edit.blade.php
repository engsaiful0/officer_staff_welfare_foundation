@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Monthly Deposit Collection')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Monthly Deposit Collection</h5>
        <a href="{{ route('deposits.monthly-collections.index') }}" class="btn btn-outline-secondary">
          <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
      </div>
      
      <div class="card-body">
        <form action="{{ route('deposits.monthly-collections.update', $collection->id) }}" method="POST" id="collectionForm" novalidate>
          @csrf
          @method('PUT')
          
          <div class="row">
            <!-- Deposit Account Selection -->
            <div class="col-md-6 mb-3">
              <label for="deposit_id" class="form-label">Deposit Account <span class="text-danger">*</span></label>
              <select class="form-select @error('deposit_id') is-invalid @enderror" id="deposit_id" name="deposit_id" required>
                <option value="">Select Deposit Account</option>
                @foreach($deposits as $deposit)
                  <option value="{{ $deposit->id }}" data-monthly-amount="{{ $deposit->monthly_deposit_amount }}" {{ old('deposit_id', $collection->deposit_id) == $deposit->id ? 'selected' : '' }}>
                    {{ $deposit->deposit_account_number }} - {{ $deposit->member->name }} ({{ $deposit->member->unique_id }})
                  </option>
                @endforeach
              </select>
              @error('deposit_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Collection Date -->
            <div class="col-md-6 mb-3">
              <label for="collection_date" class="form-label">Collection Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('collection_date') is-invalid @enderror" 
                     id="collection_date" name="collection_date" 
                     value="{{ old('collection_date', $collection->collection_date->format('Y-m-d')) }}" required>
              @error('collection_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Amount -->
            <div class="col-md-6 mb-3">
              <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                       id="amount" name="amount" step="0.01" min="0.01" 
                       value="{{ old('amount', $collection->amount) }}" required>
              </div>
              <div class="form-text" id="monthlyAmountHint"></div>
              @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Month and Year -->
            <div class="col-md-6 mb-3">
              <label class="form-label">Month & Year</label>
              <div class="row g-2">
                <div class="col-6">
                  @php
                    $monthValue = old('month', $collection->month);
                    $yearValue = old('year', $collection->month ? date('Y', strtotime($collection->month)) : date('Y'));
                    if ($monthValue && strpos($monthValue, ' ') !== false) {
                      $parts = explode(' ', $monthValue);
                      $monthValue = $parts[0];
                      if (isset($parts[1])) {
                        $yearValue = $parts[1];
                      }
                    }
                  @endphp
                  <select class="form-select @error('month') is-invalid @enderror" id="month" name="month">
                    <option value="">Select Month</option>
                    <option value="January" {{ $monthValue == 'January' ? 'selected' : '' }}>January</option>
                    <option value="February" {{ $monthValue == 'February' ? 'selected' : '' }}>February</option>
                    <option value="March" {{ $monthValue == 'March' ? 'selected' : '' }}>March</option>
                    <option value="April" {{ $monthValue == 'April' ? 'selected' : '' }}>April</option>
                    <option value="May" {{ $monthValue == 'May' ? 'selected' : '' }}>May</option>
                    <option value="June" {{ $monthValue == 'June' ? 'selected' : '' }}>June</option>
                    <option value="July" {{ $monthValue == 'July' ? 'selected' : '' }}>July</option>
                    <option value="August" {{ $monthValue == 'August' ? 'selected' : '' }}>August</option>
                    <option value="September" {{ $monthValue == 'September' ? 'selected' : '' }}>September</option>
                    <option value="October" {{ $monthValue == 'October' ? 'selected' : '' }}>October</option>
                    <option value="November" {{ $monthValue == 'November' ? 'selected' : '' }}>November</option>
                    <option value="December" {{ $monthValue == 'December' ? 'selected' : '' }}>December</option>
                  </select>
                </div>
                <div class="col-6">
                  <select class="form-select @error('year') is-invalid @enderror" id="year" name="year">
                    <option value="">Select Year</option>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                      <option value="{{ $y }}" {{ $yearValue == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                  </select>
                </div>
              </div>
              <input type="hidden" id="month_year" name="month_year" value="{{ old('month_year', $collection->month) }}">
              <div class="form-text">Leave empty to auto-generate from date</div>
              @error('month')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              @error('year')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Description -->
            <div class="col-12 mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror" 
                        id="description" name="description" rows="3" 
                        placeholder="Additional notes about this collection...">{{ old('description', $collection->description) }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <!-- Form Actions -->
          <div class="row">
            <div class="col-12">
              <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('deposits.monthly-collections.index') }}" class="btn btn-outline-secondary">
                  Cancel
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                  <i class="bx bx-save me-1" id="submitIcon"></i> 
                  <span id="submitText">Update Collection</span>
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
jQuery(document).ready(function($) {
  // Deposit selection change - auto-fill monthly amount
  $('#deposit_id').on('change', function() {
    const depositId = this.value;
    if (depositId) {
      const option = this.options[this.selectedIndex];
      const monthlyAmount = option.dataset.monthlyAmount;
      if (monthlyAmount && parseFloat(monthlyAmount) > 0) {
        $('#monthlyAmountHint').text('Monthly deposit amount: ৳' + parseFloat(monthlyAmount).toFixed(2));
      } else {
        $('#monthlyAmountHint').text('');
      }
    } else {
      $('#monthlyAmountHint').text('');
    }
  });

  // Date change - auto-generate month and year
  $('#collection_date').on('change', function() {
    if (!$('#month').val() || !$('#year').val()) {
      const date = new Date(this.value);
      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                         'July', 'August', 'September', 'October', 'November', 'December'];
      $('#month').val(monthNames[date.getMonth()]);
      $('#year').val(date.getFullYear());
      updateMonthYear();
    }
  });

  // Update hidden month_year field when month or year changes
  function updateMonthYear() {
    const month = $('#month').val();
    const year = $('#year').val();
    if (month && year) {
      $('#month_year').val(month + ' ' + year);
    } else {
      $('#month_year').val('');
    }
  }

  $('#month, #year').on('change', function() {
    updateMonthYear();
  });

  // Initialize on page load
  updateMonthYear();

  // Form submission
  $('#collectionForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = $(this);
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
    
    // Get form data
    const formData = form.serialize();
    
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
            toastr.options = {
              closeButton: true,
              progressBar: true,
              timeOut: 3000,
              extendedTimeOut: 1000,
              positionClass: 'toast-top-right'
            };
            toastr.success(response.message || 'Monthly deposit collection updated successfully');
          } else {
            alert(response.message || 'Monthly deposit collection updated successfully');
          }
          
          // Redirect to index page
          setTimeout(function() {
            window.location.href = '{{ route("deposits.monthly-collections.index") }}';
          }, 1500);
        } else {
          // Show error message
          if (typeof toastr !== 'undefined') {
            toastr.error(response.message || 'Failed to update collection');
          } else {
            alert(response.message || 'Failed to update collection');
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
            : 'An error occurred while updating the collection. Please try again.';
          
          if (typeof toastr !== 'undefined') {
            toastr.error(errorMessage);
          } else {
            alert(errorMessage);
          }
        }
      }
    });
  });
});
</script>
@endsection
