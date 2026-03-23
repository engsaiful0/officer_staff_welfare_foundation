@extends('layouts/contentNavbarLayout')

@section('title', 'Quards - Show')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Show Quard</h5>
                <a href="{{ route('quards.view-quards') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Back to List
                </a>
            </div>

            <div class="card-body">


                <div class="row">
                    <table class="table table-bordered table-striped table-hover">

                        <tr>
                            <td style="font-weight: bold;">Member</td>
                            <td>{{ $quard->member->name }} ({{ $quard->member->unique_id }})</td>
                            <td style="font-weight: bold;">Total Deposit Amount</td>
                            <td>{{ number_format((float) $quard->total_deposit_amount, 2) }}</td>
                        </tr>
                        <tr></tr>
                        <td style="font-weight: bold;">Percentage of Deposit (%) <span class="text-danger">*</span></td>
                        <td>{{ $quard->percentage_of_deposit }}</td>
                        <td style="font-weight: bold;">Quard Amount</td>
                        <td>{{ $quard->quard_amount }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Period (Years)</td>
                            <td>{{ $quard->period_in_years }}</td>
                            <td style="font-weight: bold;">Installment Number</td>
                            <td>{{ $quard->installment_number }}</td>
                        </tr>

                        <tr>
                            <td style="font-weight: bold;">Charge Percentage (%) <span class="text-danger">*</span></td>
                            <td>{{ $quard->charge_percentage }}</td>
                            <td style="font-weight: bold;">Charge Amount</td>
                            <td>{{ $quard->charge_amount }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Total Payable Amount</td>
                            <td>{{ $quard->total_payable_amount }}</td>

                            <td style="font-weight: bold;">Installment Amount</td>
                            <td>{{ $quard->installment_amount }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Start Date</td>
                            <td>{{ $quard->maturity_date ? $quard->maturity_date->format('Y-m-d') : '' }}</td>
                            <td style="font-weight: bold;">Maturity Date</td>
                            <td>{{ $quard->maturity_date ? $quard->maturity_date->format('Y-m-d') : '' }}</td>
                        </tr>
                        <tr>

                            <td style="font-weight: bold;">Status</td>
                            <td>{{ ucfirst($quard->status) }}</td>
                       
                            <td style="font-weight: bold;">Notes</td>
                            <td>{{ $quard->notes ?: '-' }}</td>

                        </tr>


                    </table>

                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Quard Payments</h6>
                            <a href="{{ route('quard-payment.add-quard-payment') }}" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus me-1"></i> Add Payment
                            </a>
                        </div>

                        @if($quard->quardPayments && $quard->quardPayments->count())
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Payment Date</th>
                                        <th>Payment Amount</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quard->quardPayments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                                        <td>{{ number_format((float) $payment->payment_amount, 2) }}</td>
                                        <td>{{ $payment->notes ?: '-' }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2" class="text-end">Total Payment Amount</td>
                                        <td>{{ number_format((float) $quard->quardPayments->sum('payment_amount'), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-secondary mb-0">No quard payments found.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- @section('page-script')
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
  const totalPayableAmountInput = $('#total_payable_amount');
  
  const startDateInput = $('#start_date');
  const maturityDateInput = $('#maturity_date');

  function toNumber(val) {
    const n = parseFloat(val);
    return isNaN(n) ? 0 : n;
  }

  function format2(n) {
    return (Math.round(n * 100) / 100).toFixed(2);
  }

  function calc() {
    const totalDeposit = toNumber(totalDepositInput.val());
    const percent = toNumber(percentInput.val());
    const quardAmount = (totalDeposit * percent) / 100;
    quardAmountInput.val(format2(quardAmount));

    const instN = Math.max(1, parseInt(installmentNumberInput.val() || '1', 10));
    installmentNumberInput.val(instN);
    

    // Total installment amount (without charge) = total payable via installments
    totalPayableAmountInput.val(format2(quardAmount));

    const chPercent = toNumber(chargePercentInput.val());
    const chAmount = (quardAmount * chPercent) / 100;
    chargeAmountInput.val(format2(chAmount));

    // Total payable including charge
    totalPayableAmountInput.val(format2(quardAmount + chAmount));

    const instAmount = instN > 0 ? toNumber(totalPayableAmountInput.val()) / instN : 0;
    installmentAmountInput.val(format2(instAmount));
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

  if (memberSelect.val()) memberSelect.trigger('change');
  calcMaturity();
  calc();

  function resetSubmitButton() {
    $('#submitBtn').prop('disabled', false);
    $('#submitSpinner').addClass('d-none');
    $('#submitIcon').removeClass('d-none');
    $('#submitText').text('Create Quard');
  }

  function submitFormViaAjax() {
    const form = $('#quardForm');
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
          if (typeof toastr !== 'undefined') toastr.success(response.message || 'Quard created successfully');
          else alert(response.message || 'Quard created successfully');
          setTimeout(function() {
            window.location.href = '{{ route("quards.view-quards") }}';
}, 800);
} else {
if (typeof toastr !== 'undefined') toastr.error(response.message || 'Failed to create quard');
else alert(response.message || 'Failed to create quard');
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
    errorHtml += '<strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-2">';
        $.each(errors, function(field, messages) {
        const fieldElement = $('[name="' + field + '"]');
        fieldElement.addClass('is-invalid');
        const msg = Array.isArray(messages) ? messages.join(' ') : messages;
        fieldElement.siblings('.invalid-feedback').remove();
        fieldElement.after('<div class="invalid-feedback">' + msg.trim() + '</div>');
        errorHtml += '<li>' + msg.trim() + '</li>';
        });
        errorHtml += '</ul>
</div>';
form.prepend(errorHtml);
$('html, body').animate({ scrollTop: 0 }, 500);
} else {
const errorMessage = (responseJSON && responseJSON.message) ? responseJSON.message : 'An error occurred while creating the quard.';
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

$('#quardForm').on('submit', function(e) {
e.preventDefault();
e.stopPropagation();
submitFormViaAjax();
return false;
});
});
</script>
@endsection --}}
