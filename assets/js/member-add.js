$(document).ready(function () {
    // Form validation and submission for ADD member
    $('#memberForm').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);

        // Clear previous validation errors
        MemberFormUtils.clearValidationErrors(form);

        // Client-side validation for required fields
        if (!MemberFormUtils.validateRequiredFields(form)) {
            toastr.error('Please fill in all required fields.');
            return;
        }

        var formData = new FormData(this);
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val() || 'POST';

        console.log('Add Member Form submission:', { url: url, method: method });
        console.log('Form data:', Object.fromEntries(formData.entries()));
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));

        // Show loading state with spinner
        var submitBtn = form.find('button[type="submit"]');
        var originalText = MemberFormUtils.showLoadingState(form, submitBtn, 'Creating...');

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

                // Clear the form after successful creation
                form[0].reset();
                
                // Redirect to members list or show success message
                setTimeout(function() {
                    window.location.href = window.membersListUrl || '/app/members/view-member';
                }, 1500);
            },
            error: function (xhr) {
                MemberFormUtils.handleAjaxError(xhr);
            },
            complete: function () {
                MemberFormUtils.hideLoadingState(form, submitBtn, originalText);
            }
        });
    });

    // Setup real-time validation (no member ID for add form)
    MemberFormUtils.setupRealTimeValidation(null);
    
    // Setup image preview
    MemberFormUtils.setupImagePreview();

    // Form reset on cancel
    $('a[href*="members"]').on('click', function () {
        if ($(this).text().trim() === 'Cancel') {
            $('#memberForm')[0].reset();
            $('.form-control').removeClass('is-invalid is-valid');
            $('.invalid-feedback').text('');
        }
    });
});
