jQuery(document).ready(function($) {

    // Toggle Password Visibility
    $(document).on('click', '.toggle-password', function() {
        var input = $(this).siblings('input');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            $(this).text('Hide');
        } else {
            input.attr('type', 'password');
            $(this).text('Show');
        }
    });

    // Simple AJAX Auth Handlers
    $(document).on('submit', '#board-auth-form, #board-auth-form-reg, #board-auth-form-reset', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        var action = form.data('action');

        $.ajax({
            url: board_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=' + action + '&nonce=' + board_ajax.nonce,
            beforeSend: function() {
                form.find('button').prop('disabled', true).text('Processing...');
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    if (action !== 'board_reset') {
                        window.location.reload();
                    } else {
                        form.find('button').prop('disabled', false).text('Send Link');
                    }
                } else {
                    alert(response.data.message);
                    form.find('button').prop('disabled', false).text('Try Again');
                }
            }
        });
    });

    // Tab switching for registration/login
    $(document).on('click', '.board-auth-toggle a', function() {
        var target = $(this).data('target');
        $('.board-auth-view').hide();
        $('#' + target).show();
    });

});
