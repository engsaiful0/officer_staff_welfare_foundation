'use strict';

// Add CSS for form disabled state
$('<style>')
  .prop('type', 'text/css')
  .html(`
    .form-disabled {
      opacity: 0.6;
      pointer-events: none;
    }
    .form-overlay {
      cursor: wait !important;
    }
  
  `)
  .appendTo('head');

$(function () {
  try {
    console.log('Student management JavaScript loaded');
    console.log('Form exists:', $('#createStudentForm').length > 0);
    console.log('Submit button exists:', $('#submit-button').length > 0);

    // Check if personal number field exists
    if ($('#personal_number').length > 0) {
      console.log('Personal number field found');
      console.log('Field element:', $('#personal_number')[0]);
    } else {
      console.log('Personal number field NOT found');
      console.log('All input fields:', $('input[type="text"]').length);
      console.log('All inputs with id containing personal:', $('input[id*="personal"]').length);
    }

    // Wait a bit and check again in case of timing issues
    setTimeout(function () {
      console.log('Delayed check - Personal number field found:', $('#personal_number').length > 0);
    }, 1000);
  } catch (error) {
    console.error('Error in student management JavaScript:', error);
  }

  // Auto-submit form when per_page changes
  $('#per_page').on('change', function () {
    $(this).closest('form').submit();
  });

  // Delete Record
  $(document).on('click', '.delete-student', function () {
    var studentId = $(this).data('id');
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
            row.fadeOut(300, function () {
              $(this).remove();
            });
            toastr.success(response.message);
            // Reload page after 1 second to refresh pagination
            setTimeout(function () {
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

  // ajax setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Personal number duplicate validation
  let personalNumberTimeout;
  let emailTimeout;

  // Function to handle personal number validation
  function handlePersonalNumberValidation(e) {
    console.log('Personal number field event triggered:', e.type);
    const personalNumber = $(this).val().trim();
    const field = $(this);
    const statusDiv = field.siblings('.personal-number-status');

    console.log('Personal number value:', personalNumber);

    // Clear previous timeout
    clearTimeout(personalNumberTimeout);

    // Remove previous styling
    field.removeClass('is-valid is-invalid');
    statusDiv.remove();

    // Only validate if personal number is 11 digits
    if (personalNumber.length === 11 && /^[0-9]{11}$/.test(personalNumber)) {
      console.log('Personal number format is valid, checking uniqueness...');
      
      // Debounce the validation request
      personalNumberTimeout = setTimeout(function () {
        validatePersonalNumber(personalNumber, field);
      }, 500);
    } else if (personalNumber.length > 0) {
      console.log('Personal number format is invalid');
      // Show invalid format message
      field.addClass('is-invalid');
      field.after('<div class="personal-number-status unavailable">Please enter exactly 11 digits</div>');
    }
  }

  // Function to handle email validation
  function handleEmailValidation(e) {
    console.log('Email field event triggered:', e.type);
    const email = $(this).val().trim();
    const field = $(this);
    const errorDiv = field.siblings('.email-error');

    console.log('Email value:', email);

    // Clear previous timeout
    clearTimeout(emailTimeout);

    // Remove previous error styling
    field.removeClass('is-valid is-invalid is-loading');
    errorDiv.remove();

    // Basic email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email.length > 0) {
      if (emailRegex.test(email)) {
        console.log('Email format is valid, checking uniqueness...');
        // Add loading state
        field.addClass('is-loading');

        // Debounce the validation request
        emailTimeout = setTimeout(function () {
          validateEmail(email, field);
        }, 500);
      } else {
        console.log('Email format is invalid');
        // Show invalid format message
        field.addClass('is-invalid');
        field.after('<div class="email-error invalid-feedback">Please enter a valid email address</div>');
      }
    }
  }

  // // Try direct binding first for personal number
  // $('#personal_number').on('input blur click', handlePersonalNumberValidation);

  // // Try direct binding first for email
  // $('#email').on('input blur', handleEmailValidation);

  // // Also try document delegation as fallback
  // $(document).on('input blur click', '#personal_number', handlePersonalNumberValidation);
  // $(document).on('input blur', '#email', handleEmailValidation);

  // // Additional test - try to trigger manually
  // setTimeout(function () {
  //   if ($('#personal_number').length > 0) {
  //     console.log('Attempting to manually trigger event binding...');
  //     $('#personal_number').off('input blur click').on('input blur click', handlePersonalNumberValidation);

  //     // Test if we can manually trigger the validation
  //     console.log('Testing manual validation with test number...');
  //     $('#personal_number').val('01712345678');
  //     handlePersonalNumberValidation.call($('#personal_number')[0], { type: 'manual' });
  //   }
  // }, 2000);

  // Function to validate personal number
  function validatePersonalNumber(personalNumber, field) {
    console.log('validatePersonalNumber called with:', personalNumber);
    const studentId = $('#student_id').val(); // For edit form
    const statusDiv = field.siblings('.personal-number-status');

    console.log('Student ID for edit form:', studentId);
    console.log('Making AJAX request to check personal number...');

    $.ajax({
      url: window.appConfig ? window.appConfig.checkPersonalNumberUrl : '/students/check-personal-number-duplicate',
      method: 'POST',
      data: {
        personal_number: personalNumber,
        student_id: studentId
      },
      success: function (response) {
        console.log('AJAX success response:', response);
        
        if (response.exists) {
          console.log('Personal number already exists');
          field.addClass('is-invalid');
          field.after('<div class="personal-number-status unavailable">The number is already registered</div>');
        } else {
          console.log('Personal number is available');
          field.addClass('is-valid');
          field.after('<div class="personal-number-status available">The number is available</div>');
        }
      },
      error: function (xhr) {
        console.error('AJAX error:', xhr);
        field.addClass('is-invalid');
        let errorMessage = 'Error checking personal number availability';

        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = xhr.responseJSON.errors;
          if (errors.personal_number) {
            errorMessage = errors.personal_number[0];
          }
        }

        field.after('<div class="personal-number-status unavailable">' + errorMessage + '</div>');
        console.error('Error checking personal number:', xhr);
      }
    });
  }

  // Function to validate email
  function validateEmail(email, field) {
    console.log('validateEmail called with:', email);
    const studentId = $('#student_id').val(); // For edit form
    const errorDiv = field.siblings('.email-error');

    console.log('Student ID for edit form:', studentId);
    console.log('Making AJAX request to check email...');

    $.ajax({
      url: window.appConfig ? window.appConfig.checkEmailUrl : '/students/check-email-duplicate',
      method: 'POST',
      data: {
        email: email,
        student_id: studentId
      },
      success: function (response) {
        console.log('Email AJAX success response:', response);
        field.removeClass('is-loading');

        if (response.exists) {
          console.log('Email already exists');
          field.addClass('is-invalid');
          field.after('<div class="email-error invalid-feedback">' + response.message + '</div>');
        } else {
          console.log('Email is available');
          field.addClass('is-valid');
          field.after('<div class="email-error valid-feedback">' + response.message + '</div>');
        }
      },
      error: function (xhr) {
        console.error('Email AJAX error:', xhr);
        field.removeClass('is-loading');
        field.addClass('is-invalid');
        let errorMessage = 'Error checking email availability';

        if (xhr.responseJSON && xhr.responseJSON.errors) {
          const errors = xhr.responseJSON.errors;
          if (errors.email) {
            errorMessage = errors.email[0];
          }
        }

        field.after('<div class="email-error invalid-feedback">' + errorMessage + '</div>');
        console.error('Error checking email:', xhr);
      }
    });
  }

  // create student

  $('#submit-button').on('click', function () {
    let $form = $('#createStudentForm');
    let submitBtn = $('#submit-button');
    let spinner = $('#spinner');
    let buttonText = $('#button-text');

    // Prevent double click
    if (submitBtn.prop('disabled')) return;

    // Remove old validation states
    $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
    $form.find('.invalid-feedback, .valid-feedback').remove();

    // ✅ Prepare form data BEFORE disabling fields
    let formData = new FormData($form[0]);

    // Debug: Log form data
    console.log('Form action URL:', $form.attr('action'));
    console.log('Form data entries:', [...formData.entries()]);

    // Disable form fields after collecting data
    // $form.find('input, select, textarea, button').prop('disabled', true);

    // Show spinner
    spinner.removeClass('d-none');
    buttonText.text('Saving...');

    $.ajax({
      url: $form.attr('action'),
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        toastr.success(response.message || 'Student created successfully!');

        // Reset form after success
        $form[0].reset();

        // Reset select2 if used
        $form.find('.select2').val('').trigger('change');

        // Focus first field (optional UX improvement)
        $form.find('input, textarea, select').first().focus();
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          let errorMessages = [];
          
          $.each(errors, function (key, messages) {
            let input = $form.find(`[name="${key}"]`);

            if (input.length > 0) {
              input.addClass('is-invalid');

              // ✅ Handle select2 errors differently
              if (input.hasClass('select2')) {
                input.next('.select2-container')
                  .after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
              } else {
                input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
              }
              
              // Add field-specific error to toast message
              let fieldLabel = input.closest('.mb-3').find('label').text().replace('*', '').trim();
              errorMessages.push(`${fieldLabel}: ${messages[0]}`);
            }
          });
          
          // Show field-specific errors in toast
          if (errorMessages.length > 0) {
            toastr.error(errorMessages.join('<br>'), 'Validation Errors', {
              timeOut: 8000,
              closeButton: true,
              escapeHtml: false
            });
          } else {
            toastr.error('Please fix the errors and try again.');
          }
        } else {
          toastr.error(xhr.responseJSON?.message || 'Something went wrong!');
        }
      },
      complete: function () {
        // Re-enable form
        $form.find('input, select, textarea, button').prop('disabled', false);

        // Hide spinner
        spinner.addClass('d-none');
        buttonText.text('Save');
      }
    });
  });




  // Apply jQuery Validation
  $(document).ready(function () {
    console.log('Setting up form validation...');
    var validator = $('#createStudentForm').validate({
      rules: {
        // Personal Info
        full_name_in_banglai: { required: true },
        father_name_in_banglai: { required: true },
        mother_name_in_banglai: { required: true },
        mother_name_in_english_block_letter: { required: true },
        full_name_in_english_block_letter: { required: true },
        father_name_in_english_block_letter: { required: true },
        personal_number: {
          required: true,
          digits: true,
          minlength: 11,
          maxlength: 11
        },
        email: {
          required: true,
          email: true
        },
        guardian_phone: {
          required: true,
          digits: true,
          minlength: 11,
          maxlength: 11
        },
        present_address: { required: true },
        permanent_address: { required: true },
        academic_year_id: { required: true },
        semester_id: { required: true },
        nationality_id: { required: true },
        date_of_birth: { required: true, date: true },
        religion_id: { required: true },
        shift_id: { required: true },
        gender: { required: true },
        technology_id: { required: true },

        // Educational Info
        ssc_or_equivalent_institute_name: { required: true },
        ssc_or_equivalent_roll_number: {
          required: true,
          digits: true
        },
        ssc_or_equivalent_registration_number: {
          required: true,
          digits: true
        },
        ssc_or_equivalent_session_id: { required: true },
        ssc_or_equivalent_passing_year_id: { required: true },
        ssc_or_equivalent_gpa: {
          required: true,
          number: true,
          min: 1,
          max: 5
        },
        board_id: { required: true }
      },
      messages: {
        // Personal Info
        full_name_in_banglai: "Full name in Bangla is required",
        father_name_in_banglai: "Father's name in Bangla is required",
        mother_name_in_banglai: "Mother's name in Bangla is required",
        mother_name_in_english_block_letter: "Mother's name in English is required",
        full_name_in_english_block_letter: "Full name in English is required",
        father_name_in_english_block_letter: "Father's name in English is required",
        personal_number: {
          required: "Personal phone number is required",
          digits: "Only numbers allowed",
          minlength: "Must be exactly 11 digits",
          maxlength: "Must be exactly 11 digits"
        },
        email: {
          required: "Email is required",
          email: "Enter a valid email address"
        },
        guardian_phone: {
          required: "Guardian phone number is required",
          digits: "Only numbers allowed",
          minlength: "Must be exactly 11 digits",
          maxlength: "Must be exactly 11 digits"
        },
        present_address: "Present address is required",
        permanent_address: "Permanent address is required",
        academic_year_id: "Please select an academic year",
        semester_id: "Please select a semester",
        nationality_id: "Please select nationality",
        date_of_birth: "Date of birth is required",
        religion_id: "Please select religion",
        shift_id: "Please select shift",
        gender: "Please select gender",
        technology_id: "Please select technology",

        // Educational Info
        ssc_or_equivalent_institute_name: "SSC institute name is required",
        ssc_or_equivalent_roll_number: {
          required: "SSC roll number is required",
          digits: "Only numbers allowed"
        },
        ssc_or_equivalent_registration_number: {
          required: "SSC registration number is required",
          digits: "Only numbers allowed"
        },
        ssc_or_equivalent_session_id: "SSC session is required",
        ssc_or_equivalent_passing_year_id: "Passing year is required",
        ssc_or_equivalent_gpa: {
          required: "SSC GPA is required",
          number: "Enter a valid GPA",
          min: "Minimum GPA is 1.0",
          max: "Maximum GPA is 5.0"
        },
        board_id: "Please select a board"
      },
      errorElement: "span",
      errorClass: "invalid-feedback",
      highlight: function (element) {
        $(element).addClass("is-invalid");
      },
      unhighlight: function (element) {
        $(element).removeClass("is-invalid");
      },
      errorPlacement: function (error, element) {
        if (element.parent('.input-group').length) {
          error.insertAfter(element.parent());
        } else if (element.hasClass("select2")) {
          error.insertAfter(element.next('span')); // for select2
        } else {
          error.insertAfter(element);
        }
      },
      submitHandler: function (form) {
        // AJAX form submission
        console.log('Form submitted via submitHandler');
        submitStudentForm(form);
        return false; // Prevent default form submission
      }
    });

    console.log('Form validation setup complete');
    console.log('Validator object:', validator);
  });

  // Function to disable/enable form fields during submission
  function disableForm($form, disable) {
    var formFields = $form.find('input, select, textarea, button');

    if (disable) {
      // Disable all form fields
      formFields.each(function () {
        var $field = $(this);
        $field.data('original-disabled', $field.prop('disabled'));
        $field.prop('disabled', true);

        // Add visual styling for disabled state
        if (!$field.is('button')) {
          $field.addClass('form-disabled');
        }
      });

      // Add overlay to form
      if (!$form.find('.form-overlay').length) {
        $form.css('position', 'relative').append(
          '<div class="form-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.7); z-index: 1000; pointer-events: none;"></div>'
        );
      }
    } else {
      // Re-enable form fields based on their original state
      formFields.each(function () {
        var $field = $(this);
        var originalDisabled = $field.data('original-disabled') || false;
        $field.prop('disabled', originalDisabled);
        $field.removeClass('form-disabled');
      });

      // Remove overlay
      $form.find('.form-overlay').remove();
    }
  }

  // AJAX form submission function
  function submitStudentForm(form) {
    console.log('submitStudentForm called');
    var $form = $(form);
    var personalNumberField = $('#personal_number');
    var submitBtn = $form.find('#submit-button');

    console.log('Form element:', form);
    console.log('Form jQuery object:', $form);
    console.log('Form action:', $form.attr('action'));

    // Prevent double submission
    if (submitBtn.hasClass('disabled') || submitBtn.prop('disabled')) {
      console.log('Form submission already in progress');
      return;
    }

    // Check if personal number is valid before submitting
    if (personalNumberField.length > 0 && personalNumberField.hasClass('is-invalid')) {
      console.log('Personal number validation failed');
      toastr.error('Please ensure the personal number is valid and available', 'Validation Error', {
        timeOut: 3000,
        closeButton: true
      });
      return;
    }

    // Check if email is valid before submitting
    var emailField = $('#email');
    if (emailField.length > 0 && emailField.hasClass('is-invalid')) {
      console.log('Email validation failed');
      toastr.error('Please ensure the email is valid and available', 'Validation Error', {
        timeOut: 3000,
        closeButton: true
      });
      return;
    }

    var formData = new FormData(form);
    var spinner = submitBtn.find('#spinner');
    var buttonText = submitBtn.find('#button-text');

    // Disable entire form during submission
    disableForm($form, true);

    // Show spinner and disable button
    spinner.removeClass('d-none');
    submitBtn.addClass('disabled').prop('disabled', true);
    buttonText.text('Saving...');

    console.log('Making AJAX request to:', $form.attr('action'));
    console.log('Form data:', formData);

    $.ajax({
      url: $form.attr('action'),
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      timeout: 30000, // 30 second timeout
      success: function (response) {
        // Re-enable form
        disableForm($form, false);

        // Hide spinner and enable button
        spinner.addClass('d-none');
        submitBtn.removeClass('disabled').prop('disabled', false);
        buttonText.text('Save');

        if (response.success || response.message) {
          // Show success toast
          toastr.success(response.message || 'Student created successfully!', 'Success', {
            timeOut: 3000,
            closeButton: true
          });

          // Reset form after successful submission
          setTimeout(function () {
            resetStudentForm();
          }, 1000);
        } else {
          toastr.error('Something went wrong!', 'Error', {
            timeOut: 3000,
            closeButton: true
          });
        }
      },
      error: function (xhr, status, error) {
        // Re-enable form
        disableForm($form, false);

        // Hide spinner and enable button
        spinner.addClass('d-none');
        submitBtn.removeClass('disabled').prop('disabled', false);
        buttonText.text('Save');

        console.error('AJAX Error:', { xhr: xhr, status: status, error: error });

        // Handle different types of errors
        var errorMessage = '';
        var errorTitle = 'Error';

        if (status === 'timeout') {
          errorMessage = 'Request timed out. Please check your connection and try again.';
          errorTitle = 'Connection Timeout';
        } else if (status === 'abort') {
          errorMessage = 'Request was cancelled.';
          errorTitle = 'Request Cancelled';
        } else if (xhr.status === 0) {
          errorMessage = 'Network connection error. Please check your internet connection.';
          errorTitle = 'Network Error';
        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
          // Validation errors
          var errors = xhr.responseJSON.errors;
          var errorMessages = [];
          $.each(errors, function (key, value) {
            var $input = $('[name="' + key + '"]');
            var fieldLabel = $input.closest('.mb-3').find('label').text().replace('*', '').trim();
            errorMessages.push(`${fieldLabel}: ${value[0]}`);
          });
          errorMessage = errorMessages.join('<br>');
          errorTitle = 'Validation Errors';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        } else if (xhr.status === 500) {
          errorMessage = 'Internal server error. Please try again later.';
          errorTitle = 'Server Error';
        } else if (xhr.status === 422) {
          errorMessage = 'Invalid data submitted. Please check your inputs.';
          errorTitle = 'Validation Error';
        } else {
          errorMessage = 'An unexpected error occurred. Please try again.';
        }

        toastr.error(errorMessage, errorTitle, {
          timeOut: 8000,
          closeButton: true,
          escapeHtml: false
        });
      }
    });
  }

  // Function to reset the student form
  function resetStudentForm() {
    var $form = $('#createStudentForm');
    var form = $form[0];

    // Reset the form
    form.reset();

    // Clear all validation states and errors
    $form.find('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
    $form.find('.invalid-feedback, .valid-feedback').remove();
    $form.find('.personal-number-status, .email-error').remove();

    // Reset Select2 dropdowns
    $form.find('.select2').each(function () {
      $(this).val('').trigger('change');
    });

    // Clear validation styling for personal number and email
    $('#personal_number').removeClass('is-valid is-invalid');
    $('#email').removeClass('is-valid is-invalid');

    // Reset button state
    var submitBtn = $form.find('#submit-button');
    var spinner = submitBtn.find('#spinner');
    var buttonText = submitBtn.find('#button-text');

    spinner.addClass('d-none');
    submitBtn.removeClass('disabled').prop('disabled', false);
    buttonText.text('Save');

    // Remove any form overlay
    $form.find('.form-overlay').remove();

    // Re-enable form if it was disabled
    disableForm($form, false);

    // Reset validator if it exists
    if ($form.data('validator')) {
      $form.data('validator').resetForm();
    }

    console.log('Form has been reset successfully');
  }



  $('#submit-edit-button').on('click', function (e) {
    e.preventDefault();

    let $form = $('#editStudentForm');
    let url = $form.attr('action');
    let method = $form.attr('method');

    // ✅ Prepare form data BEFORE disabling inputs
    let formData = new FormData($form[0]);

    console.log("Form action URL:", url);
    console.log("Form data entries:", [...formData.entries()]);

    // Disable form inputs after FormData is created
    $form.find('input, select, textarea, button').prop('disabled', true);
    $('#spinner').removeClass('d-none');
    $('#button-text').text('Updating...');

    $.ajax({
      url: url,
      type: method,
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        console.log("Update success:", response);

        // Enable form again
        $form.find('input, select, textarea, button').prop('disabled', false);
        $('#spinner').addClass('d-none');
        $('#button-text').text('Update');

        // Show success message
        toastr.success('Student updated successfully!');
      },
      error: function (xhr) {
        console.error("Update error:", xhr.responseText);

        // Enable form again
        $form.find('input, select, textarea, button').prop('disabled', false);
        $('#spinner').addClass('d-none');
        $('#button-text').text('Update');

        // Clear old error messages
        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');

        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          let errorMessages = [];

          $.each(errors, function (field, messages) {
            let $input = $('[name="' + field + '"]');

            if ($input.length) {
              // Special handling for select2
              if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2').find('.select2-selection')
                  .addClass('is-invalid');
                $input.parent().append(
                  '<div class="invalid-feedback d-block">' + messages[0] + '</div>'
                );
              } else {
                $input.addClass('is-invalid');
                $input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
              }
              
              // Add field-specific error to toast message
              let fieldLabel = $input.closest('.mb-3').find('label').text().replace('*', '').trim();
              errorMessages.push(`${fieldLabel}: ${messages[0]}`);
            }
          });
          
          // Show field-specific errors in toast
          if (errorMessages.length > 0) {
            toastr.error(errorMessages.join('<br>'), 'Validation Errors', {
              timeOut: 8000,
              closeButton: true,
              escapeHtml: false
            });
          } else {
            toastr.error('Please fix the errors and try again.');
          }
        } else {
          toastr.error('An unexpected error occurred.');
        }
      }
    });
  });
});

let personalNumberTimeout;

// AJAX request to backend
function validatePersonalNumber(personalNumber, field, studentId = null) {
    $.ajax({
      url: window.appConfig ? window.appConfig.checkPersonalNumberUrl : '/students/check-personal-number-duplicate',
        method: "POST",
        data: {
            personal_number: personalNumber,
            student_id: studentId, // null for Add, actual ID for Edit
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function(response) {
            field.siblings(".personal-number-status").remove();

            if (response.exists) {
                field.addClass("is-invalid");
                field.after(
                    `<div class="personal-number-status unavailable">The number is already registered</div>`
                );
            } else {
                field.addClass("is-valid");
                field.after(
                    `<div class="personal-number-status available">The number is available</div>`
                );
            }
        },
        error: function(xhr) {
            console.error("Validation error:", xhr.responseText);
            field.addClass("is-invalid");
            field.after(
                `<div class="personal-number-status unavailable">Error checking number availability</div>`
            );
        },
    });
}

// Validation handler
function handlePersonalNumberValidation(e, studentId = null) {
    const personalNumber = $(this).val().trim();
    const field = $(this);

    clearTimeout(personalNumberTimeout);
    field.removeClass("is-valid is-invalid");
    field.siblings(".personal-number-status").remove();

    if (personalNumber.length === 11 && /^[0-9]{11}$/.test(personalNumber)) {
        personalNumberTimeout = setTimeout(() => {
            validatePersonalNumber(personalNumber, field, studentId);
        }, 500);
    } else if (personalNumber.length > 0) {
        field.addClass("is-invalid");
        field.after(
            '<div class="personal-number-status unavailable">Please enter exactly 11 digits</div>'
        );
    }
}

// Attach events
$(document).ready(function () {
    // Add Student form (check against all rows)
    $("#createStudentForm #personal_number").on("input blur", function (e) {
        handlePersonalNumberValidation.call(this, e, null);
    });

    // Edit Student form (exclude current row)
    const currentStudentId = $("#student_id").val();
    $("#editStudentForm #personal_number").on("input blur", function (e) {
        handlePersonalNumberValidation.call(this, e, currentStudentId);
    });
});
$(document).ready(function () {
  function checkPhoneDifference() {
      const guardian = $("#guardian_phone").val().trim();
      const personal = $("#personal_number").val().trim();

      // Remove old errors
      $("#guardian_phone, #personal_number").removeClass("is-invalid");
      $(".phone-error").remove();

      if (guardian && personal && guardian === personal) {
          $("#guardian_phone, #personal_number").addClass("is-invalid");

          $("#personal_number").after(
              '<div class="phone-error invalid-feedback">Guardian phone and personal phone cannot be the same.</div>'
          );
          return false;
      }
      return true;
  }

  // Validate on input
  $("#guardian_phone, #personal_number").on("input blur", checkPhoneDifference);

  // Prevent submit if invalid
  $("#createStudentForm, #editStudentForm").on("submit", function (e) {
      if (!checkPhoneDifference()) {
          e.preventDefault();
      }
  });
});
