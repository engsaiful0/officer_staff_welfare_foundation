/* global $, AppUtils, Swal */
$(function () {
  var dt_basic_table = $('.datatables-basic'),
    dt_basic;

  function spinnerize($btn, on, textWhenOn) {
    var $spinner = $('#submitSpinner');
    var $text = $('#submitText');
    if (on) {
      $btn.prop('disabled', true);
      $spinner.removeClass('d-none');
      $text.text(textWhenOn || 'Saving…');
    } else {
      $btn.prop('disabled', false);
      $spinner.addClass('d-none');
      $text.text('Submit');
    }
  }

  function ajaxHeaders() {
    return {
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    };
  }

  if (dt_basic_table.length) {
    dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: window.permissionUrls.getData,
        type: 'GET',
        dataSrc: 'data',
        headers: ajaxHeaders()
      },
      columns: [
        { data: 'id' },
        { data: 'name' },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            return (
              '<div class="d-flex align-items-center gap-1">' +
              '<button type="button" class="btn btn-sm btn-outline-primary item-edit" data-id="' + row.id + '"><i class="bx bx-edit-alt me-1"></i>Edit</button>' +
              '<button type="button" class="btn btn-sm btn-outline-danger item-delete" data-id="' + row.id + '">' +
              '<span class="delete-text"><i class="bx bx-trash me-1"></i>Delete</span>' +
              '<span class="spinner-border spinner-border-sm d-none delete-spinner" role="status" aria-hidden="true"></span>' +
              '</button>' +
              '</div>'
            );
          }
        }
      ],
      order: [[0, 'desc']],
      dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      displayLength: 7,
      lengthMenu: [7, 10, 25, 50, 75, 100],
      language: {
        paginate: {
          next: '<i class="ti ti-chevron-right ti-sm"></i>',
          previous: '<i class="ti ti-chevron-left ti-sm"></i>'
        }
      },
      buttons: [
        {
          text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Create Permission</span>',
          className: 'create-new btn btn-primary waves-effect waves-light'
        }
      ]
    });
  }

  // Open offcanvas for add
  $(document).on('click', '.create-new', function () {
    $('#form-add-new-record')[0].reset();
    $('#add-new-record').offcanvas('show');
  });

  // Submit create/update
  $('#form-add-new-record').on('submit', function (e) {
    e.preventDefault();
    var $form = $(this);
    var $btn = $form.find('.data-submit');
    var id = $form.data('edit-id') || null;
    var url = window.permissionUrls.store;
    var method = 'POST';
    var successMsg = 'Permission added successfully.';
    var savingText = 'Creating…';

    if (id) {
      url = window.permissionUrls.update + '/' + id;
      method = 'PUT';
      successMsg = 'Permission updated successfully.';
      savingText = 'Updating…';
    }

    spinnerize($btn, true, savingText);

    $.ajax({
      url: url,
      type: method,
      data: $form.serialize(),
      headers: ajaxHeaders(),
      success: function (resp) {
        AppUtils.showSuccess((resp && resp.message) || successMsg);
        $('#add-new-record').offcanvas('hide');
        $form[0].reset();
        $form.removeData('edit-id');
        if (dt_basic) dt_basic.ajax.reload(null, false);
      },
      error: function (xhr) {
        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
          var messages = [];
          Object.values(xhr.responseJSON.errors).forEach(function (arr) {
            if (Array.isArray(arr)) messages = messages.concat(arr);
          });
          AppUtils.showError(messages.join('\n') || 'Validation failed.');
        } else {
          var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || 'Operation failed.';
          AppUtils.showError(msg);
        }
      },
      complete: function () {
        spinnerize($btn, false);
      }
    });
  });

  // Edit item: load into form and open offcanvas
  $(document).on('click', '.item-edit', function () {
    var id = $(this).data('id');
    // Load current from table row instead of extra AJAX for simplicity
    var rowData = dt_basic.row($(this).closest('tr')).data();
    $('#name').val(rowData.name || '');
    $('#form-add-new-record').data('edit-id', id);
    $('#add-new-record').offcanvas('show');
  });

  // Delete item with confirm + spinner on button
  $(document).on('click', '.item-delete', function () {
    var $btn = $(this);
    var id = $btn.data('id');
    var url = window.permissionUrls.destroy + '/' + id;
    var $spinner = $btn.find('.delete-spinner');
    var $text = $btn.find('.delete-text');

    var performDelete = function () {
      if ($btn.prop('disabled')) return;
      $btn.prop('disabled', true);
      $text.addClass('d-none');
      $spinner.removeClass('d-none');

      $.ajax({
        url: url,
        type: 'DELETE',
        headers: ajaxHeaders(),
        success: function (resp) {
          AppUtils.showSuccess((resp && resp.message) || 'Permission deleted successfully.');
          if (dt_basic) dt_basic.ajax.reload(null, false);
        },
        error: function (xhr) {
          var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || 'Delete failed.';
          AppUtils.showError(msg);
        },
        complete: function () {
          $btn.prop('disabled', false);
          $spinner.addClass('d-none');
          $text.removeClass('d-none');
        }
      });
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Are you sure?',
        text: 'This permission will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
      }).then(function (result) {
        if (result.isConfirmed) performDelete();
      });
    } else {
      if (confirm('Are you sure you want to delete this permission?')) {
        performDelete();
      }
    }
  });

  // Set table head label
  $('div.head-label').html('<h5 class="card-title mb-0">Permissions</h5>');
});

