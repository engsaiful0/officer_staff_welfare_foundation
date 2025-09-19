/**
 * DataTables Basic
 */

'use strict';

// datatable (jquery)
$(function () {
  var dt_basic_table = $('.datatables-basic'),
    dt_date = new Date(),
    select2 = $('.select2'),
    offCanvasEl = $('#add-new-record'),
    offCanvasBootstrap = bootstrap.Offcanvas.getOrCreateInstance(offCanvasEl);

  // select2
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({
        placeholder: 'Select option',
        dropdownParent: $this.parent()
      });
    });
  }

  // DataTable with API URLs
  if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
      ajax: {
        url: window.feeSettingsUrls.getData,
        type: 'GET'
      },
      columns: [
        { data: 'id' },
        { data: 'name' },
        { 
          data: 'amount',
          render: function (data, type, row) {
            return '৳' + parseFloat(data).toFixed(2);
          }
        },
        { 
          data: 'fine_type',
          render: function (data, type, row) {
            return data === 'percentage' ? 'Percentage' : 'Fixed Amount';
          }
        },
        { 
          data: 'payment_deadline_day',
          render: function (data, type, row) {
            var suffix = 'th';
            if (data == 1) suffix = 'st';
            else if (data == 2) suffix = 'nd';
            else if (data == 3) suffix = 'rd';
            return data + suffix + ' of month';
          }
        },
        { 
          data: 'grace_period_days',
          render: function (data, type, row) {
            return data + ' days';
          }
        },
        { 
          data: 'is_active',
          render: function (data, type, row) {
            return data ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
          }
        },
        {
          // Actions
          targets: -1,
          title: 'Actions',
          orderable: false,
          render: function (data, type, full, meta) {
            return (
              '<div class="d-inline-block">' +
              '<a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="text-primary ti ti-dots-vertical"></i></a>' +
              '<ul class="dropdown-menu dropdown-menu-end m-0">' +
              '<li><a href="javascript:;" class="dropdown-item">Details</a></li>' +
              '<li><a href="javascript:;" class="dropdown-item edit-record">Edit</a></li>' +
              '<li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a></li>' +
              '</ul>' +
              '</div>' +
              '<a href="javascript:;" class="btn btn-sm btn-icon item-edit edit-record"><i class="text-primary ti ti-pencil"></i></a>'
            );
          }
        }
      ],
      columnDefs: [
        {
          className: 'control',
          orderable: false,
          targets: 0
        },
        {
          targets: -1,
          title: 'Actions',
          orderable: false,
          render: function (data, type, full, meta) {
            return (
              '<div class="d-inline-block">' +
              '<a href="javascript:;" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="text-primary ti ti-dots-vertical"></i></a>' +
              '<ul class="dropdown-menu dropdown-menu-end m-0">' +
              '<li><a href="javascript:;" class="dropdown-item edit-record">Edit</a></li>' +
              '<li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a></li>' +
              '</ul>' +
              '</div>' +
              '<a href="javascript:;" class="btn btn-sm btn-icon item-edit edit-record"><i class="text-primary ti ti-pencil"></i></a>'
            );
          }
        }
      ],
      order: [[1, 'desc']],
      dom:
        '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      displayLength: 7,
      lengthMenu: [7, 10, 25, 50, 75, 100],
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-primary dropdown-toggle me-2',
          text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
          buttons: [
            {
              extend: 'print',
              text: '<i class="ti ti-printer me-1" ></i>Print',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              },
              customize: function (win) {
                $(win.document.body)
                  .css('color', config.colors.headingColor)
                  .css('border-color', config.colors.borderColor)
                  .css('background-color', config.colors.bodyBg);
                $(win.document.body)
                  .find('table')
                  .addClass('compact')
                  .css('color', 'inherit')
                  .css('border-color', 'inherit')
                  .css('background-color', 'inherit');
              }
            },
            {
              extend: 'csv',
              text: '<i class="ti ti-file-text me-1" ></i>Csv',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'excel',
              text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'pdf',
              text: '<i class="ti ti-file-description me-1"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            },
            {
              extend: 'copy',
              text: '<i class="ti ti-copy me-1" ></i>Copy',
              className: 'dropdown-item',
              exportOptions: {
                columns: [1, 2, 3, 4, 5, 6],
                format: {
                  body: function (inner, coldex, rowdex) {
                    if (inner.length <= 0) return inner;
                    var el = $.parseHTML(inner);
                    var result = '';
                    $.each(el, function (index, item) {
                      if (item.classList !== undefined && item.classList.contains('user-name')) {
                        result = result + item.lastChild.firstChild.textContent;
                      } else if (item.innerText === undefined) {
                        result = result + item.textContent;
                      } else result = result + item.innerText;
                    });
                    return result;
                  }
                }
              }
            }
          ]
        },
        {
          text: '<i class="ti ti-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Fee Settings</span>',
          className: 'create-new btn btn-primary'
        }
      ],
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
      }
    });
    $('div.head-label').html('<h5 class="card-title mb-0">Fee Settings</h5>');
  }

  // Form Validation
  const fv = FormValidation.formValidation(document.getElementById('form-add-new-record'), {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: 'Please enter fee settings name'
          }
        }
      },
      amount: {
        validators: {
          notEmpty: {
            message: 'Please enter fee amount'
          },
          numeric: {
            message: 'Fee amount must be numeric'
          }
        }
      },
      payment_deadline_day: {
        validators: {
          notEmpty: {
            message: 'Please select payment deadline day'
          }
        }
      },
      fine_type: {
        validators: {
          notEmpty: {
            message: 'Please select fine type'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: function (field, ele) {
          return '.col-sm-12';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  // Handle fine type change
  $('input[name="fine_type"]').on('change', function() {
    var selectedType = $(this).val();
    if (selectedType === 'percentage') {
      $('#fixedFineSection').hide();
      $('#percentageFineSection').show();
      $('#fine_amount_per_day').removeAttr('required');
      $('#fine_percentage').attr('required', 'required');
    } else {
      $('#fixedFineSection').show();
      $('#percentageFineSection').hide();
      $('#fine_percentage').removeAttr('required');
      $('#fine_amount_per_day').attr('required', 'required');
    }
  });

  // Add/Update Record
  fv.on('core.form.valid', function () {
    var formData = new FormData($('#form-add-new-record')[0]);
    var id = $('#form-add-new-record').attr('data-id');

    var url = window.feeSettingsUrls.store;
    var method = 'POST';
    var message = 'Fee Settings added successfully.';

    if (id) {
      url = window.feeSettingsUrls.store + '/' + id;
      method = 'POST';
      message = 'Fee Settings updated successfully.';
      formData.append('_method', 'PUT');
    }

    // Get submit button
    var $submitBtn = $('#form-add-new-record button[type="submit"]');
    // Disable button and show spinner
    $submitBtn.prop('disabled', true).html('<i class="ti ti-loader ti-xs me-2 spinner-border spinner-border-sm"></i> Saving...');

    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        dt_basic.ajax.reload();
        offCanvasBootstrap.hide();
        $('#form-add-new-record').removeAttr('data-id');
        $('#form-add-new-record')[0].reset();
        
        // Reset fine type display
        $('#fixedFineSection').show();
        $('#percentageFineSection').hide();
        $('#fineTypeFixed').prop('checked', true);
        
        toastr.success(message);

        // Reset button text
        $submitBtn.prop('disabled', false).html('Submit');
      },
      error: function (error) {
        var errorMsg = 'An error occurred.';
        if (error.responseJSON && error.responseJSON.errors) {
          var errors = error.responseJSON.errors;
          var errorMessages = '';
          for (var key in errors) {
            if (errors.hasOwnProperty(key)) {
              errorMessages += errors[key][0] + '<br>';
            }
          }
          errorMsg = errorMessages;
        } else if (error.responseJSON && error.responseJSON.message) {
          errorMsg = error.responseJSON.message;
        }
        
        toastr.error(errorMsg);
        console.log(error);

        // Reset button text on error
        $submitBtn.prop('disabled', false).html('Submit');
      }
    });
  });

  // Delete Record
  $('.datatables-basic tbody').on('click', '.delete-record', function () {
    var row = $(this).closest('tr');
    var data = dt_basic.row(row).data();
    
    Swal.fire({
      title: 'Are you sure?',
      text: 'You want to delete this fee settings: ' + data.name,
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
          url: window.feeSettingsUrls.store + '/' + data.id,
          type: 'POST',
          data: {
            '_method': 'DELETE',
            '_token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function () {
            dt_basic.ajax.reload();
            toastr.success('Fee Settings deleted successfully.');
          },
          error: function (error) {
            toastr.error('An error occurred while deleting.');
            console.log(error);
          }
        });
      }
    });
  });

  // Edit Record
  $('.datatables-basic tbody').on('click', '.edit-record', function () {
    var row = $(this).closest('tr');
    var data = dt_basic.row(row).data();
    
    // Populate form with data
    $('#form-add-new-record').attr('data-id', data.id);
    $('#name').val(data.name);
    $('#amount').val(data.amount);
    $('#payment_deadline_day').val(data.payment_deadline_day);
    $('#fine_amount_per_day').val(data.fine_amount_per_day);
    $('#fine_percentage').val(data.fine_percentage);
    $('#maximum_fine_amount').val(data.maximum_fine_amount);
    $('#grace_period_days').val(data.grace_period_days);
    $('#notes').val(data.notes);
    
    // Set fine type
    if (data.fine_type === 'percentage') {
      $('#fineTypePercentage').prop('checked', true);
      $('#fixedFineSection').hide();
      $('#percentageFineSection').show();
    } else {
      $('#fineTypeFixed').prop('checked', true);
      $('#fixedFineSection').show();
      $('#percentageFineSection').hide();
    }
    
    // Change offcanvas title
    $('.offcanvas-title').text('Edit Fee Settings');
    
    // Show offcanvas
    offCanvasBootstrap.show();
  });

  // Create new record
  $('.create-new').on('click', function () {
    $('#form-add-new-record').removeAttr('data-id');
    $('#form-add-new-record')[0].reset();
    $('#fixedFineSection').show();
    $('#percentageFineSection').hide();
    $('#fineTypeFixed').prop('checked', true);
    $('.offcanvas-title').text('New Fee Settings');
    offCanvasBootstrap.show();
  });

  // To remove ordering when modal opens
  $('.dt-buttons > .btn-group > .btn').on('click', function() {
    if ($(this).text().includes('Export')) {
      setTimeout(() => {
        $('.dt-button-background').on('click', function() {
          $('.dt-button-collection').removeClass('show');
        });
      }, 100);
    }
  });
});
