$(document).ready(function () {
    // Form validation and submission
    $('#memberForm').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);

        // Clear previous validation errors
        form.find('.form-control').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        var formData = new FormData(this);
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val() || 'POST';

        console.log('Form submission:', { url: url, method: method });

        // Show loading state with spinner
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();

        // Disable the whole form (all inputs, selects, textareas, buttons)
        form.find('input, select, textarea, button').prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                toastr.success(response.message);

                // Clear the form after success
                form[0].reset();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    // Validation errors
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        var input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]);
                    });
                } else {
                    Swal.fire('Error', 'An error occurred while saving the member', 'error');
                }
            },
            complete: function () {
                // Re-enable the whole form
                form.find('input, select, textarea, button').prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });


    // Real-time validation for email
    $('#email').on('blur', function () {
        var email = $(this).val();
        if (email) {
            checkEmailUnique(email);
        }
    });

    // Real-time validation for mobile
    $('#mobile').on('blur', function () {
        var mobile = $(this).val();
        if (mobile) {
            checkMobileUnique(mobile);
        }
    });

    // Real-time validation for NID
    $('#nid_number').on('blur', function () {
        var nid = $(this).val();
        if (nid) {
            checkNidUnique(nid);
        }
    });

    // Check email uniqueness
    function checkEmailUnique(email) {
        var data = { email: email };
        if (window.memberId) {
            data.id = window.memberId;
        }

        $.ajax({
            url: window.checkEmailUrl,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var input = $('#email');
                if (response.unique) {
                    input.removeClass('is-invalid').addClass('is-valid');
                    input.siblings('.invalid-feedback').text('');
                } else {
                    input.removeClass('is-valid').addClass('is-invalid');
                    input.siblings('.invalid-feedback').text('This email is already taken.');
                }
            }
        });
    }

    // Check mobile uniqueness
    function checkMobileUnique(mobile) {
        var data = { mobile: mobile };
        if (window.memberId) {
            data.id = window.memberId;
        }

        $.ajax({
            url: window.checkMobileUrl,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var input = $('#mobile');
                if (response.unique) {
                    input.removeClass('is-invalid').addClass('is-valid');
                    input.siblings('.invalid-feedback').text('');
                } else {
                    input.removeClass('is-valid').addClass('is-invalid');
                    input.siblings('.invalid-feedback').text('This mobile number is already registered.');
                }
            }
        });
    }

    // Check NID uniqueness
    function checkNidUnique(nid) {
        var data = { nid_number: nid };
        if (window.memberId) {
            data.id = window.memberId;
        }

        $.ajax({
            url: window.checkNidUrl,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                var input = $('#nid_number');
                if (response.unique) {
                    input.removeClass('is-invalid').addClass('is-valid');
                    input.siblings('.invalid-feedback').text('');
                } else {
                    input.removeClass('is-valid').addClass('is-invalid');
                    input.siblings('.invalid-feedback').text('This NID number is already registered.');
                }
            }
        });
    }

    // Image preview
    $('#picture').on('change', function () {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = $('#picture').siblings('.image-preview');
                if (preview.length === 0) {
                    $('#picture').after('<div class="image-preview mt-2"><img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;"></div>');
                } else {
                    preview.html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px;">');
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Form reset on cancel
    $('a[href*="members"]').on('click', function () {
        if ($(this).text().trim() === 'Cancel') {
            $('#memberForm')[0].reset();
            $('.form-control').removeClass('is-invalid is-valid');
            $('.invalid-feedback').text('');
        }
    });
});
