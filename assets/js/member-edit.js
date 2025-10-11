$(document).ready(function () {
    // Form validation and submission for EDIT member
    $('#memberEditForm').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);

      

        var formData = new FormData(this);
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val() || 'POST';

        console.log('Edit Member Form submission:', { url: url, method: method });
        console.log('Form data:', Object.fromEntries(formData.entries()));
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));

        // Show loading state with spinner
        var submitBtn = form.find('button[type="submit"]');
        var originalText = MemberFormUtils.showLoadingState(form, submitBtn, 'Updating...');

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
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Update form with any server-returned data
                    if (response.member) {
                        // Update any fields that might have been modified by the server
                        console.log('Member updated:', response.member);
                    }
                    
                    // Show success message with option to redirect
                    setTimeout(function() {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Member updated successfully!',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Go to Members List',
                            cancelButtonText: 'Stay Here',
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = window.membersListUrl || '/app/members/view-member';
                            }
                        });
                    }, 500);
                } else {
                    toastr.error(response.message || 'Update failed');
                }
            },
            error: function (xhr) {
                MemberFormUtils.handleAjaxError(xhr);
            },
            complete: function () {
                MemberFormUtils.hideLoadingState(form, submitBtn, originalText);
            }
        });
    });

    // Setup real-time validation (with member ID for edit)
    MemberFormUtils.setupRealTimeValidation(window.memberId);
    
    // Setup image preview
    MemberFormUtils.setupImagePreview();

    // Form reset on cancel (for edit form)
    $('a[href*="members"]').on('click', function () {
        if ($(this).text().trim() === 'Cancel') {
            // For edit forms, we might want to reload the page to get fresh data
            if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
                window.location.reload();
            }
        }
    });

    // Auto-save functionality (optional - saves draft every 30 seconds)
    var autoSaveTimer;
    function startAutoSave() {
        autoSaveTimer = setInterval(function() {
            // Only auto-save if form has been modified
            if ($('#memberForm').data('modified')) {
                console.log('Auto-saving draft...');
                // You could implement auto-save functionality here
                // For now, just log that auto-save would happen
            }
        }, 30000); // 30 seconds
    }

    // Mark form as modified when any field changes
    $('#memberForm input, #memberForm select, #memberForm textarea').on('change', function() {
        $('#memberForm').data('modified', true);
        
        // Clear any existing validation errors when user starts typing
        var field = $(this);
        field.removeClass('is-invalid');
        field.siblings('.invalid-feedback').text('');
    });

    // Start auto-save when page loads
    startAutoSave();

    // Clear auto-save timer when form is submitted
    $('#memberForm').on('submit', function() {
        clearInterval(autoSaveTimer);
    });

    // Warn user before leaving if form has unsaved changes
    $(window).on('beforeunload', function() {
        if ($('#memberForm').data('modified')) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    // Clear modified flag on successful save
    $('#memberForm').on('ajax:success', function() {
        $('#memberForm').data('modified', false);
    });
});
