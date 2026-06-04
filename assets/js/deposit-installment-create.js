/**
 * Add Deposit Installment (create page) - AJAX submit with spinner, redirect on success.
 */
'use strict';

$(function () {
  var config = window.depositInstallmentCreate || {};
  var storeUrl = config.storeUrl || '/app/members/monthly-deposit-installment-settings';
  var indexUrl = config.indexUrl || '/app/members/monthly-deposit-installment-settings';
  var lastAmountUrl = config.lastAmountUrl || '/app/members/monthly-deposit-installment-settings/last-amount';

  var $memberSelect = $('#member_id');
  if ($memberSelect.length && config.members && config.members.length) {
    var first = $memberSelect.find('option:first').clone();
    $memberSelect.find('option').remove();
    $memberSelect.append(first);
    config.members.forEach(function (m) {
      $memberSelect.append($('<option></option>').attr('value', m.id).text(m.name + (m.member_unique_id ? ' (' + m.member_unique_id + ')' : '')));
    });
  }

  if ($memberSelect.length && !$memberSelect.hasClass('select2-hidden-accessible')) {
    $memberSelect.select2({ width: '100%' });
  }

  var today = new Date().toISOString().slice(0, 10);
  if (!$('#date').val()) $('#date').val(today);

  $memberSelect.on('change', function () {
    var memberId = $(this).val();
    if (!memberId) return;
    $.ajax({
      url: lastAmountUrl + '/' + memberId,
      type: 'GET',
      success: function (res) {
        if (res.installment_amount != null) $('#installment_amount').val(res.installment_amount);
        if (res.date) $('#date').val(res.date);
        if (res.month != null) $('#month').val(res.month);
        if (res.year != null) $('#year').val(res.year);
      }
    });
  });

  $('#form-deposit-installment-create').on('submit', function (e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $('#submit-btn');
    var $spinner = $('#submit-spinner');
    var $text = $('#submit-text');

    if (!$('#member_id').val() || !$('#installment_amount').val() || !$('#date').val()) {
      $form.addClass('was-validated');
      return;
    }

    $btn.prop('disabled', true);
    $spinner.removeClass('d-none');
    $text.text('Saving...');

    $.ajax({
      url: storeUrl,
      type: 'POST',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        member_id: $('#member_id').val(),
        installment_amount: $('#installment_amount').val(),
        date: $('#date').val(),
        month: $('#month').val() || null,
        year: $('#year').val() || null
      },
      success: function () {
        if (typeof toastr !== 'undefined') toastr.success('Deposit installment created successfully.');
        window.location.href = indexUrl;
      },
      error: function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to save.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          var err = xhr.responseJSON.errors;
          msg = Object.keys(err).map(function (k) { return err[k][0]; }).join(' ');
        }
        if (typeof toastr !== 'undefined') toastr.error(msg);
        $btn.prop('disabled', false);
        $spinner.addClass('d-none');
        $text.text('Save Deposit Installment');
      }
    });
  });
});
