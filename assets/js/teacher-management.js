'use strict';

$(function () {
  // Auto-submit form when per_page changes
  $('#per_page').on('change', function() {
    $(this).closest('form').submit();
  });

  // Delete Record
  $(document).on('click', '.delete-teacher', function () {
    var teacherId = $(this).data('id');
    var deleteUrl = $(this).data('url');
    var row = $(this).closest('tr');
    
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
          url: deleteUrl,
          type: 'DELETE',
          data: {
            _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            row.fadeOut(300, function() {
              $(this).remove();
            });
            toastr.success(response.message);
            // Reload page after 1 second to refresh pagination
            setTimeout(function() {
              window.location.reload();
            }, 1000);
          },
          error: function (error) {
            toastr.error(error.responseJSON.message);
            console.log(error);
          }
        });
      }
    });
  });


  // Image preview functionality
  function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#' + previewId).attr('src', e.target.result).show();
      }
      reader.readAsDataURL(input.files[0]);
    }
  }

  // Profile picture preview
  $('#picture').on('change', function() {
    previewImage(this, 'picture-preview');
    // Hide current picture when new one is selected
    $('#current-picture').hide();
  });

  // NID picture preview
  $('#nid_picture').on('change', function() {
    previewImage(this, 'nid-picture-preview');
    // Hide current NID picture when new one is selected
    $('#current-nid-picture').hide();
  });

  // Comprehensive form validation
  function validateForm(formId) {
    var isValid = true;
    var errors = [];
    var $form = $('#' + formId);

    // Clear previous validation states
    $form.find('.is-invalid').removeClass('is-invalid');
    $form.find('.invalid-feedback').remove();

    // Required field validation
    var requiredFields = [
      { id: 'teacher_name', name: 'Teacher Name' },
      { id: 'father_name', name: 'Father\'s Name' },
      { id: 'mother_name', name: 'Mother\'s Name' },
      { id: 'mobile', name: 'Mobile' },
      { id: 'gender', name: 'Gender' },
      { id: 'designation_id', name: 'Designation' },
      { id: 'basic_salary', name: 'Basic Salary' },
      { id: 'house_rent', name: 'House Rent' }
    ];

    // Check required fields
    requiredFields.forEach(function(field) {
      var $field = $form.find('#' + field.id);
      var value = $field.val();
      
      if (!value || value.trim() === '') {
        showFieldError($field, field.name + ' is required');
        isValid = false;
      }
    });

    // Email validation
    var email = $form.find('#email').val();
    if (email && !isValidEmail(email)) {
      showFieldError($form.find('#email'), 'Please enter a valid email address');
      isValid = false;
    }

    // Mobile validation
    var mobile = $form.find('#mobile').val();
    if (mobile && !isValidMobile(mobile)) {
      showFieldError($form.find('#mobile'), 'Please enter a valid mobile number');
      isValid = false;
    }

    // GPA validation
    var bachelorGpa = $form.find('#bachelor_or_equivalent_gpa').val();
    if (bachelorGpa && (bachelorGpa < 0 || bachelorGpa > 4)) {
      showFieldError($form.find('#bachelor_or_equivalent_gpa'), 'Bachelor GPA must be between 0 and 4');
      isValid = false;
    }

    var masterGpa = $form.find('#master_or_equivalent_gpa').val();
    if (masterGpa && (masterGpa < 0 || masterGpa > 4)) {
      showFieldError($form.find('#master_or_equivalent_gpa'), 'Master GPA must be between 0 and 4');
      isValid = false;
    }

    // Allowance validation
    var medicalAllowance = $form.find('#medical_allowance').val();
    if (medicalAllowance && medicalAllowance < 0) {
      showFieldError($form.find('#medical_allowance'), 'Medical allowance cannot be negative');
      isValid = false;
    }

    var otherAllowance = $form.find('#other_allowance').val();
    if (otherAllowance && otherAllowance < 0) {
      showFieldError($form.find('#other_allowance'), 'Other allowance cannot be negative');
      isValid = false;
    }

    // File validation
    var picture = $form.find('#picture')[0];
    if (picture && picture.files.length > 0) {
      if (!isValidImageFile(picture.files[0])) {
        showFieldError($form.find('#picture'), 'Please select a valid image file (JPEG, PNG, JPG, GIF)');
        isValid = false;
      }
    }

    var nidPicture = $form.find('#nid_picture')[0];
    if (nidPicture && nidPicture.files.length > 0) {
      if (!isValidImageFile(nidPicture.files[0])) {
        showFieldError($form.find('#nid_picture'), 'Please select a valid image file (JPEG, PNG, JPG, GIF)');
        isValid = false;
      }
    }

    if (!isValid) {
      toastr.error('Please fix the validation errors before submitting', 'Validation Error', {
        timeOut: 5000,
        closeButton: true
      });
    }

    return isValid;
  }

  // Helper functions for validation
  function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function isValidMobile(mobile) {
    var mobileRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
    return mobileRegex.test(mobile);
  }

  function isValidImageFile(file) {
    var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    var maxSize = 2 * 1024 * 1024; // 2MB
    return allowedTypes.includes(file.type) && file.size <= maxSize;
  }

  function showFieldError($field, message) {
    $field.addClass('is-invalid');
    $field.after('<div class="invalid-feedback">' + message + '</div>');
  }

  // Real-time validation
  function setupRealTimeValidation(formId) {
    var $form = $('#' + formId);
    
    // Remove error state on input
    $form.find('input, select, textarea').on('input change', function() {
      var $this = $(this);
      $this.removeClass('is-invalid');
      $this.next('.invalid-feedback').remove();
    });

    // Clear all validation errors on form reset
    $form.on('reset', function() {
      $form.find('.is-invalid').removeClass('is-invalid');
      $form.find('.invalid-feedback').remove();
      $('#picture-preview, #nid-picture-preview').hide();
    });

    // Email validation on blur
    $form.find('#email').on('blur', function() {
      var email = $(this).val();
      if (email && !isValidEmail(email)) {
        showFieldError($(this), 'Please enter a valid email address');
      }
    });

    // Mobile validation on blur
    $form.find('#personal_number').on('blur', function() {
      var mobile = $(this).val();
      if (mobile && !isValidMobile(mobile)) {
        showFieldError($(this), 'Please enter a valid mobile number');
      }
    });

    // GPA validation on blur
    $form.find('#bachelor_or_equivalent_gpa, #master_or_equivalent_gpa').on('blur', function() {
      var gpa = parseFloat($(this).val());
      if (!isNaN(gpa) && (gpa < 0 || gpa > 4)) {
        showFieldError($(this), 'GPA must be between 0 and 4');
      }
    });

    // Allowance validation on blur
    $form.find('#medical_allowance, #other_allowance').on('blur', function() {
      var value = parseFloat($(this).val());
      if (!isNaN(value) && value < 0) {
        showFieldError($(this), 'Allowance cannot be negative');
      }
    });
  }

  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Setup real-time validation for create form
  setupRealTimeValidation('createTeacherForm');

  // create teacher
  $('#createTeacherForm').on('submit', function (e) {
    e.preventDefault();
    
    // Validate form before submission
    if (!validateForm('createTeacherForm')) {
      return false;
    }
    
    var form = $(this);
    var url = form.attr('action');
    var method = form.attr('method');
    var data = new FormData(this);
    var submitBtn = form.find('button[type="submit"]');
    var spinner = submitBtn.find('.spinner-border');

    // disable form inputs & button
    form.find('input, select, textarea, button').prop('disabled', true);
    spinner.removeClass('d-none');

    $.ajax({
      url: url,
      method: method,
      data: data,
      processData: false,
      contentType: false,
      success: function (response) {
        spinner.addClass('d-none');
        form.find('input, select, textarea, button').prop('disabled', false);

        if (response.success) {
          toastr.success(response.message, 'Success', {
            timeOut: 3000,
            closeButton: true
          });

          // reset form after successful save
          form[0].reset();
          // Clear validation states
          form.find('.is-invalid').removeClass('is-invalid');
          form.find('.invalid-feedback').remove();
          // Reset image previews
          $('#picture-preview, #nid-picture-preview').hide();
        } else {
          toastr.error(response.message);
        }
      },
      error: function (xhr) {
        spinner.addClass('d-none');
        form.find('input, select, textarea, button').prop('disabled', false);

        var errors = xhr.responseJSON?.errors;
        var errorMessages = '';
        if (errors) {
          $.each(errors, function (key, value) {
            errorMessages += value[0] + '\n';
          });
        } else {
          errorMessages = 'Something went wrong. Please try again.';
        }
        toastr.error(errorMessages, 'Error', {
          timeOut: 5000,
          closeButton: true
        });
      }
    });
});


  // Setup real-time validation for edit form
  setupRealTimeValidation('editTeacherForm');

  // edit teacher
  $('#editTeacherForm').on('submit', function (e) {
    e.preventDefault();
    
    // Validate form before submission
    if (!validateForm('editTeacherForm')) {
      return false;
    }
    
    var form = $(this);
    var url = form.attr('action');
    var method = form.attr('method');
    var data = new FormData(this);
    var submitBtn = form.find('button[type="submit"]');
    var spinner = submitBtn.find('.spinner-border');

    spinner.removeClass('d-none');
    submitBtn.addClass('disabled');

    $.ajax({
      url: url,
      method: 'POST', // method spoofing
      data: data,
      processData: false,
      contentType: false,
      success: function (response) {
        spinner.addClass('d-none');
        submitBtn.removeClass('disabled');
        if (response.success) {
          toastr.success(response.message, 'Success', {
            timeOut: 3000,
            closeButton: true
          });
          window.location.href = '/app/teachers/view-teacher';
        } else {
          toastr.error('Something went wrong!', 'Error', {
            timeOut: 3000,
            closeButton: true
          });
        }
      },
      error: function (xhr) {
        spinner.addClass('d-none');
        submitBtn.removeClass('disabled');
        var errors = xhr.responseJSON.errors;
        var errorMessages = '';
        $.each(errors, function (key, value) {
          errorMessages += value[0] + '\n';
        });
        toastr.error(errorMessages, 'Error', {
          timeOut: 5000,
          closeButton: true
        });
      }
    });
  });
});

