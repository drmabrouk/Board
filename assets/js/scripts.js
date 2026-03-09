jQuery(document).ready(function($) {

    // Custom Modal System
    function boardConfirm(title, message, onConfirm) {
        var modalHtml = '<div class="board-modal-overlay">' +
            '<div class="board-modal">' +
            '<h3>' + title + '</h3>' +
            '<p>' + message + '</p>' +
            '<div style="display: flex; gap: 10px;">' +
            '<button class="board-btn-black" id="modal-confirm">Confirm</button>' +
            '<button class="board-btn-black board-btn-outline" id="modal-cancel">Cancel</button>' +
            '</div>' +
            '</div>' +
            '</div>';
        $('body').append(modalHtml);
        $('#modal-confirm').on('click', function() {
            $('.board-modal-overlay').remove();
            onConfirm();
        });
        $('#modal-cancel, .board-modal-overlay').on('click', function(e) {
            if (e.target !== this) return;
            $('.board-modal-overlay').remove();
        });
    }

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
    $(document).on('submit', '#board-auth-form, #board-auth-form-reg, #board-auth-form-reset, #board-auth-form-otp, #board-auth-form-new-pass', function(e) {
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

                    if (action === 'board_reset') {
                        $('#board-auth-form-reset').hide();
                        $('#board-auth-form-otp').fadeIn().find('input[name="username"]').val(response.data.username);
                    } else if (action === 'board_verify_otp') {
                        $('#board-auth-form-otp').hide();
                        $('#board-auth-form-new-pass').fadeIn();
                        $('#board-auth-form-new-pass input[name="username"]').val(form.find('input[name="username"]').val());
                        $('#board-auth-form-new-pass input[name="otp"]').val(form.find('input[name="otp"]').val());
                    } else if (response.data.redirect) {
                        setTimeout(function() { window.location.href = response.data.redirect; }, 1000);
                    } else {
                        setTimeout(function() { window.location.reload(); }, 1000);
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
    $('#user-search, #role-filter, #status-filter').on('keyup change', function() {
        var searchVal = $('#user-search').val().toLowerCase();
        var roleVal = $('#role-filter').val();
        var statusVal = $('#status-filter').val();

        $('#users-table tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            var rowRole = $(this).data('role');
            var rowStatus = $(this).data('status');

            var showSearch = rowText.indexOf(searchVal) > -1;
            var showRole = !roleVal || rowRole.indexOf(roleVal) > -1;
            var showStatus = !statusVal || rowStatus === statusVal;

            if (showSearch && showRole && showStatus) $(this).show();
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

    // Predictive Search Suggestions
    $(document).on('keyup', '#user-search, #program-search, #exam-search, #cert-search', function() {
        var input = $(this);
        var val = input.val().toLowerCase();
        var target = $('#' + input.attr('id') + '-suggestions');

        if (val.length < 2) { target.hide(); return; }

        var suggestions = [];
        if (input.attr('id') === 'user-search') {
            $('#users-table tbody tr:visible').each(function() { suggestions.push($(this).find('strong').text()); });
        } else {
            $('.board-program-card:visible').each(function() { suggestions.push($(this).find('h3, h4').text()); });
        }

        suggestions = [...new Set(suggestions)].filter(s => s.toLowerCase().includes(val)).slice(0, 5);

        if (suggestions.length) {
            target.html(suggestions.map(s => '<div class="suggestion-item">' + s + '</div>').join('')).show();
        } else {
            target.hide();
        }
    });

    $(document).on('click', '.suggestion-item', function() {
        var val = $(this).text();
        var input = $(this).parent().siblings('input');
        input.val(val).trigger('keyup');
        $(this).parent().hide();
    });

    // Expandable Panels
    $(document).on('click', '.board-expand-toggle', function() {
        $(this).closest('.board-program-card').toggleClass('panel-collapsed');
    });

    // Live Search: Programs
    $('#program-search, #program-type-filter, #program-category-filter').on('keyup change', function() {
        var searchVal = $('#program-search').val().toLowerCase();
        var typeVal = $('#program-type-filter').val().toLowerCase();
        var catVal = $('#program-category-filter').val().toLowerCase();

        $('#admin-programs-grid .board-program-card, .board-programs-grid .board-program-card').each(function() {
            var text = $(this).text().toLowerCase();
            var pType = $(this).data('type') || '';
            var pCat = $(this).data('category') || '';

            var showSearch = text.indexOf(searchVal) > -1;
            var showType = !typeVal || pType.indexOf(typeVal) > -1;
            var showCat = !catVal || pCat.indexOf(catVal) > -1;

            if (showSearch && showType && showCat) $(this).fadeIn(200);
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

    // Global Header Search (Structural)
    $('#global-header-search').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        var results = $('#global-search-results');
        if (val.length < 2) { results.hide(); return; }

        var sections = [
            { name: 'Dashboard', url: '?cp_tab=dashboard' },
            { name: 'Users Management', url: '?cp_tab=users' },
            { name: 'Programs', url: '?cp_tab=programs' },
            { name: 'Exams', url: '?cp_tab=exams' },
            { name: 'Membership Requests', url: '?cp_tab=requests' },
            { name: 'Certificates & Accreditations', url: '?cp_tab=certificates' },
            { name: 'Verification', url: '?cp_tab=verification' },
            { name: 'Reports', url: '?cp_tab=reports' },
            { name: 'Settings', url: '?cp_tab=settings' },
            { name: 'System Settings - General', url: '?cp_tab=settings&set_tab=general' },
            { name: 'System Settings - Design', url: '?cp_tab=settings&set_tab=design' },
            { name: 'System Settings - Activity Logs', url: '?cp_tab=settings&set_tab=logs' },
            { name: 'System Settings - Backup', url: '?cp_tab=settings&set_tab=backup' }
        ];

        var filtered = sections.filter(s => s.name.toLowerCase().includes(val));

        if (filtered.length) {
            results.html(filtered.map(s => '<div class="suggestion-item" data-url="' + s.url + '">' + s.name + '</div>').join('')).show();
        } else {
            results.hide();
        }
    });

    $(document).on('click', '#global-search-results .suggestion-item', function() {
        window.location.href = $(this).data('url');
    });

    // Dynamic User Lookup logic for forms
    $(document).on('keyup', '.board-user-lookup-input', function() {
        var input = $(this);
        var val = input.val();
        var target = input.siblings('.board-user-lookup-results');
        var hiddenInput = input.siblings('input[type="hidden"]');

        if (val.length < 2) {
            target.hide();
            hiddenInput.val('');
            return;
        }

        $.post(board_ajax.ajax_url, {
            action: 'board_user_lookup',
            nonce: board_ajax.nonce,
            term: val
        }, function(response) {
            if (response.success && response.data.length > 0) {
                target.html(response.data.map(u => '<div class="suggestion-item" data-id="' + u.id + '">' + u.text + '</div>').join('')).show();
            } else {
                target.hide();
            }
        });
    });

    $(document).on('click', '.board-user-lookup-results .suggestion-item', function() {
        var item = $(this);
        var input = item.parent().siblings('.board-user-lookup-input');
        var hiddenInput = item.parent().siblings('input[type="hidden"]');

        input.val(item.text());
        hiddenInput.val(item.data('id'));
        item.parent().hide();
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

    $(document).on('change', '.app-status-change', function() {
        var select = $(this);
        var id = select.data('id');
        var status = select.val();

        $.post(board_ajax.ajax_url, {
            action: 'board_update_application_status',
            nonce: board_ajax.nonce,
            app_id: id,
            status: status
        }, function(response) {
            if (response.success) {
                boardNotify(response.data.message);
                setTimeout(function() { window.location.reload(); }, 1000);
            }
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

        boardConfirm('Confirm Action', confirmMsg, function() {
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
    });

    // Membership Request Multi-step
    $(document).on('click', '.next-step', function() {
        var next = $(this).data('next');
        $('.cm-step').hide();
        $('#step-' + next).fadeIn();
    });

    // Form Submissions with Notify
    $(document).on('submit', '#board-membership-form, #board-save-program-form, #board-save-exam-form, #board-generate-cert-form, #board-add-user-form, #board-general-settings-form, #board-advanced-settings-form, #board-email-settings-form', function(e) {
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
            'board-advanced-settings-form': 'board_save_advanced_settings',
            'board-email-settings-form': 'board_save_email_settings'
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
                    } else {
                         // Force reload for programs and other management sections to show new data
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
        boardConfirm('Approve Membership', 'Are you sure you want to approve this certified membership request?', function() {
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
    $(document).on('click', '#save-all-email-templates', function() {
        var btn = $(this);
        var data = { action: 'board_save_email_templates', nonce: board_ajax.nonce };
        $('[name^="template_"]').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });
        btn.prop('disabled', true).text('Updating...');
        $.post(board_ajax.ajax_url, data, function(response) {
            btn.prop('disabled', false).text('Update All Templates');
            if (response.success) boardNotify(response.data.message);
        });
    });

    $(document).on('click', '.view-app-data', function() {
        var data = $(this).data('data');
        alert("Application Form Data:\n\n" + data.replace(/&/g, "\n").replace(/=/g, ": "));
    });

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
                var statusClass = response.data.is_active ? 'status-active' : 'status-expired';
                var statusText = response.data.is_active ? '✔ AUTHENTICATED' : '✘ EXPIRED / INVALID';

                var html = '<div style="display: grid; grid-template-columns: 1fr; gap: 20px;">' +
                           '<div style="text-align:center; margin-bottom:20px;"><span class="status-badge ' + statusClass + '" style="font-size:16px; padding:10px 30px;">' + statusText + '</span></div>' +
                           '<div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding-bottom:10px;"><strong>Holder Name:</strong> <span>' + response.data.name + '</span></div>' +
                           '<div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding-bottom:10px;"><strong>Credential Type:</strong> <span>' + response.data.type + '</span></div>' +
                           '<div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding-bottom:10px;"><strong>Specialization:</strong> <span>' + response.data.specialty + '</span></div>' +
                           '<div style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; padding-bottom:10px;"><strong>Valid Until:</strong> <span>' + response.data.expiry + '</span></div>';

                if (response.data.url) {
                    html += '<div style="text-align:center; margin-top:30px;"><a href="' + response.data.url + '" class="board-btn-black" style="width:auto; padding:15px 40px;">View Digital Credential</a></div>';
                }
                html += '</div>';

                $('#verify-content').html(html);
                boardNotify('Credential verified successfully.');
            } else {
                $('#verify-content').html('<div style="text-align:center; padding:30px;"><span class="status-badge status-revoked" style="font-size:16px; padding:10px 30px; margin-bottom:20px;">' + (response.data.message || 'INVALID CREDENTIAL') + '</span><p style="margin-top:20px;">The verification code entered does not match our records or has been permanently revoked.</p></div>');
                boardNotify('Verification failed.', 'error');
            }
        });
    });

});
