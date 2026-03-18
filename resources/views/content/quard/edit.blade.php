@extends('layouts/contentNavbarLayout')

@section('title', 'Quards - Edit')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Quard</h5>
        <a href="{{ route('quards.view-quards') }}" class="btn btn-outline-secondary">
          <i class="bx bx-arrow-back me-1"></i> Back to List
        </a>
      </div>

      <div class="card-body">
        <form action="{{ route('quards.update', $quard) }}" method="POST" id="quardForm" novalidate>
          @csrf
          @method('PUT')

          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
              <select class="select2 form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                <option value="">Select Member</option>
                @foreach($members as $m)
                  <option value="{{ $m->id }}" {{ old('member_id', $quard->member_id) == $m->id ? 'selected' : '' }}>
                    {{ $m->name }} ({{ $m->unique_id }})
                  </option>
                @endforeach
              </select>
              @error('member_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="total_deposit_amount" class="form-label">Total Deposit Amount</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="total_deposit_amount" name="total_deposit_amount" value="{{ old('total_deposit_amount', $quard->total_deposit_amount) }}" step="0.01" min="0" readonly>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="percentage_of_deposit" class="form-label">Percentage of Deposit (%) <span class="text-danger">*</span></label>
              <input type="number" class="form-control @error('percentage_of_deposit') is-invalid @enderror" id="percentage_of_deposit" name="percentage_of_deposit" value="{{ old('percentage_of_deposit', $quard->percentage_of_deposit) }}" step="0.01" min="0" max="100" required>
              @error('percentage_of_deposit')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="quard_amount" class="form-label">Quard Amount</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('quard_amount') is-invalid @enderror" id="quard_amount" name="quard_amount" value="{{ old('quard_amount', $quard->quard_amount) }}" step="0.01" min="0" readonly required>
              </div>
              @error('quard_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="period_in_years" class="form-label">Period (Years) <span class="text-danger">*</span></label>
              <select class="form-select @error('period_in_years') is-invalid @enderror" id="period_in_years" name="period_in_years" required>
                @for($y = 1; $y <= 10; $y++)
                  <option value="{{ $y }}" {{ old('period_in_years', $quard->period_in_years) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
              @error('period_in_years')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="installment_number" class="form-label">Installment Number <span class="text-danger">*</span></label>
              <input type="number" class="form-control @error('installment_number') is-invalid @enderror" id="installment_number" name="installment_number" value="{{ old('installment_number', $quard->installment_number) }}" min="1" required>
              @error('installment_number')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="installment_amount" class="form-label">Installment Amount</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('installment_amount') is-invalid @enderror" id="installment_amount" name="installment_amount" value="{{ old('installment_amount', $quard->installment_amount) }}" step="0.01" min="0" readonly required>
              </div>
              @error('installment_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="charge_percentage" class="form-label">Charge Percentage (%)</label>
              <input type="number" class="form-control @error('charge_percentage') is-invalid @enderror" id="charge_percentage" name="charge_percentage" value="{{ old('charge_percentage', $quard->charge_percentage) }}" step="0.01" min="0" max="100">
              @error('charge_percentage')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="charge_amount" class="form-label">Charge Amount</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('charge_amount') is-invalid @enderror" id="charge_amount" name="charge_amount" value="{{ old('charge_amount', $quard->charge_amount) }}" step="0.01" min="0" readonly>
              </div>
              @error('charge_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="total_installment_amount" class="form-label">Total Installment Amount</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control @error('total_installment_amount') is-invalid @enderror" id="total_installment_amount" name="total_installment_amount" value="{{ old('total_installment_amount', $quard->total_installment_amount ?? 0) }}" step="0.01" min="0" readonly required>
              </div>
              @error('total_installment_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="total_payable_amount" class="form-label">Total Payable (Incl. Charge)</label>
              <div class="input-group">
                <span class="input-group-text">৳</span>
                <input type="number" class="form-control" id="total_payable_amount" value="0" step="0.01" min="0" readonly>
              </div>
            </div>

            <div class="col-md-6 mb-3">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', optional($quard->start_date)->format('Y-m-d')) }}">
              @error('start_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="maturity_date" class="form-label">Maturity Date</label>
              <input type="date" class="form-control @error('maturity_date') is-invalid @enderror" id="maturity_date" name="maturity_date" value="{{ old('maturity_date', optional($quard->maturity_date)->format('Y-m-d')) }}">
              @error('maturity_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
              <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="active" {{ old('status', $quard->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="matured" {{ old('status', $quard->status) === 'matured' ? 'selected' : '' }}>Matured</option>
                <option value="closed" {{ old('status', $quard->status) === 'closed' ? 'selected' : '' }}>Closed</option>
              </select>
              @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-12 mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $quard->notes) }}</textarea>
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
                  <span id="submitText">Update Quard</span>
                </button>
                <a href="{{ route('quards.view-quards') }}" class="btn btn-outline-secondary">Cancel</a>
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
window.memberTotalDepositsUrl = @json(route('quards.member-total-deposits', ['memberId' => '__ID__']));
</script>
<script>
jQuery(document).ready(function($) {
  const memberSelect = $('#member_id');
  const totalDepositInput = $('#total_deposit_amount');
  const percentInput = $('#percentage_of_deposit');
  const quardAmountInput = $('#quard_amount');
  const periodSelect = $('#period_in_years');
  const installmentNumberInput = $('#installment_number');
  const installmentAmountInput = $('#installment_amount');
  const chargePercentInput = $('#charge_percentage');
  const chargeAmountInput = $('#charge_amount');
  const totalInstallmentAmountInput = $('#total_installment_amount');
  const totalPayableAmountInput = $('#total_payable_amount');
  const startDateInput = $('#start_date');
  const maturityDateInput = $('#maturity_date');

  function toNumber(val) { const n = parseFloat(val); return isNaN(n) ? 0 : n; }
  function format2(n) { return (Math.round(n * 100) / 100).toFixed(2); }

  function calc() {
    const totalDeposit = toNumber(totalDepositInput.val());
    const percent = toNumber(percentInput.val());
    const quardAmount = (totalDeposit * percent) / 100;
    quardAmountInput.val(format2(quardAmount));

    const instN = Math.max(1, parseInt(installmentNumberInput.val() || '1', 10));
    installmentNumberInput.val(instN);
    const instAmount = instN > 0 ? quardAmount / instN : 0;
    installmentAmountInput.val(format2(instAmount));

    // Total installment amount (without charge) = total payable via installments
    totalInstallmentAmountInput.val(format2(quardAmount));

    const chPercent = toNumber(chargePercentInput.val());
    const chAmount = (quardAmount * chPercent) / 100;
    chargeAmountInput.val(format2(chAmount));

    // Total payable including charge
    totalPayableAmountInput.val(format2(quardAmount + chAmount));
  }

  function calcMaturity() {
    const start = startDateInput.val();
    const years = parseInt(periodSelect.val() || '1', 10);
    if (!start) return;
    const d = new Date(start + 'T00:00:00');
    if (isNaN(d.getTime())) return;
    d.setFullYear(d.getFullYear() + (isNaN(years) ? 1 : years));
    maturityDateInput.val(d.toISOString().split('T')[0]);
  }

  memberSelect.on('change', function() {
    const memberId = $(this).val();
    if (!memberId) {
      totalDepositInput.val('0.00');
      calc();
      return;
    }
    $.ajax({
      url: window.memberTotalDepositsUrl.replace('__ID__', memberId),
      type: 'GET',
      dataType: 'json',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function(data) {
        totalDepositInput.val(format2(toNumber(data.total_deposit_amount)));
        calc();
      },
      error: function() {
        totalDepositInput.val('0.00');
        calc();
      }
    });
  });

  percentInput.on('input', calc);
  installmentNumberInput.on('input', calc);
  chargePercentInput.on('input', calc);
  startDateInput.on('change', calcMaturity);
  periodSelect.on('change', function() {
    const years = Math.max(1, parseInt(periodSelect.val() || '1', 10));
    installmentNumberInput.val(years * 12);
    calcMaturity();
    calc();
  });

  calcMaturity();
  calc();

  function resetSubmitButton() {
    $('#submitBtn').prop('disabled', false);
    $('#submitSpinner').addClass('d-none');
    $('#submitIcon').removeClass('d-none');
    $('#submitText').text('Update Quard');
  }

  function submitFormViaAjax() {
    const form = $('#quardForm');
    const submitBtn = $('#submitBtn');
    const submitSpinner = $('#submitSpinner');
    const submitIcon = $('#submitIcon');
    const submitText = $('#submitText');

    if (!form[0].checkValidity()) { form[0].reportValidity(); return false; }
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
          if (typeof toastr !== 'undefined') toastr.success(response.message || 'Quard updated successfully');
          else alert(response.message || 'Quard updated successfully');
          setTimeout(function() { window.location.href = '{{ route("quards.view-quards") }}'; }, 800);
        } else {
          if (typeof toastr !== 'undefined') toastr.error(response.message || 'Failed to update quard');
          else alert(response.message || 'Failed to update quard');
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
          const errorMessage = (responseJSON && responseJSON.message) ? responseJSON.message : 'An error occurred while updating the quard.';
          if (typeof toastr !== 'undefined') toastr.error(errorMessage);
          else alert(errorMessage);
        }
      }
    });
  }

  $('#submitBtn').on('click', function(e) { e.preventDefault(); e.stopPropagation(); submitFormViaAjax(); return false; });
  $('#quardForm').on('submit', function(e) { e.preventDefault(); e.stopPropagation(); submitFormViaAjax(); return false; });
});
</script>
@endsection

