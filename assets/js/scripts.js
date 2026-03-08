jQuery(document).ready(function($) {

    // Notification System
    function boardNotify(message, type = 'success') {
        if ($('#board-notifier').length === 0) {
            $('body').append('<div id="board-notifier"></div>');
        }
        var icon = type === 'success' ? '✔' : '✘';
        var notification = $('<div class="board-notification">' + icon + ' ' + message + '</div>');
        $('#board-notifier').append(notification);
        setTimeout(function() {
            notification.fadeOut(300, function() { $(this).remove(); });
        }, 4000);
    }

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
                    boardNotify(response.data.message);
                    if (response.data.redirect) {
                        setTimeout(function() { window.location.href = response.data.redirect; }, 1000);
                    } else if (action !== 'board_reset') {
                        setTimeout(function() { window.location.reload(); }, 1000);
                    } else {
                        form.find('button').prop('disabled', false).text('Send Link');
                    }
                } else {
                    boardNotify(response.data.message, 'error');
                    form.find('button').prop('disabled', false).text('Try Again');
                }
            }
        });
    });

    // Tab switching for registration/login
    $(document).on('click', '.board-auth-toggle a', function() {
        var target = $(this).data('target');
        $('.board-auth-view').hide();
        $('#' + target).fadeIn();
    });

    // Live Search: Users
    $('#user-search, #role-filter').on('keyup change', function() {
        var searchVal = $('#user-search').val().toLowerCase();
        var roleVal = $('#role-filter').val();

        $('#users-table tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            var rowRole = $(this).data('role');
            var show = rowText.indexOf(searchVal) > -1 && (!roleVal || rowRole.indexOf(roleVal) > -1);
            if (show) $(this).show();
            else $(this).hide();
        });
    });

    // Live Search: Exams
    $('#exam-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('.board-program-card').each(function() {
            var title = $(this).find('h3').text().toLowerCase();
            var code = $(this).find('code').text().toLowerCase();
            var meta = $(this).text().toLowerCase();
            if (title || code) {
                var show = meta.indexOf(val) > -1;
                if (show) $(this).fadeIn(200);
                else $(this).fadeOut(200);
            }
        });
    });

    // Live Search: Programs
    $('#program-search, #program-type-filter').on('keyup change', function() {
        var searchVal = $('#program-search').val().toLowerCase();
        var typeVal = $('#program-type-filter').val().toLowerCase();

        $('#admin-programs-grid .board-program-card, .board-programs-grid .board-program-card').each(function() {
            var text = $(this).text().toLowerCase();
            var show = text.indexOf(searchVal) > -1 && (!typeVal || text.indexOf(typeVal) > -1);
            if (show) $(this).fadeIn(200);
            else $(this).fadeOut(200);
        });
    });

    // Live Search: Certificates
    $('#cert-search, #cert-status-filter').on('keyup change', function() {
        var searchVal = $('#cert-search').val().toLowerCase();
        var statusVal = $('#cert-status-filter').val().toLowerCase();

        $('#admin-certs-grid .board-program-card').each(function() {
            var text = $(this).text().toLowerCase();
            var show = text.indexOf(searchVal) > -1 && (!statusVal || text.indexOf(statusVal) > -1);
            if (show) $(this).fadeIn(200);
            else $(this).fadeOut(200);
        });
    });

    // Live Search: Logs
    $('#log-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#audit-logs-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // Dynamic Role/Status Changes in Table
    $(document).on('change', '.quick-role-change, .quick-status-change', function() {
        var select = $(this);
        var id = select.data('id');
        var action = select.hasClass('quick-role-change') ? 'board_update_user_role' : 'board_update_user_status';
        var value = select.val();
        var data = { action: action, nonce: board_ajax.nonce, user_id: id };

        if (action === 'board_update_user_role') data.role = value;
        else data.status = value;

        $.post(board_ajax.ajax_url, data, function(response) {
            if (response.success) boardNotify(response.data.message);
            else boardNotify(response.data.message, 'error');
        });
    });

    // Dynamic Deletion/Revocation
    $(document).on('click', '.delete-user, .delete-program, .delete-cert, .revoke-cert', function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');
        var confirmMsg = 'Are you sure you want to perform this action?';
        var action = '';
        var dataKey = '';

        if (btn.hasClass('delete-user')) { action = 'board_delete_user'; dataKey = 'user_id'; }
        else if (btn.hasClass('delete-program')) { action = 'board_delete_program'; dataKey = 'program_id'; }
        else if (btn.hasClass('delete-cert')) { action = 'board_delete_certificate'; dataKey = 'cert_id'; }
        else if (btn.hasClass('revoke-cert')) { action = 'board_revoke_certificate'; dataKey = 'cert_id'; }

        if (!confirm(confirmMsg)) return;

        var postData = { action: action, nonce: board_ajax.nonce };
        postData[dataKey] = id;

        $.post(board_ajax.ajax_url, postData, function(response) {
            if (response.success) {
                boardNotify(response.data.message);
                if (action === 'board_revoke_certificate') {
                    // Update UI for revocation instead of deleting
                    btn.closest('.board-program-card').find('span').text('revoked').css({'color': 'black', 'text-decoration': 'line-through'});
                    btn.remove();
                } else {
                    btn.closest('tr, .board-program-card').fadeOut(300, function() { $(this).remove(); });
                }
            } else {
                boardNotify(response.data.message, 'error');
            }
        });
    });

    // Membership Request Multi-step
    $(document).on('click', '.next-step', function() {
        var next = $(this).data('next');
        $('.cm-step').hide();
        $('#step-' + next).fadeIn();
    });

    // Form Submissions with Notify
    $(document).on('submit', '#board-membership-form, #board-save-program-form, #board-save-exam-form, #board-generate-cert-form, #board-add-user-form, #board-general-settings-form, #board-advanced-settings-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var formData = new FormData(this);
        var action = '';

        // Map form IDs to actions
        var actions = {
            'board-membership-form': 'board_membership_request',
            'board-save-program-form': 'board_save_program',
            'board-save-exam-form': 'board_save_exam',
            'board-generate-cert-form': 'board_generate_certificate',
            'board-add-user-form': 'board_add_user',
            'board-general-settings-form': 'board_save_general_settings',
            'board-advanced-settings-form': 'board_save_advanced_settings'
        };

        action = actions[form.attr('id')];
        formData.append('action', action);
        formData.append('nonce', board_ajax.nonce);

        btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: board_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                btn.prop('disabled', false).text('Success');
                if (response.success) {
                    boardNotify(response.data.message);
                    if (form.attr('id') === 'board-membership-form') {
                        $('#cm-request-steps').hide();
                        $('#cm-request-success').fadeIn();
                    } else if (action.indexOf('save') === -1 && action.indexOf('settings') === -1) {
                         setTimeout(function() { window.location.reload(); }, 1000);
                    }
                } else {
                    boardNotify(response.data.message, 'error');
                    btn.text('Try Again');
                }
            }
        });
    });

    // Export Handlers
    $('#board-full-backup-json').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).text('Generating...');
        $.post(board_ajax.ajax_url, { action: 'board_export_json', nonce: board_ajax.nonce }, function(response) {
            btn.prop('disabled', false).text('Full Backup (JSON)');
            if (response.success) {
                var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(response.data));
                var downloadAnchorNode = document.createElement('a');
                downloadAnchorNode.setAttribute("href", dataStr);
                downloadAnchorNode.setAttribute("download", "gshb_backup.json");
                document.body.appendChild(downloadAnchorNode);
                downloadAnchorNode.click();
                downloadAnchorNode.remove();
                boardNotify('Backup generated successfully.');
            }
        });
    });

    // Search: Directory
    $('#directory-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#directory-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // Search: Verification Mgmt
    $('#verify-mgmt-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#verify-mgmt-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
        });
    });

    // Exam Submission Handler (Public)
    $(document).on('click', '.start-exam', function() {
        var btn = $(this);
        var examId = btn.data('id');
        if (confirm('Do you want to submit this exam with a random score for demo?')) {
            var score = Math.floor(Math.random() * 40) + 60; // 60-100
            btn.prop('disabled', true).text('Submitting...');
            $.post(board_ajax.ajax_url, {
                action: 'board_submit_exam',
                nonce: board_ajax.nonce,
                exam_id: examId,
                score: score
            }, function(response) {
                if (response.success) {
                    boardNotify(response.data.message);
                    setTimeout(function() { window.location.href = board_ajax.mb_url || '/mb'; }, 1500);
                } else {
                    boardNotify(response.data.message, 'error');
                    btn.prop('disabled', false).text('Start Exam');
                }
            });
        }
    });

    // Approval Request Handler
    $('.approve-request').on('click', function() {
        var btn = $(this);
        var requestId = btn.data('id');
        if (!confirm('Are you sure you want to approve this request?')) return;
        btn.prop('disabled', true).text('...');
        $.post(board_ajax.ajax_url, { action: 'board_approve_request', nonce: board_ajax.nonce, request_id: requestId }, function(response) {
            if (response.success) {
                boardNotify(response.data.message);
                btn.closest('tr').fadeOut(300, function() { $(this).remove(); });
            } else {
                boardNotify(response.data.message, 'error');
                btn.prop('disabled', false).text('Approve');
            }
        });
    });

    // Advanced Table Sorting
    $(document).on('click', '.board-table th', function() {
        var table = $(this).parents('table').eq(0);
        var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
        this.asc = !this.asc;
        if (!this.asc) { rows = rows.reverse(); }
        for (var i = 0; i < rows.length; i++) { table.append(rows[i]); }

        // Visual indicator
        table.find('th').removeClass('sorted-asc sorted-desc');
        $(this).addClass(this.asc ? 'sorted-asc' : 'sorted-desc');
    });

    function comparer(index) {
        return function(a, b) {
            var valA = getCellValue(a, index), valB = getCellValue(b, index);
            return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
        };
    }

    function getCellValue(row, index) {
        return $(row).children('td').eq(index).text();
    }

    // Copy Serial to Clipboard
    $(document).on('click', '#copy-serial', function() {
        var serial = $(this).data('serial');
        var btn = $(this);
        navigator.clipboard.writeText(serial).then(function() {
            var originalHtml = btn.html();
            btn.html('✔ Copied!');
            boardNotify('Serial number copied to clipboard.');
            setTimeout(function() { btn.html(originalHtml); }, 2000);
        });
    });

    // Verification Portal Handler
    $('#board-verify-form').on('submit', function(e) {
        e.preventDefault();
        var code = $('#verify_code').val();
        var btn = $(this).find('button');
        btn.prop('disabled', true).text('Verifying...');

        $.post(board_ajax.ajax_url, {
            action: 'board_verify_document',
            nonce: board_ajax.nonce,
            verify_code: code
        }, function(response) {
            btn.prop('disabled', false).text('Verify Document');
            $('#verify-result').fadeIn();
            if (response.success && response.data.valid) {
                var status = response.data.is_active ? '<span style="font-weight: bold; border-bottom: 2px solid black;">✔ Valid</span>' : '<span style="color: #666; font-weight: bold;">✘ Expired / Invalid</span>';
                var html = '<p><strong>Status:</strong> ' + status + '</p>' +
                           '<p><strong>Holder:</strong> ' + response.data.name + '</p>' +
                           '<p><strong>Type:</strong> ' + response.data.type + '</p>' +
                           '<p><strong>Specialty:</strong> ' + response.data.specialty + '</p>' +
                           '<p><strong>Expires:</strong> ' + response.data.expiry + '</p>';
                if (response.data.url) html += '<a href="' + response.data.url + '" class="board-btn-black board-btn-small" style="margin-top:10px;">View Digital Certificate</a>';
                $('#verify-content').html(html);
                boardNotify('Verification successful.');
            } else {
                $('#verify-content').html('<p style="font-weight: bold; border-bottom: 1px solid black; display: inline-block; padding-bottom: 5px; margin-bottom: 15px;">✘ ' + (response.data.message || 'Invalid or Expired Code') + '</p><p>Please check the code and try again.</p>');
                boardNotify('Invalid code provided.', 'error');
            }
        });
    });

});
