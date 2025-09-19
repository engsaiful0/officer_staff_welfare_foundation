/**
 * DataTables Basic
 */

'use strict';

let fv, offCanvasEl;
document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const formAddNewRecord = document.getElementById('form-add-new-record');

    setTimeout(() => {
      const newRecord = document.querySelector('.create-new'),
        offCanvasElement = document.querySelector('#add-new-record');

      // To open offCanvas, to add new record
      if (newRecord) {
        newRecord.addEventListener('click', function () {
          offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
          // Empty fields on offCanvas open
          offCanvasElement.querySelector('.dt-full-name').value = '';
          $('#form-add-new-record').removeAttr('data-id');
          // Open offCanvas with form
          offCanvasEl.show();
        });
      }
    }, 200);

    // Form validation for Add new record
    fv = FormValidation.formValidation(formAddNewRecord, {
      fields: {
        relation_name: {
          validators: {
            notEmpty: {
              message: 'The relation name is required'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          // Use this for enabling/changing valid/invalid class
          // eleInvalidClass: '',
          eleValidClass: '',
          rowSelector: '.col-sm-12'
        }),
        submitButton: new FormValidation.plugins.SubmitButton(),
        // defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
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

// datatable (jquery)
$(function () {
  var dt_basic_table = $('.datatables-basic'),
    dt_basic;

  // DataTable with buttons
  // --------------------------------------------------------------------

  if (dt_basic_table.length) {
    dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: AppUtils.buildUrl('app/settings/get-relation'),
        type: 'GET',
        dataSrc: 'data',
        beforeSend: function() {
          // Show loading message
          $('.datatables-basic tbody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        }
      },
      columns: [
        { data: 'id' },
        { data: 'relation_name' },
        { data: '' }
      ],
      columnDefs: [
        {
          // Actions
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
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['relation_name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                col.rowIndex +
                '" data-dt-column="' +
                col.columnIndex +
                '">' +
                '<td>' +
                col.title +
                ':' +
                '</td> ' +
                '<td>' +
                col.data +
                '</td>' +
                '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      },
      initComplete: function (settings, json) {
        $('.card-header').after('<hr class="my-0">');
      }
    });
    $('div.head-label').html('<h5 class="card-title mb-0">Relations</h5>');
  }

  // Add/Update Record
  fv.on('core.form.valid', function () {
    var $new_name = $('.add-new-record .dt-full-name').val();
    var id = $('#form-add-new-record').attr('data-id');
    var $submitBtn = $('.data-submit');
    var $cancelBtn = $('.btn-outline-secondary');

    if ($new_name != '') {
      var url = AppUtils.buildUrl('app/settings/relation');
      var method = 'POST';
      var message = 'Relation added successfully.';
      var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        relation_name: $new_name
      };

      if (id) {
        url = AppUtils.buildUrl('app/settings/relation/' + id);
        method = 'PUT';
        message = 'Relation updated successfully.';
      }

      // Show loading state
      $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...');
      $cancelBtn.prop('disabled', true);

      $.ajax({
        url: url,
        type: method,
        data: data,
        success: function (response) {
          // Show loading state for table reload
          $('.datatables-basic tbody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Updating...</span></div></td></tr>');
          
          dt_basic.ajax.reload(function() {
            offCanvasEl.hide();
            $('#form-add-new-record').removeAttr('data-id');
            // Reset form fields
            $('.dt-full-name').val('');
            toastr.success(message);
          });
        },
        error: function (error) {
          if (error.responseJSON && error.responseJSON.message) {
            message = error.responseJSON.message;
          } else {
            message = 'An error occurred while processing your request.';
          }
          toastr.error(message);
          console.log(error);
        },
        complete: function() {
          // Reset button state
          $submitBtn.prop('disabled', false).html('Submit');
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
        // Show loading state on delete button
        $deleteBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        $deleteBtn.prop('disabled', true);
        
        $.ajax({
          url: AppUtils.buildUrl('app/settings/relation/' + data.id),
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            row.remove().draw();
            toastr.success("Relation has been deleted.");
          },
          error: function (error) {
            if (error.responseJSON && error.responseJSON.message) {
              toastr.error(error.responseJSON.message);
            } else {
              toastr.error('An error occurred while deleting the relation.');
            }
            console.log(error);
          },
          complete: function() {
            // Reset button state
            $deleteBtn.html('<i class="ti ti-trash ti-md"></i>');
            $deleteBtn.prop('disabled', false);
          }
        });
      }
    });
  });

  // Edit Record
  $('.datatables-basic tbody').on('click', '.item-edit', function () {
    var row = dt_basic.row($(this).parents('tr'));
    var data = row.data();
    offCanvasEl = new bootstrap.Offcanvas(document.querySelector('#add-new-record'));
    document.querySelector('.dt-full-name').value = data.relation_name;
    $('#form-add-new-record').attr('data-id', data.id);
    offCanvasEl.show();
  });
});
