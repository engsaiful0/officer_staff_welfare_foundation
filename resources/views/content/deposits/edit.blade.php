@extends('layouts/contentNavbarLayout')

@section('title', 'Deposits - Edit')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Deposit</h5>
        <a href="{{ route('deposits.show', $deposit) }}" class="btn btn-outline-secondary">
          <i class="bx bx-arrow-back me-1"></i> Back to Details
        </a>
      </div>
      
      <div class="card-body">
        <form action="{{ route('deposits.update', $deposit) }}" method="POST" id="depositForm" novalidate>
          @csrf
          @method('PUT')
          
          <div class="row">
            <!-- Member Selection -->
            <div class="col-md-12 mb-3">
              <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
              <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                <option value="">Select Member</option>
                @foreach($members as $member)
                  <option value="{{ $member->id }}" {{ (old('member_id', $deposit->member_id) == $member->id) ? 'selected' : '' }}>
                    {{ $member->name }} ({{ $member->unique_id }})
                  </option>
                @endforeach
              </select>
              @error('member_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Deposit Amount -->
            <div class="col-md-12 mb-3">
              <label for="deposit_amount" class="form-label">Deposit Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('deposit_amount') is-invalid @enderror" 
                       id="deposit_amount" name="deposit_amount" 
                       value="{{ old('deposit_amount', $deposit->deposit_amount) }}" 
                       step="0.01" min="0" placeholder="0.00" required>
              </div>
              @error('deposit_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Deposit Date -->
            <div class="col-md-12 mb-3">
              <label for="deposit_date" class="form-label">Deposit Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('deposit_date') is-invalid @enderror" 
                     id="deposit_date" name="deposit_date" 
                     value="{{ old('deposit_date', $deposit->deposit_date ? $deposit->deposit_date->format('Y-m-d') : '') }}" required>
              @error('deposit_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Notes -->
            <div class="col-12 mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control @error('notes') is-invalid @enderror" 
                        id="notes" name="notes" rows="3" 
                        placeholder="Additional notes about this deposit">{{ old('notes', $deposit->notes) }}</textarea>
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
                  <span id="submitText">Update Deposit</span>
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
if (typeof jQuery === 'undefined') {
  console.error('jQuery is not loaded!');
}

jQuery(document).ready(function($) {
  @if(session('success'))
    if (typeof toastr !== 'undefined') {
      toastr.success('{{ session('success') }}');
    }
  @endif
  @if(session('error'))
    if (typeof toastr !== 'undefined') {
      toastr.error('{{ session('error') }}');
    }
  @endif

  function resetSubmitButton() {
    $('#submitBtn').prop('disabled', false);
    $('#submitSpinner').addClass('d-none');
    $('#submitIcon').removeClass('d-none');
    $('#submitText').text('Update Deposit');
  }

  function submitFormViaAjax() {
    var form = $('#depositForm');
    var submitBtn = $('#submitBtn');
    var submitSpinner = $('#submitSpinner');
    var submitIcon = $('#submitIcon');
    var submitText = $('#submitText');

    if (!form[0].checkValidity()) {
      form[0].reportValidity();
      return false;
    }
    if (submitBtn.prop('disabled')) {
      return false;
    }

    submitBtn.prop('disabled', true);
    submitSpinner.removeClass('d-none');
    submitIcon.addClass('d-none');
    submitText.text('Saving…');

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
            toastr.success(response.message || 'Deposit updated successfully');
          } else {
            alert(response.message || 'Deposit updated successfully');
          }
          setTimeout(function() {
            if (response.data && response.data.id) {
              window.location.href = '{{ route("deposits.view-deposits") }}';
            } else {
              window.location.href = '{{ route("deposits.view-deposits") }}';
            }
          }, 800);
        } else {
          if (typeof toastr !== 'undefined') {
            toastr.error(response.message || 'Failed to update deposit');
          } else {
            alert(response.message || 'Failed to update deposit');
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
            : 'An error occurred while updating the deposit. Please try again.';
          if (typeof toastr !== 'undefined') {
            toastr.error(errorMessage);
          } else {
            alert(errorMessage);
          }
        }
      }
    });
  }

  $('#submitBtn').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    submitFormViaAjax();
    return false;
  });

  $('#depositForm').on('submit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    submitFormViaAjax();
    return false;
  });
});
</script>
@endsection

