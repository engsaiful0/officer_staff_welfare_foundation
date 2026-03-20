@extends('layouts/contentNavbarLayout')

@section('title', 'Quard Payments - Add New')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add New Quard Payment</h5>
        <a href="{{ route('quard-payment.view-quard-payment') }}" class="btn btn-outline-secondary">
          <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
      </div>

      <div class="card-body">
        <form action="{{ route('quard-payment.store') }}" method="POST" id="quardPaymentForm" novalidate>
          @csrf

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
              <select class="select2 form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                <option value="">Select Member</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                    {{ $m->name }} ({{ $m->unique_id }})
                  </option>
                @endforeach
              </select>
              @error('member_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <input type="hidden" id="quard_id" name="quard_id" required>

            <div class="col-md-4 mb-3">
              <label for="payment_amount" class="form-label">Quard Payment Amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number"
                       class="form-control @error('payment_amount') is-invalid @enderror"
                       id="payment_amount"
                       name="payment_amount"
                       value="{{ old('payment_amount', 0) }}"
                       step="0.01"
                       min="0"
                       readonly
                       required>
              </div>
              <div class="form-text">Auto-populated from selected member’s quard.</div>
              @error('payment_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            
           
        

            <div class="col-md-4 mb-3">
              <label for="start_date" class="form-label">Payment Date</label>
              <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
              @error('payment_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>


            <div class="col-4 mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Notes">{{ old('notes') }}</textarea>
              @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" id="submitBtn">
                  <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
                  <i class="bx bx-save me-1" id="submitIcon"></i>
                  <span id="submitText">Create Quard Payment</span>
                </button>
                <a href="{{ route('quard-payment.view-quard-payment') }}" class="btn btn-outline-secondary">Cancel</a>
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
  window.getQuardPaymentAmountUrl = @json(route('quard-payment.get-quard-amount', ['memberId' => '__ID__']));
</script>
<script>
jQuery(document).ready(function($) {
  const memberSelect = $('#member_id');
  const quardIdInput = $('#quard_id');
  const paymentAmountInput = $('#payment_amount');
  const paymentDateInput = $('#payment_date');

  function format2(n) {
    return (Math.round(n * 100) / 100).toFixed(2);
  }

  // Populate quard payment amount on member change
  memberSelect.on('change', function() {
    const memberId = $(this).val();
    if (!memberId) {
      quardIdInput.val('');
      paymentAmountInput.val('0.00');
      return;
    }

    $.ajax({
      url: window.getQuardPaymentAmountUrl.replace('__ID__', memberId),
      type: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(data) {
        quardIdInput.val(data.quard_id || '');
        paymentAmountInput.val(format2(parseFloat(data.payment_amount || 0)));
      },
      error: function() {
        quardIdInput.val('');
        paymentAmountInput.val('0.00');
      }
    });
  });

  if (memberSelect.val()) memberSelect.trigger('change');

  function resetSubmitButton() {
    $('#submitBtn').prop('disabled', false);
    $('#submitSpinner').addClass('d-none');
    $('#submitIcon').removeClass('d-none');
    $('#submitText').text('Create Quard Payment');
  }

  function submitFormViaAjax() {
    const form = $('#quardPaymentForm');
    const submitBtn = $('#submitBtn');
    const submitSpinner = $('#submitSpinner');
    const submitIcon = $('#submitIcon');
    const submitText = $('#submitText');

    if (!form[0].checkValidity()) {
      form[0].reportValidity();
      return false;
    }
    if (submitBtn.prop('disabled')) return false;

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
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(response) {
        if (response.success) {
          if (typeof toastr !== 'undefined') toastr.success(response.message || 'Quard payment created successfully');
          else alert(response.message || 'Quard payment created successfully');

          setTimeout(function() {
            window.location.href = '{{ route("quard-payment.view-quard-payment") }}';
          }, 800);
        } else {
          if (typeof toastr !== 'undefined') toastr.error(response.message || 'Failed to create quard payment');
          else alert(response.message || 'Failed to create quard payment');
          resetSubmitButton();
        }
      },
      error: function(xhr) {
        resetSubmitButton();

        const responseJSON = xhr.responseJSON || null;
        if (xhr.status === 422 && responseJSON && responseJSON.errors) {
          const errors = responseJSON.errors;
          let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
          errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
          errorHtml += '<strong>Please fix the following errors:</strong><ul class="mb-0 mt-2">';

          $.each(errors, function(field, messages) {
            const fieldElement = $('[name="' + field + '"]');
            fieldElement.addClass('is-invalid');
            const msg = Array.isArray(messages) ? messages.join(' ') : messages;
            fieldElement.siblings('.invalid-feedback').remove();
            fieldElement.after('<div class="invalid-feedback">' + msg.trim() + '</div>');
            errorHtml += '<li>' + msg.trim() + '</li>';
          });

          errorHtml += '</ul></div>';
          form.prepend(errorHtml);
          $('html, body').animate({ scrollTop: 0 }, 500);
        } else {
          const errorMessage = (responseJSON && responseJSON.message) ? responseJSON.message : 'An error occurred while creating the quard payment.';
          if (typeof toastr !== 'undefined') toastr.error(errorMessage);
          else alert(errorMessage);
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

  $('#quardPaymentForm').on('submit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    submitFormViaAjax();
    return false;
  });
});
</script>
@endsection
