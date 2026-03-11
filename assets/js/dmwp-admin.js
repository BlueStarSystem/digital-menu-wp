/**
 * Digital Menu WP — Admin settings page interactions.
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        // Initialize color pickers
        $('.dmwp-color-picker').wpColorPicker();

        // Test Connection
        $('#dmwp-test-connection').on('click', function () {
            var $btn = $(this);
            var $status = $('#dmwp-connection-status');
            var apiUrl = $('#dmwp_api_url').val();
            var apiToken = $('#dmwp_api_token').val();

            if (!apiUrl || !apiToken) {
                $status.text(dmwpAdmin.i18n.error + ': missing URL or token')
                    .attr('class', 'dmwp-status dmwp-status-error');
                return;
            }

            $btn.prop('disabled', true);
            $status.text(dmwpAdmin.i18n.testing)
                .attr('class', 'dmwp-status dmwp-status-loading');

            $.post(dmwpAdmin.ajaxUrl, {
                action: 'dmwp_test_connection',
                nonce: dmwpAdmin.nonce,
                api_url: apiUrl,
                api_token: apiToken,
            }).done(function (response) {
                if (response.success) {
                    var msg = dmwpAdmin.i18n.connected;
                    if (response.data.store_name) {
                        msg += ' — ' + response.data.store_name;
                    }
                    if (response.data.menus && response.data.menus.length) {
                        msg += ' (' + response.data.menus.length + ' menu)';
                    }
                    $status.text(msg).attr('class', 'dmwp-status dmwp-status-success');
                } else {
                    $status.text(dmwpAdmin.i18n.error + ': ' + (response.data || ''))
                        .attr('class', 'dmwp-status dmwp-status-error');
                }
            }).fail(function () {
                $status.text(dmwpAdmin.i18n.error)
                    .attr('class', 'dmwp-status dmwp-status-error');
            }).always(function () {
                $btn.prop('disabled', false);
            });
        });

        // Clear Cache
        $('#dmwp-clear-cache').on('click', function () {
            if (!confirm(dmwpAdmin.i18n.confirm_clear)) {
                return;
            }

            var $btn = $(this);
            var $status = $('#dmwp-cache-status');

            $btn.prop('disabled', true);
            $status.text(dmwpAdmin.i18n.clearing)
                .attr('class', 'dmwp-status dmwp-status-loading');

            $.post(dmwpAdmin.ajaxUrl, {
                action: 'dmwp_clear_cache',
                nonce: dmwpAdmin.nonce,
            }).done(function (response) {
                if (response.success) {
                    $status.text(dmwpAdmin.i18n.cleared)
                        .attr('class', 'dmwp-status dmwp-status-success');
                } else {
                    $status.text(dmwpAdmin.i18n.error)
                        .attr('class', 'dmwp-status dmwp-status-error');
                }
            }).fail(function () {
                $status.text(dmwpAdmin.i18n.error)
                    .attr('class', 'dmwp-status dmwp-status-error');
            }).always(function () {
                $btn.prop('disabled', false);
            });
        });

        // Refresh Menus
        $('#dmwp-refresh-menus').on('click', function () {
            var $btn = $(this);
            var $status = $('#dmwp-cache-status');

            $btn.prop('disabled', true);
            $status.text(dmwpAdmin.i18n.refreshing)
                .attr('class', 'dmwp-status dmwp-status-loading');

            $.post(dmwpAdmin.ajaxUrl, {
                action: 'dmwp_refresh_menus',
                nonce: dmwpAdmin.nonce,
            }).done(function (response) {
                if (response.success) {
                    var msg = dmwpAdmin.i18n.refreshed;
                    if (response.data.menus) {
                        msg += ' (' + response.data.menus.length + ' menu)';
                    }
                    $status.text(msg).attr('class', 'dmwp-status dmwp-status-success');
                } else {
                    $status.text(dmwpAdmin.i18n.error + ': ' + (response.data || ''))
                        .attr('class', 'dmwp-status dmwp-status-error');
                }
            }).fail(function () {
                $status.text(dmwpAdmin.i18n.error)
                    .attr('class', 'dmwp-status dmwp-status-error');
            }).always(function () {
                $btn.prop('disabled', false);
            });
        });
    });
})(jQuery);
