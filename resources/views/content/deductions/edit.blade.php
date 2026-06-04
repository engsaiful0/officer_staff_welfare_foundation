@extends('layouts/contentNavbarLayout')

@section('title', 'Deductions - Edit')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit deduction</h5>
        <a href="{{ route('deductions.monthly-deduction-list') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left me-1"></i> Back to List
        </a>
      </div>

      <div class="card-body">
        <p class="text-muted mb-3">
          <strong>Member:</strong>
          @if($deduction->member)
            {{ $deduction->member->name }} ({{ $deduction->member->unique_id }})
          @else
            —
          @endif
          <span class="ms-2"><strong>Period:</strong> {{ date('F', mktime(0, 0, 0, $deduction->month, 1)) }} {{ $deduction->year }}</span>
        </p>

        <form action="{{ route('deductions.update', $deduction) }}" method="POST" id="deductionForm" novalidate>
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-12 mb-3">
              <button type="button" class="btn btn-outline-primary btn-sm" id="refreshAmountsBtn"
                      data-member-id="{{ $deduction->member_id }}"
                      data-month="{{ $deduction->month }}"
                      data-year="{{ $deduction->year }}">
                <span class="spinner-border spinner-border-sm me-1 d-none" id="refreshSpinner" role="status"></span>
                Refresh amounts from system
              </button>
              <span class="form-text ms-2">Recalculates suggested values for this member and period.</span>
            </div>

            <div class="col-md-6 mb-3">
              <label for="monthly_deposit_amount" class="form-label">Monthly deposit amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="monthly_deposit_amount" name="monthly_deposit_amount"
                       value="{{ old('monthly_deposit_amount', $deduction->monthly_deposit_amount) }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="monthly_investment_amount" class="form-label">Monthly investment amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="monthly_investment_amount" name="monthly_investment_amount"
                       value="{{ old('monthly_investment_amount', $deduction->monthly_investment_amount) }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="monthly_qard_amount" class="form-label">Monthly qard amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="monthly_qard_amount" name="monthly_qard_amount"
                       value="{{ old('monthly_qard_amount', $deduction->monthly_qard_amount) }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="profit_on_deposit_amount" class="form-label">Profit on deposit amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="profit_on_deposit_amount" name="profit_on_deposit_amount"
                       value="{{ old('profit_on_deposit_amount', $deduction->profit_on_deposit_amount) }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="compensation_on_investment_amount" class="form-label">Compensation on investment amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="compensation_on_investment_amount" name="compensation_on_investment_amount"
                       value="{{ old('compensation_on_investment_amount', $deduction->compensation_on_investment_amount) }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="total_amount" class="form-label">Total <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="total_amount" name="total_amount"
                       value="{{ old('total_amount', $deduction->total_amount) }}" step="0.01" min="0" required readonly>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="deduction_date" class="form-label">Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="deduction_date" name="deduction_date"
                     value="{{ old('deduction_date', $deduction->deduction_date?->format('Y-m-d')) }}" required>
            </div>

            <div class="col-12 mb-3">
              <label for="remarks" class="form-label">Remarks</label>
              <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Optional notes">{{ old('remarks', $deduction->remarks) }}</textarea>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" id="submitBtn">
              <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
              <i class="ti ti-device-floppy me-1" id="submitIcon"></i>
              <span id="submitText">Update deduction</span>
            </button>
            <a href="{{ route('deductions.monthly-deduction-list') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
window.deductionCalculateUrl = @json(route('deductions.calculate-amounts'));
window.deductionListUrl = @json(route('deductions.monthly-deduction-list'));
</script>
<script>
jQuery(document).ready(function($) {
  const $member = $('#member_id');
  const $month = $('#month');
  const $year = $('#year');

  function toNum(v) {
    const n = parseFloat(v);
    return isNaN(n) ? 0 : n;
  }

  function recalcTotal() {
    let sum = 0;
    $('.deduction-amount').each(function() {
      sum += toNum($(this).val());
    });
    $('#total_amount').val((Math.round(sum * 100) / 100).toFixed(2));
  }

  $('.deduction-amount').on('input', recalcTotal);

  $('#refreshAmountsBtn').on('click', function() {
    const btn = $(this);
    const sp = $('#refreshSpinner');
    btn.prop('disabled', true);
    sp.removeClass('d-none');
    const btnEl = $('#refreshAmountsBtn');
    const xhr = $.ajax({
      url: window.deductionCalculateUrl,
      type: 'GET',
      data: {
        member_id: btnEl.data('member-id'),
        month: btnEl.data('month'),
        year: btnEl.data('year')
      },
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
    xhr.done(function(data) {
      $('#monthly_deposit_amount').val(toNum(data.monthly_deposit_amount).toFixed(2));
      $('#monthly_investment_amount').val(toNum(data.monthly_investment_amount).toFixed(2));
      $('#monthly_qard_amount').val(toNum(data.monthly_qard_amount).toFixed(2));
      $('#profit_on_deposit_amount').val(toNum(data.profit_on_deposit_amount).toFixed(2));
      $('#compensation_on_investment_amount').val(toNum(data.compensation_on_investment_amount).toFixed(2));
      $('#total_amount').val(toNum(data.total_amount).toFixed(2));
    });
    xhr.fail(function() {
      if (typeof toastr !== 'undefined') toastr.warning('Could not refresh amounts.');
    });
    xhr.always(function() {
      btn.prop('disabled', false);
      sp.addClass('d-none');
    });
  });

  function resetSubmitButton() {
    $('#submitBtn').prop('disabled', false);
    $('#submitSpinner').addClass('d-none');
    $('#submitIcon').removeClass('d-none');
    $('#submitText').text('Update deduction');
  }

  function submitFormViaAjax() {
    const form = $('#deductionForm');
    const submitBtn = $('#submitBtn');
    const submitSpinner = $('#submitSpinner');
    const submitIcon = $('#submitIcon');
    const submitText = $('#submitText');

    recalcTotal();

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
    $('.invalid-feedback.dynamic').remove();
    $('.alert.ajax-error').remove();

    $.ajax({
      url: form.attr('action'),
      type: 'POST',
      data: form.serialize(),
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(response) {
        if (response.success) {
          if (typeof toastr !== 'undefined') toastr.success(response.message || 'Updated.');
          else alert(response.message || 'Updated.');
          setTimeout(function() {
            window.location.href = window.deductionListUrl;
          }, 600);
        } else {
          if (typeof toastr !== 'undefined') toastr.error(response.message || 'Could not update.');
          else alert(response.message || 'Could not update.');
          resetSubmitButton();
        }
      },
      error: function(xhr) {
        resetSubmitButton();
        const responseJSON = xhr.responseJSON || null;
        if (xhr.status === 422 && responseJSON && responseJSON.errors) {
          const errors = responseJSON.errors;
          let errorHtml = '<div class="alert alert-danger alert-dismissible fade show ajax-error" role="alert">';
          errorHtml += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
          errorHtml += '<strong>Please fix the following errors:</strong><ul class="mb-0 mt-2">';
          $.each(errors, function(field, messages) {
            const fieldElement = $('[name="' + field + '"]');
            fieldElement.addClass('is-invalid');
            const msg = Array.isArray(messages) ? messages.join(' ') : messages;
            fieldElement.siblings('.invalid-feedback.dynamic').remove();
            fieldElement.after('<div class="invalid-feedback dynamic">' + String(msg).trim() + '</div>');
            errorHtml += '<li>' + String(msg).trim() + '</li>';
          });
          errorHtml += '</ul></div>';
          form.prepend(errorHtml);
          $('html, body').animate({ scrollTop: 0 }, 400);
        } else {
          const errorMessage = (responseJSON && responseJSON.message)
            ? responseJSON.message
            : 'An error occurred while saving.';
          if (typeof toastr !== 'undefined') toastr.error(errorMessage);
          else alert(errorMessage);
        }
      }
    });
  }

  $('#submitBtn').on('click', function(e) {
    e.preventDefault();
    submitFormViaAjax();
    return false;
  });

  $('#deductionForm').on('submit', function(e) {
    e.preventDefault();
    submitFormViaAjax();
    return false;
  });
});
</script>
@endsection
