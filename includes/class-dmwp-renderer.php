<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * HTML renderer for menu data.
 */
class DMWP_Renderer {

    private array $settings;

    public function __construct() {
        $this->settings = get_option( 'dmwp_settings', [] );
    }

    /**
     * Render the full menu output.
     *
     * @param array  $data   Menu data from API.
     * @param array  $atts   Shortcode/block attributes (menu, layout, lang overrides).
     */
    public function render( array $data, array $atts = [] ): string {
        $menus  = $data['menus'] ?? [];
        $legends = $data['legends'] ?? [];
        $store  = $data['store'] ?? [];

        // Filter specific menus if requested
        if ( ! empty( $atts['menu'] ) ) {
            $ids   = array_map( 'intval', explode( ',', $atts['menu'] ) );
            $menus = array_values( array_filter( $menus, fn( $m ) => in_array( $m['id'], $ids, true ) ) );
        }

        if ( empty( $menus ) ) {
            return '<div class="dmwp-menu-container"><p>' . esc_html__( 'No menus available.', 'digital-menu-wp' ) . '</p></div>';
        }

        $layout           = ( ! empty( $atts['layout'] ) ? $atts['layout'] : null ) ?? $this->settings['layout'] ?? 'accordion';
        $allergen_display = $this->settings['allergen_display'] ?? 'names';
        $feature_display  = $this->settings['feature_display'] ?? 'names';
        $hide_zero_prices = $this->settings['hide_zero_prices'] ?? true;
        $css_vars         = $this->build_css_variables();

        // Build allergen number map for "numbers" mode.
        $allergen_number_map = [];
        if ( $allergen_display === 'numbers' && ! empty( $legends['allergens'] ) ) {
            foreach ( $legends['allergens'] as $index => $allergen ) {
                $allergen_number_map[ $allergen['id'] ] = $index + 1;
            }
        }

        ob_start();
        include DMWP_PLUGIN_DIR . 'templates/menu-wrapper.php';
        return ob_get_clean();
    }

    /**
     * Build inline CSS custom properties string.
     */
    private function build_css_variables(): string {
        $colors     = $this->settings['colors'] ?? [];
        $font_sizes = $this->settings['font_sizes'] ?? [];
        $scale      = $this->settings['scale'] ?? 'medium';
        $radius     = $this->settings['radius'] ?? 'medium';

        $scale_value = match ( $scale ) {
            'small' => '0.875',
            'large' => '1.125',
            default => '1',
        };

        $radius_value = match ( $radius ) {
            'none'  => '0',
            'light' => '0.25rem',
            default => '0.5rem',
        };

        $size_map = [
            'small' => '0.85',
            'large' => '1.15',
        ];

        $accent = $colors['accent'] ?? '#059669';

        $vars = [
            '--dmwp-accent'              => $accent,
            '--dmwp-accent-contrast'     => $this->contrast_color( $accent ),
            '--dmwp-section-bg'          => $colors['section_bg'] ?? '#f8fafc',
            '--dmwp-hover-bg'            => $colors['hover_bg'] ?? '#f0fdf4',
            '--dmwp-section-title-color'  => $colors['section_title'] ?? '#222222',
            '--dmwp-expanded-bg'         => $colors['expanded_bg'] ?? '#f0fdf4',
            '--dmwp-description-color'   => $colors['description'] ?? '#6b7280',
            '--dmwp-allergen-bg'         => $colors['allergen_bg'] ?? '#fef3c7',
            '--dmwp-allergen-text'       => $colors['allergen_text'] ?? '#92400e',
            '--dmwp-feature-bg'          => $colors['feature_bg'] ?? '#dbeafe',
            '--dmwp-feature-text'        => $colors['feature_text'] ?? '#1e40af',
            '--dmwp-radius'              => $radius_value,
            '--dmwp-scale'               => $scale_value,
        ];

        // Font size multipliers.
        $font_fields = [ 'menu_title', 'section_title', 'dish_name', 'price', 'description', 'badges' ];
        foreach ( $font_fields as $field ) {
            $size = $font_sizes[ $field ] ?? 'medium';
            if ( isset( $size_map[ $size ] ) ) {
                $vars[ '--dmwp-fs-' . str_replace( '_', '-', $field ) ] = $size_map[ $size ];
            }
        }

        $parts = [];
        foreach ( $vars as $prop => $val ) {
            $parts[] = esc_attr( $prop ) . ':' . esc_attr( $val );
        }

        return implode( ';', $parts );
    }

    /**
     * Simple contrast color (black or white) for a hex background.
     */
    private function contrast_color( string $hex ): string {
        $hex = ltrim( $hex, '#' );

        if ( strlen( $hex ) === 3 ) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec( substr( $hex, 0, 2 ) );
        $g = hexdec( substr( $hex, 2, 2 ) );
        $b = hexdec( substr( $hex, 4, 2 ) );

        // Relative luminance
        $luminance = ( 0.299 * $r + 0.587 * $g + 0.114 * $b ) / 255;

        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }

    /**
     * Format a price value according to plugin settings.
     */
    public function format_price( float $value ): string {
        $symbol    = $this->settings['currency_symbol'] ?? '€';
        $position  = $this->settings['currency_position'] ?? 'after';
        $dec_sep   = $this->settings['decimal_separator'] ?? ',';
        $thou_sep  = $this->settings['thousand_separator'] ?? '.';
        $decimals  = (int) ( $this->settings['decimal_places'] ?? 2 );

        $number = number_format( $value, $decimals, $dec_sep, $thou_sep );

        return match ( $position ) {
            'before'       => $symbol . $number,
            'before_space' => $symbol . ' ' . $number,
            'after_space'  => $number . ' ' . $symbol,
            default        => $number . $symbol,
        };
    }

    /**
     * Render a single menu for tabs/accordion.
     */
    public function render_menu( array $menu ): string {
        ob_start();
        include DMWP_PLUGIN_DIR . 'templates/menu-tabs.php';
        return ob_get_clean();
    }

    /**
     * Render a section with its dishes.
     */
    public function render_section( array $section, array $store = [] ): string {
        ob_start();
        include DMWP_PLUGIN_DIR . 'templates/menu-section.php';
        return ob_get_clean();
    }

    /**
     * Render a single dish.
     */
    public function render_dish( array $dish, array $store = [] ): string {
        ob_start();
        include DMWP_PLUGIN_DIR . 'templates/menu-dish.php';
        return ob_get_clean();
    }

    /**
     * Render the allergen/feature legend.
     */
    public function render_legend( array $legends ): string {
        if ( empty( $legends['allergens'] ) && empty( $legends['features'] ) ) {
            return '';
        }

        ob_start();
        include DMWP_PLUGIN_DIR . 'templates/menu-legend.php';
        return ob_get_clean();
    }
}
