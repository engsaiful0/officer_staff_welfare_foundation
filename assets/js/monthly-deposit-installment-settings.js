/**
 * Deposit Installment Setup - View, Update, Delete with AJAX and spinners.
 * List uses Laravel pagination; Create button goes to create route.
 */
'use strict';

$(function () {
  var urls = window.monthlyDepositInstallmentSettingsUrls || {};
  var showUrl = urls.show || '/app/members/monthly-deposit-installment-settings';
  var updateUrl = urls.update || '/app/members/monthly-deposit-installment-settings';
  var destroyUrl = urls.destroy || '/app/members/monthly-deposit-installment-settings';

  $(document).on('click', '.view-record', function () {
    var id = $(this).data('id');
    var $loading = $('#view-loading'), $content = $('#view-content');
    var modal = document.getElementById('view-installment-modal');
    if (!modal) return;
    $content.addClass('d-none').empty();
    $loading.removeClass('d-none');
    new bootstrap.Modal(modal).show();
    $.ajax({
      url: showUrl + '/' + id,
      type: 'GET',
      dataType: 'json',
      success: function (res) {
        var d = res.data || {};
        $content.removeClass('d-none').html(
          '<table class="table table-borderless">' +
          '<tr><td class="text-muted">Member</td><td>' + (d.member_name || '—') + ' (' + (d.member_unique_id || '') + ')</td></tr>' +
          '<tr><td class="text-muted">Amount</td><td>' + (d.installment_amount || '—') + '</td></tr>' +
          '<tr><td class="text-muted">Date</td><td>' + (d.date_formatted || '—') + '</td></tr>' +
          '<tr><td class="text-muted">Month</td><td>' + (d.month_name || '—') + '</td></tr>' +
          '<tr><td class="text-muted">Year</td><td>' + (d.year || '—') + '</td></tr>' +
          '<tr><td class="text-muted">User</td><td>' + (d.user_name || '—') + '</td></tr>' +
          '</table>'
        );
      },
      error: function () {
        if (typeof toastr !== 'undefined') toastr.error('Failed to load record.');
        $content.removeClass('d-none').html('<p class="text-danger">Failed to load.</p>');
      },
      complete: function () {
        $loading.addClass('d-none');
      }
    });
  });

  $(document).on('click', '.edit-record', function () {
    var id = $(this).data('id');
    var el = document.getElementById('edit-installment-record');
    var $loading = $('#edit-loading'), $form = $('#form-edit-installment');
    if (!el) return;
    $form.addClass('d-none');
    $loading.removeClass('d-none');
    new bootstrap.Offcanvas(el).show();
    $.ajax({
      url: showUrl + '/' + id,
      type: 'GET',
      dataType: 'json',
      success: function (res) {
        var d = res.data || {};
        $('#edit_id').val(d.id);
        $('#edit_member_id').val(d.member_id || '');
        $('#edit_installment_amount').val(d.installment_amount_raw != null ? d.installment_amount_raw : d.installment_amount);
        $('#edit_date').val(d.date || '');
        $('#edit_month').val(d.month || '');
        $('#edit_year').val(d.year || '');
        $loading.addClass('d-none');
        $form.removeClass('d-none');
        if ($('#edit_member_id').hasClass('select2-hidden-accessible')) $('#edit_member_id').select2('destroy');
        $('#edit_member_id').select2({ dropdownParent: $('#edit-installment-record'), width: '100%' });
      },
      error: function () {
        if (typeof toastr !== 'undefined') toastr.error('Failed to load record.');
        $loading.addClass('d-none');
        $form.removeClass('d-none');
      }
    });
  });

  $('#form-edit-installment').on('submit', function (e) {
    e.preventDefault();
    var id = $('#edit_id').val();
    var $btn = $('#edit-submit-btn'), $spinner = $('#edit-spinner'), $text = $('#edit-submit-text');
    if (!$('#edit_member_id').val() || !$('#edit_installment_amount').val() || !$('#edit_date').val()) {
      $(this).addClass('was-validated');
      return;
    }
    $btn.prop('disabled', true);
    $spinner.removeClass('d-none');
    $text.text('Updating...');
    $.ajax({
      url: updateUrl + '/' + id,
      type: 'PUT',
      data: {
        _token: $('meta[name="csrf-token"]').attr('content'),
        member_id: $('#edit_member_id').val(),
        installment_amount: $('#edit_installment_amount').val(),
        date: $('#edit_date').val(),
        month: $('#edit_month').val() || null,
        year: $('#edit_year').val() || null
      },
      success: function () {
        if (typeof toastr !== 'undefined') toastr.success('Deposit installment updated.');
        bootstrap.Offcanvas.getInstance(document.getElementById('edit-installment-record')) && bootstrap.Offcanvas.getInstance(document.getElementById('edit-installment-record')).hide();
        window.location.reload();
      },
      error: function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update.';
        if (xhr.responseJSON && xhr.responseJSON.errors) msg = Object.keys(xhr.responseJSON.errors).map(function (k) { return xhr.responseJSON.errors[k][0]; }).join(' ');
        if (typeof toastr !== 'undefined') toastr.error(msg);
      },
      complete: function () {
        $btn.prop('disabled', false);
        $spinner.addClass('d-none');
        $text.text('Update');
      }
    });
  });

  $(document).on('click', '.delete-record', function (e) {
    e.preventDefault();
    var id = $(this).data('id'), $btn = $(this);
    if (typeof Swal === 'undefined') {
      if (confirm('Delete this record?')) doDelete(id, $btn);
      return;
    }
    Swal.fire({
      title: 'Are you sure?',
      text: 'This record will be deleted.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it',
      customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
      buttonsStyling: false
    }).then(function (result) {
      if (result.isConfirmed) doDelete(id, $btn);
    });
  });

  function doDelete(id, $btn) {
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
    $.ajax({
      url: destroyUrl + '/' + id,
      type: 'DELETE',
      data: { _token: $('meta[name="csrf-token"]').attr('content') },
      success: function () {
        if (typeof toastr !== 'undefined') toastr.success('Record deleted.');
        window.location.reload();
      },
      error: function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed.';
        if (typeof toastr !== 'undefined') toastr.error(msg);
        $btn.prop('disabled', false).html(origHtml);
      },
      complete: function () {
        $btn.prop('disabled', false).html(origHtml);
      }
    });
  }
});
