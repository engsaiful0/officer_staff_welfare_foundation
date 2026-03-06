/**
 * Zone DataTables - CRUD with AJAX and Spinners
 */

'use strict';

let fv, offCanvasEl;
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const formAddNewRecord = document.getElementById('form-add-new-record');

    setTimeout(() => {
      const newRecord = document.querySelector('.create-new'),
        offCanvasElement = document.querySelector('#add-new-record');

      if (newRecord) {
        newRecord.addEventListener('click', function () {
          showAddButtonSpinner(this);
          setTimeout(function () {
            offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
            offCanvasElement.querySelector('.dt-full-name').value = '';
            $('#form-add-new-record').removeAttr('data-id');
            offCanvasEl.show();
            hideAddButtonSpinner(newRecord);
          }, 200);
        });
      }
    }, 200);

    fv = FormValidation.formValidation(formAddNewRecord, {
      fields: {
        zone_name: {
          validators: {
            notEmpty: {
              message: 'The zone name is required'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.col-sm-12'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        autoFocus: new FormValidation.plugins.AutoFocus()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    });
  })();
});

$(function () {
  var getDataUrl = (typeof window.zoneUrls !== 'undefined' && window.zoneUrls.getData)
    ? window.zoneUrls.getData
    : (window.AppUtils && window.AppUtils.buildUrl ? window.AppUtils.buildUrl('app/settings/get-zone') : '/app/settings/get-zone');

  var dt_basic_table = $('.datatables-basic'),
    dt_basic;

  if (dt_basic_table.length) {
    dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: getDataUrl,
        type: 'GET',
        dataSrc: 'data',
        beforeSend: function () {
          $('.datatables-basic tbody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        },
        error: function (xhr, error, code) {
          var msg = 'Failed to load zones. ';
          if (xhr && xhr.status) msg += 'Status: ' + xhr.status + '. ';
          if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg += xhr.responseJSON.message;
          else if (xhr && xhr.responseText) msg += (xhr.responseText.substring(0, 100) || 'Invalid response.');
          console.error('Zone DataTables Ajax error:', { xhr: xhr, error: error, code: code });
          if (typeof toastr !== 'undefined') toastr.error(msg);
          $('.datatables-basic tbody').html('<tr><td colspan="3" class="text-center text-danger">' + msg + '</td></tr>');
        }
      },
      columns: [
        { data: 'id' },
        { data: 'zone_name' },
        { data: '' }
      ],
      columnDefs: [
        {
          targets: -1,
          title: 'Actions',
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return (
              '<div class="d-inline-block">' +
              '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="ti ti-pencil ti-md"></i></a>' +
              '<a href="javascript:;" class="btn btn-sm btn-text-secondary rounded-pill btn-icon delete-record"><i class="ti ti-trash ti-md"></i></a>' +
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
          text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
          className: 'create-new btn btn-primary waves-effect waves-light'
        }
      ],
      initComplete: function (settings, json) {
        $('.card-header').after('<hr class="my-0">');
      }
    });
    $('div.head-label').html('<h5 class="card-title mb-0">Zones</h5>');
  }

  // Add/Update Record
  fv.on('core.form.valid', function () {
    var $new_name = $('.add-new-record .dt-full-name').val();
    var id = $('#form-add-new-record').attr('data-id');
    var $submitBtn = $('#submit-btn');
    var $submitSpinner = $('#submit-spinner');
    var $submitText = $('#submit-text');
    var $cancelBtn = $('.add-new-record .btn-outline-secondary');

    if ($new_name !== '') {
      var baseUrl = (typeof window.zoneUrls !== 'undefined' && window.zoneUrls.store) ? window.zoneUrls.store : (window.AppUtils && window.AppUtils.buildUrl ? window.AppUtils.buildUrl('app/settings/zone') : '/app/settings/zone');
      var url = baseUrl;
      var method = 'POST';
      var message = 'Zone added successfully.';
      var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        zone_name: $new_name
      };

      if (id) {
        url = (typeof window.zoneUrls !== 'undefined' && window.zoneUrls.update) ? window.zoneUrls.update + '/' + id : (window.AppUtils && window.AppUtils.buildUrl ? window.AppUtils.buildUrl('app/settings/zone/' + id) : '/app/settings/zone/' + id);
        method = 'PUT';
        message = 'Zone updated successfully.';
      }

      $submitBtn.prop('disabled', true);
      $submitSpinner.removeClass('d-none');
      $submitText.text('Processing...');
      $cancelBtn.prop('disabled', true);

      $.ajax({
        url: url,
        type: method,
        data: data,
        success: function (response) {
          $('.datatables-basic tbody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Updating...</span></div></td></tr>');
          dt_basic.ajax.reload(function () {
            offCanvasEl.hide();
            $('#form-add-new-record').removeAttr('data-id');
            $('.dt-full-name').val('');
            toastr.success(message);
          });
        },
        error: function (error) {
          var errMsg = (error.responseJSON && error.responseJSON.message) ? error.responseJSON.message : 'An error occurred.';
          toastr.error(errMsg);
          console.log(error);
        },
        complete: function () {
          $submitBtn.prop('disabled', false);
          $submitSpinner.addClass('d-none');
          $submitText.text('Submit');
          $cancelBtn.prop('disabled', false);
        }
      });
    }
  });

  // Delete Record
  $('.datatables-basic tbody').on('click', '.delete-record', function () {
    var row = dt_basic.row($(this).parents('tr'));
    var data = row.data();
    var $deleteBtn = $(this);

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) {
        $deleteBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        $deleteBtn.prop('disabled', true);

        var deleteUrl = (typeof window.zoneUrls !== 'undefined' && window.zoneUrls.destroy) ? window.zoneUrls.destroy + '/' + data.id : (window.AppUtils && window.AppUtils.buildUrl ? window.AppUtils.buildUrl('app/settings/zone/' + data.id) : '/app/settings/zone/' + data.id);
        $.ajax({
          url: deleteUrl,
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            row.remove().draw();
            toastr.success('Zone has been deleted.');
          },
          error: function (error) {
            var errMsg = (error.responseJSON && error.responseJSON.message) ? error.responseJSON.message : 'An error occurred.';
            toastr.error(errMsg);
            console.log(error);
          },
          complete: function () {
            $deleteBtn.html('<i class="ti ti-trash ti-md"></i>');
            $deleteBtn.prop('disabled', false);
          }
        });
      }
    });
  });

  // Edit Record
  $('.datatables-basic tbody').on('click', '.item-edit', function () {
    var $editBtn = $(this);
    var row = dt_basic.row($(this).parents('tr'));
    var data = row.data();

    $editBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    $editBtn.prop('disabled', true);

    setTimeout(function () {
      offCanvasEl = new bootstrap.Offcanvas(document.querySelector('#add-new-record'));
      document.querySelector('.dt-full-name').value = data.zone_name;
      $('#form-add-new-record').attr('data-id', data.id);
      offCanvasEl.show();
      $editBtn.html('<i class="ti ti-pencil ti-md"></i>');
      $editBtn.prop('disabled', false);
    }, 200);
  });

  function showAddButtonSpinner(button) {
    var originalContent = button.innerHTML;
    button.setAttribute('data-original-content', originalContent);
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-sm-1"></span> <span class="d-none d-sm-inline-block">Loading...</span>';
    button.disabled = true;
  }

  function hideAddButtonSpinner(button) {
    var originalContent = button.getAttribute('data-original-content');
    if (originalContent) {
      button.innerHTML = originalContent;
      button.removeAttribute('data-original-content');
    }
    button.disabled = false;
  }
});
