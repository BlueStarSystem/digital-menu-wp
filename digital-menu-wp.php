<?php
/**
 * Plugin Name: Digital Menu WP
 * Plugin URI:  https://bluestarsystem.it
 * Description: Display digital menus from your restaurant management platform directly on your WordPress site.
 * Version:     1.0.0
 * Author:      BlueStarSystem
 * Author URI:  https://bluestarsystem.it
 * Text Domain: digital-menu-wp
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DMWP_VERSION', '1.0.0' );
define( 'DMWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DMWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DMWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once DMWP_PLUGIN_DIR . 'includes/class-dmwp-api-client.php';
require_once DMWP_PLUGIN_DIR . 'includes/class-dmwp-cache.php';
require_once DMWP_PLUGIN_DIR . 'includes/class-dmwp-renderer.php';
require_once DMWP_PLUGIN_DIR . 'includes/class-dmwp-settings.php';
require_once DMWP_PLUGIN_DIR . 'includes/class-dmwp-shortcode.php';
require_once DMWP_PLUGIN_DIR . 'includes/class-dmwp-block.php';

/**
 * Initialize the plugin.
 */
function dmwp_init(): void {
    load_plugin_textdomain( 'digital-menu-wp', false, dirname( DMWP_PLUGIN_BASENAME ) . '/languages' );

    DMWP_Settings::instance()->init();
    DMWP_Shortcode::instance()->init();
    DMWP_Block::instance()->init();
}
add_action( 'plugins_loaded', 'dmwp_init' );

/**
 * Enqueue frontend assets only when shortcode or block is rendered.
 */
function dmwp_enqueue_frontend_assets(): void {
    if ( ! wp_script_is( 'dmwp-menu', 'enqueued' ) ) {
        wp_enqueue_style( 'dmwp-menu', DMWP_PLUGIN_URL . 'assets/css/dmwp-menu.css', [], DMWP_VERSION );
        wp_enqueue_script( 'dmwp-menu', DMWP_PLUGIN_URL . 'assets/js/dmwp-menu.js', [], DMWP_VERSION, true );
    }
}

/**
 * Plugin activation.
 */
function dmwp_activate(): void {
    // Set default options
    $defaults = [
        'api_url'            => '',
        'api_token'          => '',
        'layout'             => 'accordion',
        'allergen_display'   => 'names',
        'feature_display'    => 'names',
        'cache_ttl'          => 15,
        'language'           => 'auto',
        'colors'             => [
            'accent'        => '#059669',
            'section_bg'    => '#f8fafc',
            'hover_bg'      => '#f0fdf4',
            'section_title' => '#222222',
            'expanded_bg'   => '#f0fdf4',
            'description'   => '#6b7280',
            'allergen_bg'   => '#fef3c7',
            'allergen_text' => '#92400e',
            'feature_bg'    => '#dbeafe',
            'feature_text'  => '#1e40af',
        ],
        'font_sizes'         => [
            'menu_title'    => 'medium',
            'section_title' => 'medium',
            'dish_name'     => 'medium',
            'price'         => 'medium',
            'description'   => 'medium',
            'badges'        => 'medium',
        ],
        'currency_symbol'    => '€',
        'currency_position'  => 'after',
        'decimal_separator'  => ',',
        'thousand_separator' => '.',
        'decimal_places'     => 2,
        'hide_zero_prices'   => true,
        'scale'              => 'medium',
        'radius'             => 'medium',
        'menus'              => [],
    ];

    if ( ! get_option( 'dmwp_settings' ) ) {
        add_option( 'dmwp_settings', $defaults );
    }
}
register_activation_hook( __FILE__, 'dmwp_activate' );
