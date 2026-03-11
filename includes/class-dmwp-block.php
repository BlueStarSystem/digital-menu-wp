<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Gutenberg block registration.
 */
class DMWP_Block {

    private static ?self $instance = null;

    public static function instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        add_action( 'init', [ $this, 'register_block' ] );
    }

    /**
     * Register the Digital Menu block.
     */
    public function register_block(): void {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type( DMWP_PLUGIN_DIR . 'assets/block', [
            'render_callback' => [ $this, 'render' ],
        ] );
    }

    /**
     * Server-side render callback for the block.
     */
    public function render( array $attributes ): string {
        $atts = [
            'menu'   => $attributes['menuIds'] ?? '',
            'layout' => $attributes['layout'] ?? '',
            'lang'   => $attributes['language'] ?? '',
        ];

        $shortcode = DMWP_Shortcode::instance();
        return $shortcode->render( $atts );
    }
}
