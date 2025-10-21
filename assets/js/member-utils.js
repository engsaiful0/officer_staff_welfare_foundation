/**
 * Member Form Utilities
 * Shared functions for member add and edit forms
 */

// Common validation functions
window.MemberFormUtils = {
    
    /**
     * Validate required fields
     */
    validateRequiredFields: function(form) {
        var isValid = true;
        form.find('input[required], select[required], textarea[required]').each(function() {
            var field = $(this);
            var value = field.val();
            
            if (!value || value.trim() === '') {
                field.addClass('is-invalid');
                field.siblings('.invalid-feedback').text('This field is required.');
                isValid = false;
            }
        });
        
        return isValid;
    },

    /**
     * Clear all validation errors
     */
    clearValidationErrors: function(form) {
        form.find('.form-control').removeClass('is-invalid is-valid');
        form.find('.invalid-feedback').text('');
    },

    /**
     * Show loading state
     */
    showLoadingState: function(form, submitBtn, loadingText) {
        var originalText = submitBtn.html();
        form.find('input, select, textarea, button').prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText);
        return originalText;
    },

    /**
     * Hide loading state
     */
    hideLoadingState: function(form, submitBtn, originalText) {
        form.find('input, select, textarea, button').prop('disabled', false);
        submitBtn.html(originalText);
    },

    /**
     * Handle AJAX errors
     */
    handleAjaxError: function(xhr) {
        if (xhr.status === 422) {
            // Validation errors
            var errors = xhr.responseJSON.errors;
            $.each(errors, function (field, messages) {
                var input = $('#' + field);
                input.addClass('is-invalid');
                input.siblings('.invalid-feedback').text(messages[0]);
            });
            
            toastr.error('Please fix the validation errors and try again.');
        } else if (xhr.status === 419) {
            toastr.error('Session expired. Please refresh the page and try again.');
        } else if (xhr.status === 500) {
            var errorMessage = 'Server error occurred. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
                title: 'Server Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            var errorMessage = 'An error occurred while processing the request';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    },

    /**
     * Check email uniqueness
     */
    checkEmailUnique: function(email, memberId) {
        var data = { email: email };
        if (memberId) {
            data.id = memberId;
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
            },
            error: function(xhr) {
                console.error('Email uniqueness check failed:', xhr);
                var input = $('#email');
                input.removeClass('is-valid is-invalid');
                input.siblings('.invalid-feedback').text('');
            }
        });
    },

    /**
     * Check mobile uniqueness
     */
    checkMobileUnique: function(mobile, memberId) {
        var data = { mobile: mobile };
        if (memberId) {
            data.id = memberId;
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
            },
            error: function(xhr) {
                console.error('Mobile uniqueness check failed:', xhr);
                var input = $('#mobile');
                input.removeClass('is-valid is-invalid');
                input.siblings('.invalid-feedback').text('');
            }
        });
    },

    /**
     * Check NID uniqueness
     */
    checkNidUnique: function(nid, memberId) {
        var data = { nid_number: nid };
        if (memberId) {
            data.id = memberId;
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
            },
            error: function(xhr) {
                console.error('NID uniqueness check failed:', xhr);
                var input = $('#nid_number');
                input.removeClass('is-valid is-invalid');
                input.siblings('.invalid-feedback').text('');
            }
        });
    },

    /**
     * Setup image preview
     */
    setupImagePreview: function() {
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
    },

    /**
     * Setup real-time validation
     */
    setupRealTimeValidation: function(memberId) {
        $('#email').on('blur', function () {
            var email = $(this).val();
            if (email) {
                MemberFormUtils.checkEmailUnique(email, memberId);
            }
        });

        $('#mobile').on('blur', function () {
            var mobile = $(this).val();
            if (mobile) {
                MemberFormUtils.checkMobileUnique(mobile, memberId);
            }
        });

        $('#nid_number').on('blur', function () {
            var nid = $(this).val();
            if (nid) {
                MemberFormUtils.checkNidUnique(nid, memberId);
            }
        });
    }
};
