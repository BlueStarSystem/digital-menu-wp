<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcode [menu-digitale] handler.
 */
class DMWP_Shortcode {

    private static ?self $instance = null;

    public static function instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        add_shortcode( 'menu-digitale', [ $this, 'render' ] );
    }

    /**
     * Render the shortcode.
     *
     * Attributes:
     *   menu     — comma-separated menu IDs
     *   layout   — accordion|tabs|list
     *   lang     — language override
     */
    public function render( $atts ): string {
        $atts = shortcode_atts( [
            'menu'   => '',
            'layout' => '',
            'lang'   => '',
        ], $atts, 'menu-digitale' );

        dmwp_enqueue_frontend_assets();

        $locale = $this->resolve_locale( $atts['lang'] );
        $cache  = new DMWP_Cache();
        $data   = $cache->get_menu_data( $locale );

        if ( $data === null ) {
            if ( current_user_can( 'manage_options' ) ) {
                return '<div class="dmwp-error">'
                    . esc_html__( 'Digital Menu: unable to load menu data. Check plugin settings.', 'digital-menu-wp' )
                    . '</div>';
            }
            return '<div class="dmwp-menu-container"><p>'
                . esc_html__( 'Menu loading...', 'digital-menu-wp' )
                . '</p></div>';
        }

        $renderer = new DMWP_Renderer();
        return $renderer->render( $data, $atts );
    }

    /**
     * Resolve locale: shortcode attr > settings > WP locale.
     */
    private function resolve_locale( string $override ): string {
        if ( $override !== '' ) {
            return $override;
        }

        $settings = get_option( 'dmwp_settings', [] );
        $lang     = $settings['language'] ?? 'auto';

        if ( $lang !== 'auto' ) {
            return $lang;
        }

        // WordPress locale → 2-letter code
        $wp_locale = get_locale();
        return substr( $wp_locale, 0, 2 );
    }
}
