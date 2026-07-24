@extends('layouts.contentNavbarLayout')

@section('title', 'Add Investment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Add New Investment</h5>
          <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back
          </a>
        </div>
        <div class="card-body">
          <form id="investment-form" action="{{ route('investments.store') }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="member_id" class="form-label">Member <span class="text-danger">*</span></label>
                <select class="form-select select2" id="member_id" name="member_id" required>
                  <option value="">Select Member</option>
                  @foreach($members as $member)
                    <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                      {{ $member->name }} ({{ $member->member_unique_id ?? $member->unique_id }})
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label for="account_number" class="form-label">Account Number</label>
                <input type="text" class="form-control" id="account_number" name="account_number" value="{{ $nextAccountNumber }}" readonly>
                <div class="form-text">Auto-generated on save</div>
              </div>

              <div class="col-md-6">
                <label for="investment_type_id" class="form-label">Investment Type <span class="text-danger">*</span></label>
                <select class="form-select" id="investment_type_id" name="investment_type_id" required>
                  <option value="">Select Investment Type</option>
                  @foreach($investmentTypes as $investmentType)
                    <option
                      value="{{ $investmentType->id }}"
                      data-code="{{ $investmentType->code }}"
                      @selected(old('investment_type_id') == $investmentType->id)
                    >
                      {{ $investmentType->investment_type_name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6 hpsm-only d-none">
                <label for="calculation_method" class="form-label">Calculation Method <span class="text-danger">*</span></label>
                <select class="form-select" id="calculation_method" name="calculation_method">
                  <option value="">Select Method</option>
                  <option value="annuity" @selected(old('calculation_method') === 'annuity')>Annuity</option>
                  <option value="reducing" @selected(old('calculation_method') === 'reducing')>Reducing Balance</option>
                </select>
              </div>

              <div class="col-md-6">
                <label for="principal_amount" class="form-label">Principal Amount <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">৳</span>
                  <input type="number" class="form-control" id="principal_amount" name="principal_amount"
                    value="{{ old('principal_amount') }}" step="0.01" min="0.01" required>
                </div>
              </div>

              <div class="col-md-6">
                <label for="rate" class="form-label">Profit / Rent Rate <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="number" class="form-control" id="rate" name="interest_rate"
                    value="{{ old('interest_rate') }}" step="0.01" min="0" required>
                  <span class="input-group-text">%</span>
                </div>
                <div class="form-text">Annual rate (e.g. 12 or 0.12)</div>
              </div>

              <div class="col-md-4">
                <label for="investment_years" class="form-label">Investment Years <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="investment_years" name="investment_years"
                  value="{{ old('investment_years') }}" min="1" max="30" required>
              </div>

              <div class="col-md-4">
                <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                <select class="form-select" id="payment_type" name="payment_type" required>
                  <option value="monthly" selected>Monthly</option>
                </select>
              </div>

              <div class="col-md-4">
                <label for="no_of_installments" class="form-label">No. of Installments</label>
                <input type="number" class="form-control bg-light" id="no_of_installments" name="no_of_installments" readonly>
              </div>

              <div class="col-md-4">
                <label for="account_opening_date" class="form-label">Account Opening Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="account_opening_date" name="account_opening_date"
                  value="{{ old('account_opening_date', date('Y-m-d')) }}" required>
              </div>

              <div class="col-md-4">
                <label for="start_date" class="form-label">Investment Start Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                  value="{{ old('start_date', date('Y-m-d')) }}" required>
              </div>

              <div class="col-md-4">
                <label for="gestation_maturity_date" class="form-label">Gestation Date</label>
                <input type="date" class="form-control" id="gestation_maturity_date" name="gestation_maturity_date"
                  value="{{ old('gestation_maturity_date') }}">
              </div>

              <div class="col-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
              </div>

              {{-- Hidden fields filled from server calculation for display/compat only --}}
              <input type="hidden" id="principal_amount_per_installment" name="principal_amount_per_installment">
              <input type="hidden" id="rent" name="rent">
              <input type="hidden" id="total_amount" name="total_amount">
              <input type="hidden" id="total_rent" name="total_rent">
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
              <a href="{{ route('investments.view-investments') }}" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" id="submit-btn" class="btn btn-primary">
                <span id="submit-text"><i class="bx bx-save me-1"></i> Create Investment</span>
                <span id="submit-spinner" class="d-none">
                  <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                </span>
              </button>
            </div>
          </form>
          <div id="form-messages" class="mt-3"></div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Installment Schedule Preview</h6>
          <span id="calc-status" class="badge bg-label-secondary">Waiting for inputs</span>
        </div>
        <div class="table-responsive" style="max-height: 420px;">
          <table class="table table-sm table-striped mb-0">
            <thead class="table-light sticky-top">
              <tr>
                <th>#</th>
                <th>Date</th>
                <th class="text-end">Beginning</th>
                <th class="text-end">Principal</th>
                <th class="text-end">Profit/Rent</th>
                <th class="text-end">Installment</th>
                <th class="text-end">Ending</th>
                <th class="text-end">Outstanding</th>
              </tr>
            </thead>
            <tbody id="schedule-body">
              <tr><td colspan="8" class="text-center text-muted py-4">Enter investment details to preview schedule.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card sticky-top" style="top: 1rem;">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0 text-white">Investment Summary</h6>
        </div>
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-6">Principal</dt>
            <dd class="col-6 text-end" id="sum_principal">—</dd>
            <dt class="col-6">Total Profit</dt>
            <dd class="col-6 text-end" id="sum_profit">—</dd>
            <dt class="col-6">Selling Price</dt>
            <dd class="col-6 text-end fw-semibold" id="sum_selling">—</dd>
            <dt class="col-6">Monthly Installment</dt>
            <dd class="col-6 text-end fw-semibold" id="sum_emi">—</dd>
            <dt class="col-6">Principal / Inst.</dt>
            <dd class="col-6 text-end" id="sum_prin_inst">—</dd>
            <dt class="col-6">Profit / Inst.</dt>
            <dd class="col-6 text-end" id="sum_rent_inst">—</dd>
            <dt class="col-6">Years</dt>
            <dd class="col-6 text-end" id="sum_years">—</dd>
            <dt class="col-6">Installments</dt>
            <dd class="col-6 text-end" id="sum_count">—</dd>
            <dt class="col-6">Maturity Date</dt>
            <dd class="col-6 text-end" id="sum_maturity">—</dd>
            <dt class="col-6">Total Payable</dt>
            <dd class="col-6 text-end fw-bold" id="sum_payable">—</dd>
          </dl>
          <div class="alert alert-info mt-3 mb-0 py-2 small">
            All figures are calculated by the server. Browser values are display-only and ignored on save.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function ($) {
  const calculateUrl = @json(route('investments.calculate'));
  let debounceTimer = null;
  let lastRequest = null;

  function money(n) {
    if (n === null || n === undefined || isNaN(n)) return '—';
    return Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function selectedTypeCode() {
    return $('#investment_type_id option:selected').data('code') || '';
  }

  function isHpsm() {
    const code = String(selectedTypeCode()).toLowerCase();
    const text = $('#investment_type_id option:selected').text().toLowerCase();
    return code === 'hpsm' || text.indexOf('hpsm') !== -1;
  }

  function toggleMethod() {
    if (isHpsm()) {
      $('.hpsm-only').removeClass('d-none');
      $('#calculation_method').prop('required', true);
    } else {
      $('.hpsm-only').addClass('d-none');
      $('#calculation_method').prop('required', false).val('');
    }
  }

  function canCalculate() {
    if (!$('#investment_type_id').val()) return false;
    if (!$('#principal_amount').val()) return false;
    if (!$('#rate').val()) return false;
    if (!$('#investment_years').val()) return false;
    if (!$('#start_date').val()) return false;
    if (isHpsm() && !$('#calculation_method').val()) return false;
    return true;
  }

  function applySummary(summary) {
    $('#sum_principal').text(money(summary.principal_amount));
    $('#sum_profit').text(money(summary.total_profit));
    $('#sum_selling').text(money(summary.selling_price));
    $('#sum_emi').text(money(summary.monthly_installment));
    $('#sum_prin_inst').text(money(summary.principal_per_installment));
    $('#sum_rent_inst').text(money(summary.profit_per_installment));
    $('#sum_years').text(summary.investment_years ?? '—');
    $('#sum_count').text(summary.number_of_installments ?? '—');
    $('#sum_maturity').text(summary.maturity_date ?? '—');
    $('#sum_payable').text(money(summary.total_payable));

    $('#no_of_installments').val(summary.number_of_installments || '');
    $('#principal_amount_per_installment').val(summary.principal_per_installment || '');
    $('#rent').val(summary.profit_per_installment || '');
    $('#total_amount').val(summary.monthly_installment || '');
    $('#total_rent').val(summary.total_profit || '');
  }

  function applySchedule(schedule) {
    const $body = $('#schedule-body');
    if (!schedule || !schedule.length) {
      $body.html('<tr><td colspan="8" class="text-center text-muted py-4">No schedule.</td></tr>');
      return;
    }
    let html = '';
    schedule.forEach(function (row) {
      html += '<tr>'
        + '<td>' + row.installment_number + '</td>'
        + '<td>' + row.schedule_date + '</td>'
        + '<td class="text-end">' + money(row.beginning_balance) + '</td>'
        + '<td class="text-end">' + money(row.principal_amount) + '</td>'
        + '<td class="text-end">' + money(row.rent) + '</td>'
        + '<td class="text-end">' + money(row.total_amount) + '</td>'
        + '<td class="text-end">' + money(row.ending_balance) + '</td>'
        + '<td class="text-end">' + money(row.outstanding_balance) + '</td>'
        + '</tr>';
    });
    $body.html(html);
  }

  function requestCalculate() {
    if (!canCalculate()) {
      $('#calc-status').attr('class', 'badge bg-label-secondary').text('Waiting for inputs');
      return;
    }

    $('#calc-status').attr('class', 'badge bg-label-warning').text('Calculating...');

    if (lastRequest && lastRequest.abort) {
      lastRequest.abort();
    }

    lastRequest = $.ajax({
      url: calculateUrl,
      type: 'POST',
      data: {
        _token: $('input[name="_token"]').val(),
        investment_type_id: $('#investment_type_id').val(),
        calculation_method: $('#calculation_method').val() || null,
        principal_amount: $('#principal_amount').val(),
        interest_rate: $('#rate').val(),
        investment_years: $('#investment_years').val(),
        start_date: $('#start_date').val(),
        payment_type: 'monthly'
      },
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      success: function (res) {
        if (!res.success) {
          $('#calc-status').attr('class', 'badge bg-label-danger').text(res.message || 'Failed');
          return;
        }
        applySummary(res.summary || {});
        applySchedule(res.schedule || []);
        $('#calc-status').attr('class', 'badge bg-label-success').text('Updated');
      },
      error: function (xhr) {
        if (xhr.statusText === 'abort') return;
        let msg = 'Calculation failed';
        if (xhr.responseJSON) {
          if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
          else if (xhr.responseJSON.errors) {
            msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
          }
        }
        $('#calc-status').attr('class', 'badge bg-label-danger').text(msg);
      }
    });
  }

  function queueCalculate() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(requestCalculate, 350);
  }

  $(document).ready(function () {
    toggleMethod();

    $('#investment_type_id').on('change', function () {
      toggleMethod();
      queueCalculate();
    });

    $('#calculation_method, #payment_type, #start_date').on('change', queueCalculate);
    $('#principal_amount, #rate, #investment_years').on('input change', queueCalculate);

    queueCalculate();

    $('#investment-form').on('submit', function (e) {
      e.preventDefault();
      const form = $(this);
      const submitBtn = $('#submit-btn');
      const submitText = $('#submit-text');
      const submitSpinner = $('#submit-spinner');
      const messages = $('#form-messages');

      submitBtn.prop('disabled', true);
      submitText.addClass('d-none');
      submitSpinner.removeClass('d-none');
      messages.html('');

      $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        success: function (response) {
          submitText.removeClass('d-none');
          submitSpinner.addClass('d-none');
          if (response.success) {
            messages.html('<div class="alert alert-success">' + (response.message || 'Saved') + '</div>');
            setTimeout(function () {
              window.location.href = response.redirect || '{{ route("investments.view-investments") }}';
            }, 800);
          } else {
            submitBtn.prop('disabled', false);
            messages.html('<div class="alert alert-danger">' + (response.message || 'Failed') + '</div>');
          }
        },
        error: function (xhr) {
          submitText.removeClass('d-none');
          submitSpinner.addClass('d-none');
          submitBtn.prop('disabled', false);
          let errorMessage = 'An error occurred while creating the investment.';
          if (xhr.responseJSON) {
            if (xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
            else if (xhr.responseJSON.errors) {
              errorMessage = '<ul class="mb-0">' + Object.values(xhr.responseJSON.errors).flat().map(function (v) {
                return '<li>' + v + '</li>';
              }).join('') + '</ul>';
            }
          }
          messages.html('<div class="alert alert-danger"><strong>Error!</strong> ' + errorMessage + '</div>');
        }
      });
    });
  });
})(jQuery);
</script>
@endsection
