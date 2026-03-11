<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Top-level settings page: Digital Menu.
 */
class DMWP_Settings {

    private static ?self $instance = null;

    public static function instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'wp_ajax_dmwp_test_connection', [ $this, 'ajax_test_connection' ] );
        add_action( 'wp_ajax_dmwp_clear_cache', [ $this, 'ajax_clear_cache' ] );
        add_action( 'wp_ajax_dmwp_refresh_menus', [ $this, 'ajax_refresh_menus' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    public function add_menu_page(): void {
        add_menu_page(
            __( 'Digital Menu', 'digital-menu-wp' ),
            __( 'Digital Menu', 'digital-menu-wp' ),
            'manage_options',
            'dmwp-settings',
            [ $this, 'render_page' ],
            'dashicons-food',
            26
        );
    }

    public function enqueue_admin_assets( string $hook ): void {
        if ( $hook !== 'toplevel_page_dmwp-settings' ) {
            return;
        }

        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'dmwp-admin', DMWP_PLUGIN_URL . 'assets/css/dmwp-admin.css', [], DMWP_VERSION );
        wp_enqueue_script( 'dmwp-admin', DMWP_PLUGIN_URL . 'assets/js/dmwp-admin.js', [ 'wp-color-picker', 'jquery' ], DMWP_VERSION, true );
        wp_localize_script( 'dmwp-admin', 'dmwpAdmin', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'dmwp_admin' ),
            'i18n'    => [
                'testing'       => __( 'Testing...', 'digital-menu-wp' ),
                'connected'     => __( 'Connected!', 'digital-menu-wp' ),
                'error'         => __( 'Connection failed', 'digital-menu-wp' ),
                'clearing'      => __( 'Clearing...', 'digital-menu-wp' ),
                'cleared'       => __( 'Cache cleared!', 'digital-menu-wp' ),
                'refreshing'    => __( 'Refreshing...', 'digital-menu-wp' ),
                'refreshed'     => __( 'Menus updated!', 'digital-menu-wp' ),
                'confirm_clear' => __( 'Clear all cached menu data?', 'digital-menu-wp' ),
            ],
        ] );
    }

    public function register_settings(): void {
        register_setting( 'dmwp_settings_group', 'dmwp_settings', [
            'type'              => 'array',
            'sanitize_callback' => [ $this, 'sanitize_settings' ],
        ] );
    }

    public function sanitize_settings( $input ): array {
        $sanitized = [];

        $sanitized['api_url']   = esc_url_raw( $input['api_url'] ?? '' );
        $sanitized['api_token'] = sanitize_text_field( $input['api_token'] ?? '' );
        $sanitized['layout']    = in_array( $input['layout'] ?? '', [ 'accordion', 'tabs', 'list' ], true )
            ? $input['layout'] : 'accordion';
        $sanitized['cache_ttl'] = in_array( (int) ( $input['cache_ttl'] ?? 15 ), [ 5, 15, 30, 60 ], true )
            ? (int) $input['cache_ttl'] : 15;
        $sanitized['allergen_display'] = in_array( $input['allergen_display'] ?? '', [ 'names', 'numbers', 'hidden' ], true )
            ? $input['allergen_display'] : 'names';
        $sanitized['feature_display'] = in_array( $input['feature_display'] ?? '', [ 'names', 'hidden' ], true )
            ? $input['feature_display'] : 'names';
        $sanitized['language']  = sanitize_text_field( $input['language'] ?? 'auto' );
        $sanitized['scale']     = in_array( $input['scale'] ?? '', [ 'small', 'medium', 'large' ], true )
            ? $input['scale'] : 'medium';
        $sanitized['radius']    = in_array( $input['radius'] ?? '', [ 'none', 'light', 'medium' ], true )
            ? $input['radius'] : 'medium';

        $sanitized['colors'] = [
            'accent'        => sanitize_hex_color( $input['colors']['accent'] ?? '#059669' ) ?: '#059669',
            'section_bg'    => sanitize_hex_color( $input['colors']['section_bg'] ?? '#f8fafc' ) ?: '#f8fafc',
            'hover_bg'      => sanitize_hex_color( $input['colors']['hover_bg'] ?? '#f0fdf4' ) ?: '#f0fdf4',
            'section_title' => sanitize_hex_color( $input['colors']['section_title'] ?? '#222222' ) ?: '#222222',
            'expanded_bg'   => sanitize_hex_color( $input['colors']['expanded_bg'] ?? '#f0fdf4' ) ?: '#f0fdf4',
            'description'   => sanitize_hex_color( $input['colors']['description'] ?? '#6b7280' ) ?: '#6b7280',
            'allergen_bg'   => sanitize_hex_color( $input['colors']['allergen_bg'] ?? '#fef3c7' ) ?: '#fef3c7',
            'allergen_text' => sanitize_hex_color( $input['colors']['allergen_text'] ?? '#92400e' ) ?: '#92400e',
            'feature_bg'    => sanitize_hex_color( $input['colors']['feature_bg'] ?? '#dbeafe' ) ?: '#dbeafe',
            'feature_text'  => sanitize_hex_color( $input['colors']['feature_text'] ?? '#1e40af' ) ?: '#1e40af',
        ];

        $valid_sizes = [ 'small', 'medium', 'large' ];
        $sanitized['font_sizes'] = [
            'menu_title'    => in_array( $input['font_sizes']['menu_title'] ?? '', $valid_sizes, true ) ? $input['font_sizes']['menu_title'] : 'medium',
            'section_title' => in_array( $input['font_sizes']['section_title'] ?? '', $valid_sizes, true ) ? $input['font_sizes']['section_title'] : 'medium',
            'dish_name'     => in_array( $input['font_sizes']['dish_name'] ?? '', $valid_sizes, true ) ? $input['font_sizes']['dish_name'] : 'medium',
            'price'         => in_array( $input['font_sizes']['price'] ?? '', $valid_sizes, true ) ? $input['font_sizes']['price'] : 'medium',
            'description'   => in_array( $input['font_sizes']['description'] ?? '', $valid_sizes, true ) ? $input['font_sizes']['description'] : 'medium',
            'badges'        => in_array( $input['font_sizes']['badges'] ?? '', $valid_sizes, true ) ? $input['font_sizes']['badges'] : 'medium',
        ];

        // Currency settings.
        $sanitized['currency_symbol']    = sanitize_text_field( $input['currency_symbol'] ?? '€' ) ?: '€';
        $sanitized['currency_position']  = in_array( $input['currency_position'] ?? '', [ 'before', 'after', 'before_space', 'after_space' ], true )
            ? $input['currency_position'] : 'after';
        $sanitized['decimal_separator']  = sanitize_text_field( $input['decimal_separator'] ?? ',' ) ?: ',';
        $sanitized['thousand_separator'] = sanitize_text_field( $input['thousand_separator'] ?? '.' );
        $sanitized['decimal_places']     = in_array( (int) ( $input['decimal_places'] ?? 2 ), [ 0, 1, 2 ], true )
            ? (int) $input['decimal_places'] : 2;
        $sanitized['hide_zero_prices']   = ! empty( $input['hide_zero_prices'] );

        $sanitized['menus'] = array_map( 'intval', (array) ( $input['menus'] ?? [] ) );

        return $sanitized;
    }

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $settings = get_option( 'dmwp_settings', [] );
        include DMWP_PLUGIN_DIR . 'templates/settings-page.php';
    }

    /**
     * AJAX: Test API connection.
     */
    public function ajax_test_connection(): void {
        check_ajax_referer( 'dmwp_admin', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized.', 'digital-menu-wp' ) );
        }

        $api_url   = sanitize_text_field( $_POST['api_url'] ?? '' );
        $api_token = sanitize_text_field( $_POST['api_token'] ?? '' );

        $client = new DMWP_Api_Client( $api_url, $api_token );
        $result = $client->test();

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        wp_send_json_success( $result );
    }

    /**
     * AJAX: Clear cached menu data.
     */
    public function ajax_clear_cache(): void {
        check_ajax_referer( 'dmwp_admin', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized.', 'digital-menu-wp' ) );
        }

        $cache = new DMWP_Cache();
        $cache->clear();

        wp_send_json_success();
    }

    /**
     * AJAX: Force refresh menus from API.
     */
    public function ajax_refresh_menus(): void {
        check_ajax_referer( 'dmwp_admin', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized.', 'digital-menu-wp' ) );
        }

        $cache = new DMWP_Cache();
        $data  = $cache->refresh();

        if ( $data === null ) {
            wp_send_json_error( __( 'Unable to fetch menus from API.', 'digital-menu-wp' ) );
        }

        $menus = array_map( fn( $m ) => [
            'id'   => $m['id'],
            'name' => $m['name'],
        ], $data['menus'] ?? [] );

        wp_send_json_success( [ 'menus' => $menus ] );
    }
}
