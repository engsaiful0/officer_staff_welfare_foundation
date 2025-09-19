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
          // Show spinner on add button
          showAddButtonSpinner(this);
          
          setTimeout(function() {
            offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
            // Empty fields on offCanvas open
            offCanvasElement.querySelector('.dt-full-name').value = '';
            $('#form-add-new-record').removeAttr('data-id');
            // Open offCanvas with form
            offCanvasEl.show();
            hideAddButtonSpinner(newRecord);
          }, 200);
        });
      }
    }, 200);

    // Form validation for Add new record
    fv = FormValidation.formValidation(formAddNewRecord, {
      fields: {
        designation_name: {
          validators: {
            notEmpty: {
              message: 'The designation name is required'
            }
          }
        },
        designation_type: {
          validators: {
            notEmpty: {
              message: 'The designation type is required'
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
        url: 'app/settings/get-designation',
        type: 'GET',
        dataSrc: 'data',
        beforeSend: function() {
          // Show loading message in table body
          $('.datatables-basic tbody').html('<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        }
      },
      columns: [
        { data: 'id' },
        { data: 'designation_name' },
        { data: 'designation_type' },
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
              return 'Details of ' + data['designation_name'] + data['designation_type'];
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
    $('div.head-label').html('<h5 class="card-title mb-0">designations</h5>');
  }

  // Add/Update Record
  fv.on('core.form.valid', function () {
    var $new_name = $('.add-new-record .dt-full-name').val();
    var $new_type = $('#designation_type').val();
    var id = $('#form-add-new-record').attr('data-id');

    if ($new_name != '') {
      var url = '/app/settings/designation';
      var method = 'POST';
      var message = 'Designation added successfully.';
      var data = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        designation_name: $new_name,
        designation_type: $new_type
      };

      if (id) {
        url = '/app/settings/designation/' + id;
        method = 'PUT';
        message = 'Designation updated successfully.';
      }

      // Show spinner immediately
      console.log('Showing submit spinner...');
      showSubmitSpinner();

      $.ajax({
        url: url,
        type: method,
        data: data,
        success: function (response) {
          // Show loading state for table reload
          $('.datatables-basic tbody').html('<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Updating...</span></div></td></tr>');
          
          dt_basic.ajax.reload(function() {
            offCanvasEl.hide();
            $('#form-add-new-record').removeAttr('data-id');
            // Reset form fields
            $('.dt-full-name').val('');
            $('#designation_type').val('');
            toastr.success(message);
            hideSubmitSpinner();
          });
        },
        error: function (error) {
          hideSubmitSpinner();
          message = error.responseJSON.message || 'An error occurred';
          toastr.error(message);
          console.log(error);
        }
      });
    }
  });

  // Delete Record
  $('.datatables-basic tbody').on('click', '.delete-record', function () {
    var row = dt_basic.row($(this).parents('tr'));
    var data = row.data();
    var deleteBtn = $(this);
    
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
        // Show spinner on delete button
        showDeleteSpinner(deleteBtn);
        
        $.ajax({
          url: 'app/settings/designation/' + data.id,
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            row.remove().draw();
            toastr.success("Designation has been deleted.");
            hideDeleteSpinner(deleteBtn);
          },
          error: function (error) {
            hideDeleteSpinner(deleteBtn);
            toastr.error(error.responseJSON.message || 'An error occurred');
            console.log(error);
          }
        });
      }
    });
  });

  // Edit Record
  $('.datatables-basic tbody').on('click', '.item-edit', function () {
    var editBtn = $(this);
    var row = dt_basic.row($(this).parents('tr'));
    var data = row.data();
    
    // Show spinner on edit button
    showEditSpinner(editBtn);
    
    // Simulate a small delay for better UX
    setTimeout(function() {
      offCanvasEl = new bootstrap.Offcanvas(document.querySelector('#add-new-record'));
      document.querySelector('.dt-full-name').value = data.designation_name;
      document.querySelector('#designation_type').value = data.designation_type;
      $('#form-add-new-record').attr('data-id', data.id);
      offCanvasEl.show();
      hideEditSpinner(editBtn);
    }, 300);
  });

  // Spinner Functions
  function showSubmitSpinner() {
    console.log('showSubmitSpinner called');
    $('#submit-spinner').removeClass('d-none');
    $('#submit-text').text('Processing...');
    $('#submit-btn').prop('disabled', true);
    console.log('Submit spinner shown');
  }

  function hideSubmitSpinner() {
    console.log('hideSubmitSpinner called');
    $('#submit-spinner').addClass('d-none');
    $('#submit-text').text('Submit');
    $('#submit-btn').prop('disabled', false);
    console.log('Submit spinner hidden');
  }

  function showDeleteSpinner(button) {
    console.log('showDeleteSpinner called');
    button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    button.prop('disabled', true);
  }

  function hideDeleteSpinner(button) {
    console.log('hideDeleteSpinner called');
    button.html('<i class="ti ti-trash ti-md"></i>');
    button.prop('disabled', false);
  }

  function showTableSpinner() {
    if ($('#table-spinner').length === 0) {
      $('.datatables-basic').before('<div id="table-spinner" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    }
  }

  function hideTableSpinner() {
    $('#table-spinner').remove();
  }

  function showEditSpinner(button) {
    console.log('showEditSpinner called');
    button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    button.prop('disabled', true);
  }

  function hideEditSpinner(button) {
    console.log('hideEditSpinner called');
    button.html('<i class="ti ti-pencil ti-md"></i>');
    button.prop('disabled', false);
  }

  function showAddButtonSpinner(button) {
    console.log('showAddButtonSpinner called');
    const originalContent = button.innerHTML;
    button.setAttribute('data-original-content', originalContent);
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-sm-1"></span> <span class="d-none d-sm-inline-block">Loading...</span>';
    button.disabled = true;
  }

  function hideAddButtonSpinner(button) {
    console.log('hideAddButtonSpinner called');
    const originalContent = button.getAttribute('data-original-content');
    if (originalContent) {
      button.innerHTML = originalContent;
      button.removeAttribute('data-original-content');
    }
    button.disabled = false;
  }
});
