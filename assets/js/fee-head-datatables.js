/**
 * DataTables for Fee Heads
 */

'use strict';

let fv, offCanvasEl;

// Function to initialize Semester Select2
function initializeSemesterSelect(selector) {
  const semesterSelect = $(selector);
  if (semesterSelect.length) {
    $.ajax({
      url: window.semesterUrls.getData,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        const semesterData = $.map(data.data, function (semester) {
          return {
            id: semester.id,
            text: semester.semester_name,
          };
        });

        semesterSelect.select2({
          placeholder: 'Select a semester',
          dropdownParent: semesterSelect.closest('.offcanvas'),
          data: semesterData
        });
      }
    });
  }
}
function initializeMonthSelect(selector) {
  const monthSelect = $(selector);
  if (monthSelect.length) {
    $.ajax({
      url: window.monthUrls.getData,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        const monthData = $.map(data.data, function (month) {
          return {
            id: month.id,
            text: month.month_name,
          };
        });

        // Initialize select2 after data is loaded
        monthSelect.select2({
          placeholder: 'Select a month',
          dropdownParent: monthSelect.closest('.offcanvas'),
          data: monthData
        });

        // Set placeholder after initialization to ensure it appears
        if (monthData.length === 0) {
          monthSelect.select2('val', null).trigger('change');
        }
      }
    });
  }
}


document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const formAddNewRecord = document.getElementById('addNewFeeHeadForm');
    const offCanvasElement = document.querySelector('#offcanvasAddFeeHead');

    // To open offCanvas, to add new record
    setTimeout(() => {
      const newRecord = document.querySelector('.create-new');
      if (newRecord) {
        newRecord.addEventListener('click', function () {
          offCanvasEl = new bootstrap.Offcanvas(offCanvasElement);
          // Empty fields on offCanvas open
          $('#addNewFeeHeadForm').trigger('reset');
          $('#addNewFeeHeadForm').removeAttr('data-id');
          $('.semester-select').val(null).trigger('change');
          // Open offCanvas with form
          offCanvasEl.show();
        });
      }
    }, 200);

    // Form validation for Add new record
    fv = FormValidation.formValidation(formAddNewRecord, {
      fields: {
        name: {
          validators: {
            notEmpty: {
              message: 'The fee head name is required'
            }
          }
        },
        amount: {
          validators: {
            notEmpty: {
              message: 'The fee head amount is required'
            }
          }
        },
        fee_type: {
          validators: {
            notEmpty: {
              message: 'The fee type is required'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.mb-3'
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

    // Initialize Semester Select2
    initializeSemesterSelect('.semester-select');
    initializeMonthSelect('.month-select');

    // Handle Fee Type change
    $('#fee_type').on('change', function () {
      var feeType = $(this).val();
      if (feeType === 'Monthly') {
        $('#month_id').closest('.mb-3').show();
        $('#semester_id').closest('.mb-3').hide();
        if (fv.fields.semester_id) {
          fv.removeField('semester_id');
        }
        fv.addField('month_id', {
          validators: {
            notEmpty: {
              message: 'The month is required'
            }
          }
        });
      } else if (feeType === 'Regular') {
        $('#month_id').closest('.mb-3').hide();
        $('#semester_id').closest('.mb-3').show();
        if (fv.fields.month_id) {
          fv.removeField('month_id');
        }
        fv.addField('semester_id', {
          validators: {
            notEmpty: {
              message: 'The semester is required'
            }
          }
        });
      }
    });
    $('#fee_type').trigger('change');

  })();
});

// datatable (jquery)
$(function () {
  var dt_basic_table = $('.datatables-fee-heads'),
    dt_basic;
  if (typeof window.feeHeadUrls === 'undefined') {
    console.error('Fee Head URLs not defined');
    return;
  }

  if (dt_basic_table.length) {
  dt_basic = dt_basic_table.DataTable({
    ajax: {
      url: window.feeHeadUrls.getData,
      type: 'GET',
      dataSrc: 'data'
    },
    columns: [
      { data: null },           // Auto Serial
      { data: 'name' },
      { data: 'fee_type' },
      { data: 'details' },      // Placeholder for combined column
      { data: 'amount' },
      { data: 'is_discountable' },
      { data: '' }              // Actions
    ],
    columnDefs: [
      {
        // Auto Serial Number (Continuous across pages)
        targets: 0,
        orderable: false,
        searchable: false,
        render: function (data, type, full, meta) {
          return meta.row + 1 + meta.settings._iDisplayStart;
        }
      },
      {
        targets: 3,
        title: 'Details',
        render: function (data, type, full, meta) {
          if (full.fee_type === 'Monthly') {
            return full.month ? full.month.month_name : 'N/A';
          } else if (full.fee_type === 'Regular') {
            return full.semester ? full.semester.semester_name : 'N/A';
          }
          return 'N/A';
        }
      },
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
    order: [[0, 'desc']], // Default ordering by Serial
    dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0">><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-n6 mt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    displayLength: 7,
    lengthMenu: [7, 10, 25, 50, 75, 100],
    language: {
      paginate: {
        next: '<i class="ti ti-chevron-right ti-sm"></i>',
        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
      }
    },
    buttons: [],
    responsive: {
      details: {
        display: $.fn.dataTable.Responsive.display.modal({
          header: function (row) {
            var data = row.data();
            return 'Details of ' + data['name'];
          }
        }),
        type: 'column',
        renderer: function (api, rowIdx, columns) {
          var data = $.map(columns, function (col, i) {
            return col.title !== ''
              ? '<tr data-dt-row="' +
                col.rowIndex +
                '" data-dt-column="' +
                col.columnIndex +
                '">' +
                '<td>' +
                col.title +
                ':</td> ' +
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

  $('div.head-label').html('<h5 class="card-title mb-0">Fee Heads</h5>');
}


  // Add/Update Record
  fv.on('core.form.valid', function () {
    var $name = $('#name').val();
    var $semester_id = $('#semester_id').val();
    var $amount = $('#amount').val();
    var $is_discountable = $('#is_discountable').val();
    var $fee_type = $('#fee_type').val();
    var $month_id = $('#month_id').val();
    var id = $('#addNewFeeHeadForm').attr('data-id');

    var url = '/app/settings/fee-head';
    var method = 'POST';
    var message = 'Fee Head added successfully.';
    var data = {
      _token: $('meta[name="csrf-token"]').attr('content'),
      name: $name,
      amount: $amount,
      semester_id: $semester_id,
      is_discountable: $is_discountable,
      fee_type: $fee_type,
      month_id: $month_id
    };

    if (id) {
      url = window.feeHeadUrls.update;
      method = 'PUT';
      message = 'Fee Head updated successfully.';
      data.id = id; // Add ID to request body for update
    }

    // Get submit button
    var $submitBtn = $('#addNewFeeHeadForm button[type="submit"]');
    // Disable button and show spinner
    $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
      url: url,
      type: method,
      data: data,
      success: function (response) {
        dt_basic.ajax.reload();
        offCanvasEl.hide();
        $('#addNewFeeHeadForm').trigger('reset');
        $('#addNewFeeHeadForm').removeAttr('data-id');
        toastr.success(response.message);

        // Reset button
        $submitBtn.prop('disabled', false).html('Save');
      },
      error: function (error) {
        if (error.responseJSON && error.responseJSON.errors) {
          var errors = error.responseJSON.errors;
          var errorMessages = '';
          for (var key in errors) {
            if (errors.hasOwnProperty(key)) {
              errorMessages += errors[key][0] + '<br>';
            }
          }
          toastr.error(errorMessages);
        } else {
          toastr.error('An error occurred.');
        }

        // Reset button on error too
        $submitBtn.prop('disabled', false).html('Save');
      }
    });
  });


  // Delete Record
  $('.datatables-fee-heads tbody').on('click', '.delete-record', function () {
    var row = dt_basic.row($(this).parents('tr'));
    var data = row.data();
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
        $.ajax({
          url: window.feeHeadUrls.destroy + '/' + data.id,
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            row.remove().draw();
            toastr.success(response.message);
          },
          error: function (error) {
            if (error.responseJSON && error.responseJSON.errors) {
              var errors = error.responseJSON.errors;
              var errorMessages = '';
              for (var key in errors) {
                if (errors.hasOwnProperty(key)) {
                  errorMessages += errors[key][0] + '<br>';
                }
              }
              toastr.error(errorMessages);
            } else {
              toastr.error('An error occurred.');
            }
          }
        });
      }
    });
  });

  // Edit Record
  $('.datatables-fee-heads tbody').on('click', '.item-edit', function () {
    var row = dt_basic.row($(this).parents('tr'));
    var data = row.data();
    offCanvasEl = new bootstrap.Offcanvas(document.querySelector('#offcanvasAddFeeHead'));
    $('#offcanvasAddFeeHeadLabel').text("Edit Fee Head");
    $('#name').val(data.name);
    $('#amount').val(data.amount);
    $('#is_discountable').val(data.is_discountable).trigger('change');
    $('#fee_type').val(data.fee_type).trigger('change');
    
    // Set semester or month based on fee type
    if (data.fee_type === 'Regular' && data.semester) {
      $('#semester_id').val(data.semester.id).trigger('change');
    } else if (data.fee_type === 'Monthly' && data.month) {
      $('#month_id').val(data.month.id).trigger('change');
    }
    
    $('#addNewFeeHeadForm').attr('data-id', data.id);
    offCanvasEl.show();
  });
});
