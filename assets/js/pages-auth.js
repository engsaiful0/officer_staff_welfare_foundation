/**
 * Pages Authentication - Login with AJAX (jQuery version)
 */
'use strict';

$(document).ready(function () {
  const $form = $('#formAuthentication');
  const $loginBtn = $('#login-btn');
  const $spinner = $loginBtn.find('.spinner-border');
  const $errorDiv = $('#auth-error');

  // Handle login click
  $loginBtn.on('click', function (e) {
    console.log('Login button clicked');
    e.preventDefault();

    const emailUsername = $.trim($form.find('[name="email-username"]').val());
    const password = $.trim($form.find('[name="password"]').val());

    // Simple client-side validation
    if (emailUsername === '' || password === '') {
      $errorDiv.text('Please fill in all fields').removeClass('d-none');
      return;
    }

    // Show spinner & disable button
    $spinner.removeClass('d-none');
    $loginBtn.prop('disabled', true);
    $errorDiv.addClass('d-none');

    // Send AJAX
    $.ajax({
      url: $form.attr('action'),
      type: 'POST',
      data: $form.serialize(),
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': $('input[name="_token"]').val()
      },
      success: function (data) {
        $spinner.addClass('d-none');
        $loginBtn.prop('disabled', false);

        if (data.status === 'success') {
          window.location.href = data.redirect_url ?? '/';
        } else if (data.message) {
          $errorDiv.text(data.message).removeClass('d-none');
        } else {
          $errorDiv.text('Invalid credentials').removeClass('d-none');
        }
      },
      error: function (xhr) {
        $spinner.addClass('d-none');
        $loginBtn.prop('disabled', false);

        let msg = 'An error occurred. Please try again.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        $errorDiv.text(msg).removeClass('d-none');
      }
    });
  });

  // Password visibility toggle 👁️
  $(document).on('click', '.form-password-toggle .input-group-text', function () {
    const $input = $(this).closest('.input-group').find('input');
    const $icon = $(this).find('i');

    if ($input.attr('type') === 'password') {
      $input.attr('type', 'text');
      $icon.removeClass('ti-eye-off').addClass('ti-eye');
    } else {
      $input.attr('type', 'password');
      $icon.removeClass('ti-eye').addClass('ti-eye-off');
    }
  });
});
