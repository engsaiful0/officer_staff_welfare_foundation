$(document).ready(function () {

    const form = $('#memberForm');

    form.on('submit', function (e) {

        e.preventDefault();

        let formData = new FormData(this);
        let url = form.attr('action');

        let submitBtn = form.find('button[type="submit"]');
        let originalText = submitBtn.html();

        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Updating...');

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function (response) {

                if (response.success) {

                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    }

                    setTimeout(function () {
                        window.location.href = response.redirect_url;
                    }, 800);

                } else {

                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || "Update failed.");
                    }

                }
            },

            error: function (xhr) {

                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);

                if (xhr.status === 422) {

                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function (key, value) {

                        let input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');

                        if (input.next('.invalid-feedback').length === 0) {
                            input.after('<div class="invalid-feedback">' + value[0] + '</div>');
                        } else {
                            input.next('.invalid-feedback').text(value[0]);
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.error(value[0]);
                        }

                    });

                } else {

                    if (typeof toastr !== 'undefined') {
                        toastr.error("Something went wrong.");
                    }

                    console.log(xhr.responseText);
                }

            },

            complete: function () {

                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);

            }

        });

    });


    /* Clear validation when typing */

    $('input,select,textarea').on('keyup change', function () {

        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();

    });

});