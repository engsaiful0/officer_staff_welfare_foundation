/**
 * Deposit Installment Setup - Create, View, Update, Delete with AJAX and spinners.
 */
'use strict';

$(function () {
  var urls = window.monthlyDepositInstallmentSettingsUrls || {};
  var getDataUrl = urls.getData || '/app/members/monthly-deposit-installment-settings/get-data';
  var lastAmountUrl = urls.lastAmount || '/app/members/monthly-deposit-installment-settings/last-amount';
  var storeUrl = urls.store || '/app/members/monthly-deposit-installment-settings';
  var showUrl = urls.show || '/app/members/monthly-deposit-installment-settings';
  var updateUrl = urls.update || '/app/members/monthly-deposit-installment-settings';
  var destroyUrl = urls.destroy || '/app/members/monthly-deposit-installment-settings';
  var getMembersUrl = urls.getMembers || '/app/members/monthly-deposit-installment-settings/get-members';

  var dt_basic_table = $('.datatables-basic');
  var dt_basic;

  function loadMembersIntoSelect($select, dropdownParent, callback) {
    if (!$select || !$select.length) return;
    var $loading = $('#member_id_loading');
    if ($loading.length) $loading.removeClass('d-none');
    $.ajax({
      url: getMembersUrl,
      type: 'GET',
      dataType: 'json',
      success: function (res) {
        var members = (res && res.members) ? res.members : [];
        var firstOpt = $select.find('option:first').clone();
        $select.find('option').remove();
        $select.append(firstOpt);
        members.forEach(function (m) {
          var uid = m.unique_id || m.member_unique_id || '';
          $select.append($('<option></option>').attr('value', m.id).text(m.name + (uid ? ' (' + uid + ')' : '')));
        });
        if (typeof callback === 'function') callback();
      },
      error: function () {
        if (typeof toastr !== 'undefined') toastr.error('Could not load members.');
        if (typeof callback === 'function') callback();
      },
      complete: function () {
        if ($loading.length) $loading.addClass('d-none');
      }
    });
  }

  var $memberSelect = $('#member_id');
  if ($memberSelect.length) {
    var fromServer = window.membersFromServer || [];
    if (fromServer.length > 0) {
      var firstOpt = $memberSelect.find('option:first').clone();
      $memberSelect.find('option').remove();
      $memberSelect.append(firstOpt);
      fromServer.forEach(function (m) {
        var uid = m.unique_id || '';
        $memberSelect.append($('<option></option>').attr('value', m.id).text(m.name + (uid ? ' (' + uid + ')' : '')));
      });
    } else if ($memberSelect.find('option').length <= 1) {
      loadMembersIntoSelect($memberSelect, null);
    }
  }

  if (dt_basic_table.length) {
    dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: getDataUrl,
        type: 'GET',
        dataSrc: 'data',
        beforeSend: function () {
          $('.datatables-basic tbody').html('<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"></div></td></tr>');
        },
        error: function (xhr) {
          var msg = 'Failed to load data. ';
          if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg += xhr.responseJSON.message;
          if (typeof toastr !== 'undefined') toastr.error(msg);
          $('.datatables-basic tbody').html('<tr><td colspan="8" class="text-center text-danger">' + msg + '</td></tr>');
        }
      },
      columns: [
        { data: 'id' },
        { data: 'member_name', render: function (d, type, row) { return d + ' <span class="text-muted">(' + (row.member_unique_id || '') + ')</span>'; } },
        { data: 'installment_amount' },
        { data: 'date_formatted' },
        { data: 'month_name' },
        { data: 'year' },
        { data: 'user_name' },
        { data: '' }
      ],
      columnDefs: [
        {
          targets: -1,
          title: 'Actions',
          orderable: false,
          searchable: false,
          render: function (data, type, full) {
            return '<div class="d-inline-block">' +
              '<a href="javascript:;" class="btn btn-sm btn-icon view-record" data-id="' + full.id + '" title="View"><i class="ti ti-eye ti-md"></i></a>' +
              '<a href="javascript:;" class="btn btn-sm btn-icon edit-record" data-id="' + full.id + '" title="Edit"><i class="ti ti-pencil ti-md"></i></a>' +
              '<a href="javascript:;" class="btn btn-sm btn-icon delete-record" data-id="' + full.id + '" title="Delete"><i class="ti ti-trash ti-md"></i></a>' +
              '</div>';
          }
        }
      ],
      order: [[0, 'desc']],
      dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      displayLength: 10,
      lengthMenu: [10, 25, 50, 75, 100],
      buttons: [
        { text: '<i class="ti ti-plus me-sm-1"></i> Create', className: 'create-new btn btn-primary' }
      ],
      initComplete: function () {
        $('.head-label').html('<h5 class="card-title mb-0">Deposit Installment Setup</h5>');
      }
    });
  }

  $(document).on('click', '.create-new', function () {
    var el = document.querySelector('#add-new-record');
    if (!el) return;
    var today = new Date().toISOString().slice(0, 10);
    $('#member_id').val('').trigger('change');
    $('#installment_amount').val('');
    $('#date').val(today);
    $('#month').val('');
    $('#year').val('');
    $('#form-create-installment').removeClass('was-validated');
    new bootstrap.Offcanvas(el).show();
    var $select = $('#member_id');
    if ($select.find('option').length <= 1) loadMembersIntoSelect($select, $('#add-new-record'), function () {
      if (!$select.hasClass('select2-hidden-accessible')) $select.select2({ dropdownParent: $('#add-new-record'), width: '100%' });
    });
    else if (!$select.hasClass('select2-hidden-accessible')) $select.select2({ dropdownParent: $('#add-new-record'), width: '100%' });
  });

  $('#member_id').on('change', function () {
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

  $('#form-create-installment').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#create-submit-btn'), $spinner = $('#create-spinner'), $text = $('#create-submit-text');
    if (!$('#member_id').val() || !$('#installment_amount').val() || !$('#date').val()) {
      $(this).addClass('was-validated');
      return;
    }
    $btn.prop('disabled', true);
    $spinner.removeClass('d-none');
    $text.text('Creating...');
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
        if (typeof toastr !== 'undefined') toastr.success('Deposit installment created.');
        bootstrap.Offcanvas.getInstance(document.querySelector('#add-new-record')) && bootstrap.Offcanvas.getInstance(document.querySelector('#add-new-record')).hide();
        $('#form-create-installment')[0].reset();
        $('#member_id').val('').trigger('change');
        if (dt_basic && dt_basic.ajax) dt_basic.ajax.reload();
      },
      error: function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to create.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          var err = xhr.responseJSON.errors;
          msg = Object.keys(err).map(function (k) { return err[k][0]; }).join(' ');
        }
        if (typeof toastr !== 'undefined') toastr.error(msg);
      },
      complete: function () {
        $btn.prop('disabled', false);
        $spinner.addClass('d-none');
        $text.text('Create');
      }
    });
  });

  $('.datatables-basic tbody').on('click', '.view-record', function () {
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

  $('.datatables-basic tbody').on('click', '.edit-record', function () {
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
        if (dt_basic && dt_basic.ajax) dt_basic.ajax.reload();
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

  $('.datatables-basic tbody').on('click', '.delete-record', function () {
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
        if (dt_basic && dt_basic.ajax) dt_basic.ajax.reload();
      },
      error: function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed.';
        if (typeof toastr !== 'undefined') toastr.error(msg);
      },
      complete: function () {
        $btn.prop('disabled', false).html(origHtml);
      }
    });
  }
});
