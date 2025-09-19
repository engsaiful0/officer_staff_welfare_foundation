'use strict';

$(function() {
    // ajax setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Validation rules for both create and edit forms
    const validationRules = {
        full_name_in_banglai: {
            required: true
        },
        full_name_in_english_block_letter: {
            required: true
        },
        father_name_in_banglai: {
            required: true
        },
        father_name_in_english_block_letter: {
            required: true
        },
        mother_name_in_banglai: {
            required: true
        },
        mother_name_in_english_block_letter: {
            required: true
        },
        personal_number: {
            required: true,
            digits: true,
            minlength: 11,
            maxlength: 11,
            remote: {
                url: '/students/check-personal-number-duplicate',
                type: 'post',
                data: {
                    personal_number: function() {
                        return $('#personal_number').val();
                    },
                    student_id: function() {
                        return $('#student_id').val();
                    }
                },
                dataType: 'json',
                dataFilter: function(response) {
                    let res = JSON.parse(response);
                    if (res.exists) {
                        return JSON.stringify(res.message);
                    }
                    return 'true';
                }
            }
        },
        email: {
            required: true,
            email: true,
            remote: {
                url: '/students/check-email-duplicate',
                type: 'post',
                data: {
                    email: function() {
                        return $('#email').val();
                    },
                    student_id: function() {
                        return $('#student_id').val();
                    }
                },
                dataType: 'json',
                dataFilter: function(response) {
                    let res = JSON.parse(response);
                    if (res.exists) {
                        return JSON.stringify(res.message);
                    }
                    return 'true';
                }
            }
        },
        guardian_phone: {
            required: true
        },
        present_address: {
            required: true
        },
        permanent_address: {
            required: true
        },
        date_of_birth: {
            required: true,
            date: true
        },
        ssc_or_equivalent_institute_name: {
            required: true
        },
        ssc_or_equivalent_roll_number: {
            required: true
        },
        ssc_or_equivalent_registration_number: {
            required: true
        },
        ssc_or_equivalent_passing_year_id: {
            required: true
        },
        ssc_or_equivalent_session_id: {
            required: true
        },
        ssc_or_equivalent_gpa: {
            required: true,
            number: true
        },
        nationality_id: {
            required: true
        },
        religion_id: {
            required: true
        },
        board_id: {
            required: true
        },
        technology_id: {
            required: true
        },
        shift_id: {
            required: true
        },
        academic_year_id: {
            required: true
        },
        semester_id: {
            required: true
        },
        gender: {
            required: true
        }
    };

    // Messages for validation
    const validationMessages = {
        personal_number: {
            remote: 'This personal number is already registered.'
        },
        email: {
            remote: 'This email is already registered.'
        }
    };

    // Function to handle form submission
    function handleFormSubmit(form, event) {
        event.preventDefault();
        var $form = $(form);
        if (!$form.valid()) return false;

        var submitBtn = $form.find('button[type="submit"]');
        var spinner = submitBtn.find('.spinner-border');
        var buttonText = submitBtn.find('#button-text');

        spinner.removeClass('d-none');
        buttonText.text('Submitting...');
        submitBtn.prop('disabled', true);
        $form.find('input, select, textarea').prop('disabled', true);


        var formData = new FormData(form);
        if ($form.attr('id') === 'editStudentForm') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 3000,
                    closeButton: true
                });
                setTimeout(function() {
                    window.location.href = '/app/students/view-student';
                }, 1000);
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessages = '';
                $.each(errors, function(key, value) {
                    errorMessages += value[0] + '<br>';
                });
                toastr.error(errorMessages, 'Error', {
                    timeOut: 5000,
                    closeButton: true,
                    escapeHtml: false
                });
            },
            complete: function() {
                spinner.addClass('d-none');
                buttonText.text($form.attr('id') === 'createStudentForm' ? 'Create' : 'Update');
                submitBtn.prop('disabled', false);
                $form.find('input, select, textarea').prop('disabled', false);

            }
        });
    }

    // Apply validation to create form
    if ($('#createStudentForm').length) {
        const createRules = { ...validationRules
        };
        createRules.picture = {
            required: true,
            extension: "jpeg|png|jpg|gif|svg"
        };

        $('#createStudentForm').validate({
            rules: createRules,
            messages: validationMessages,
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function(form, event) {
                handleFormSubmit(form, event);
            }
        });
    }

    // Apply validation to edit form
    if ($('#editStudentForm').length) {
        const editRules = { ...validationRules
        };
        editRules.picture = {
            extension: "jpeg|png|jpg|gif|svg"
        }; // Not required on edit

        $('#editStudentForm').validate({
            rules: editRules,
            messages: validationMessages,
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function(form, event) {
                handleFormSubmit(form, event);
            }
        });
    }
});
