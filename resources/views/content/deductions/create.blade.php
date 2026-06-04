@extends('layouts/contentNavbarLayout')

@section('title', 'Deductions - Add')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add Deduction</h5>
        <a href="{{ route('deductions.monthly-deduction-list') }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left me-1"></i> Back to List
        </a>
      </div>

      <div class="card-body position-relative">
        <div id="amountsOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 align-items-center justify-content-center" style="z-index: 5; display: none;">
          <div class="text-center">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>
            <div class="small text-muted mt-2">Loading amounts…</div>
          </div>
        </div>

        <form action="{{ route('deductions.store') }}" method="POST" id="deductionForm" novalidate>
          @csrf

          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
              <select class="select2 form-select" id="member_id" name="member_id" required>
                <option value="">Select Member</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ old('member_id') == $m->id ? 'selected' : '' }}>
                    {{ $m->name }} ({{ $m->unique_id }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4 mb-3">
              <label for="month" class="form-label">Month <span class="text-danger">*</span></label>
              <select class="form-select" id="month" name="month" required>
                @for($m = 1; $m <= 12; $m++)
                  <option value="{{ $m }}" {{ (int) old('month', (int) date('n')) === $m ? 'selected' : '' }}>
                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                  </option>
                @endfor
              </select>
            </div>

            <div class="col-md-4 mb-3">
              <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
              <select class="form-select" id="year" name="year" required>
                @php $cy = (int) date('Y'); @endphp
                @for($y = $cy - 5; $y <= $cy + 2; $y++)
                  <option value="{{ $y }}" {{ (int) old('year', $cy) === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label for="monthly_deposit_amount" class="form-label">Monthly deposit amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="monthly_deposit_amount" name="monthly_deposit_amount"
                       value="{{ old('monthly_deposit_amount', '0.00') }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="monthly_investment_amount" class="form-label">Monthly investment amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="monthly_investment_amount" name="monthly_investment_amount"
                       value="{{ old('monthly_investment_amount', '0.00') }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="monthly_qard_amount" class="form-label">Monthly qard amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="monthly_qard_amount" name="monthly_qard_amount"
                       value="{{ old('monthly_qard_amount', '0.00') }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="profit_on_deposit_amount" class="form-label">Profit on deposit amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="profit_on_deposit_amount" name="profit_on_deposit_amount"
                       value="{{ old('profit_on_deposit_amount', '0.00') }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="compensation_on_investment_amount" class="form-label">Compensation on investment amount <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control deduction-amount" id="compensation_on_investment_amount" name="compensation_on_investment_amount"
                       value="{{ old('compensation_on_investment_amount', '0.00') }}" step="0.01" min="0" required>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="total_amount" class="form-label">Total <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="total_amount" name="total_amount"
                       value="{{ old('total_amount', '0.00') }}" step="0.01" min="0" required readonly>
              </div>
              <div class="form-text">Sum of the amounts above (updates when you change any amount).</div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="deduction_date" class="form-label">Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="deduction_date" name="deduction_date"
                     value="{{ old('deduction_date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-12 mb-3">
              <label for="remarks" class="form-label">Remarks</label>
              <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Optional notes">{{ old('remarks') }}</textarea>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" id="submitBtn">
              <span class="spinner-border spinner-border-sm me-2 d-none" id="submitSpinner" role="status" aria-hidden="true"></span>
              <i class="ti ti-device-floppy me-1" id="submitIcon"></i>
              <span id="submitText">Save deduction</span>
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
  if ($.fn.select2) {
    $('.select2').select2({ width: '100%' });
  }

  const $overlay = $('#amountsOverlay');
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

  let fetchTimer = null;
  function scheduleFetch() {
    clearTimeout(fetchTimer);
    fetchTimer = setTimeout(fetchAmounts, 350);
  }

  function fetchAmounts() {
    const memberId = $member.val();
    const month = $month.val();
    const year = $year.val();
    if (!memberId || !month || !year) {
      return;
    }

    $overlay.css({ display: 'flex' });
    $.ajax({
      url: window.deductionCalculateUrl,
      type: 'GET',
      data: { member_id: memberId, month: month, year: year },
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(data) {
        $('#monthly_deposit_amount').val(toNum(data.monthly_deposit_amount).toFixed(2));
        $('#monthly_investment_amount').val(toNum(data.monthly_investment_amount).toFixed(2));
        $('#monthly_qard_amount').val(toNum(data.monthly_qard_amount).toFixed(2));
        $('#profit_on_deposit_amount').val(toNum(data.profit_on_deposit_amount).toFixed(2));
        $('#compensation_on_investment_amount').val(toNum(data.compensation_on_investment_amount).toFixed(2));
        if (data.total_amount != null && data.total_amount !== '') {
          $('#total_amount').val(toNum(data.total_amount).toFixed(2));
        } else {
          recalcTotal();
        }
      },
      error: function() {
        if (typeof toastr !== 'undefined') {
          toastr.warning('Could not load suggested amounts for this selection.');
        }
      },
      complete: function() {
        $overlay.css({ display: 'none' });
      }
    });
  }

  $member.on('change', scheduleFetch);
  $month.on('change', scheduleFetch);
  $year.on('change', scheduleFetch);

  if ($member.val()) {
    scheduleFetch();
  } else {
    recalcTotal();
  }

  function resetSubmitButton() {
    $('#submitBtn').prop('disabled', false);
    $('#submitSpinner').addClass('d-none');
    $('#submitIcon').removeClass('d-none');
    $('#submitText').text('Save deduction');
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
    if (submitBtn.prop('disabled')) {
      return false;
    }

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
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message || 'Deduction saved.');
          } else {
            alert(response.message || 'Deduction saved.');
          }
          setTimeout(function() {
            window.location.href = window.deductionListUrl;
          }, 600);
        } else {
          if (typeof toastr !== 'undefined') {
            toastr.error(response.message || 'Could not save deduction.');
          } else {
            alert(response.message || 'Could not save deduction.');
          }
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
