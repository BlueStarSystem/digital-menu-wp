<?php
/**
 * Clean up plugin data on uninstall.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'dmwp_settings' );
delete_option( 'dmwp_menu_data' );
delete_option( 'dmwp_menu_etag' );
delete_option( 'dmwp_menu_updated_at' );
delete_option( 'dmwp_last_check' );
