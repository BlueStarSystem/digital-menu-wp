<?php
/**
 * Admin settings page template.
 *
 * Available variables: $settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$colors     = $settings['colors'] ?? [];
$font_sizes = $settings['font_sizes'] ?? [];
?>
<div class="wrap dmwp-settings-wrap">
    <h1><?php echo esc_html__( 'Digital Menu Settings', 'digital-menu-wp' ); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields( 'dmwp_settings_group' ); ?>

        <!-- Connection -->
        <div class="dmwp-settings-card">
            <h2><?php echo esc_html__( 'Connection', 'digital-menu-wp' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="dmwp_api_url"><?php echo esc_html__( 'API URL', 'digital-menu-wp' ); ?></label></th>
                    <td>
                        <input type="url" id="dmwp_api_url" name="dmwp_settings[api_url]"
                               value="<?php echo esc_attr( $settings['api_url'] ?? '' ); ?>"
                               class="regular-text" placeholder="https://example.com/api" />
                        <p class="description"><?php echo esc_html__( 'Base URL of the Menu API (without /v1).', 'digital-menu-wp' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="dmwp_api_token"><?php echo esc_html__( 'API Token', 'digital-menu-wp' ); ?></label></th>
                    <td>
                        <input type="password" id="dmwp_api_token" name="dmwp_settings[api_token]"
                               value="<?php echo esc_attr( $settings['api_token'] ?? '' ); ?>"
                               class="regular-text" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <button type="button" id="dmwp-test-connection" class="button button-secondary">
                            <?php echo esc_html__( 'Test Connection', 'digital-menu-wp' ); ?>
                        </button>
                        <span id="dmwp-connection-status" class="dmwp-status"></span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Layout -->
        <div class="dmwp-settings-card">
            <h2><?php echo esc_html__( 'Layout', 'digital-menu-wp' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php echo esc_html__( 'Display Mode', 'digital-menu-wp' ); ?></th>
                    <td>
                        <fieldset>
                            <?php
                            $current_layout = $settings['layout'] ?? 'accordion';
                            $layouts = [
                                'accordion' => __( 'Accordion — collapsible sections', 'digital-menu-wp' ),
                                'tabs'      => __( 'Tabs — one menu at a time', 'digital-menu-wp' ),
                                'list'      => __( 'List — everything expanded', 'digital-menu-wp' ),
                            ];
                            foreach ( $layouts as $value => $label ) : ?>
                                <label style="display:block;margin-bottom:6px;">
                                    <input type="radio" name="dmwp_settings[layout]" value="<?php echo esc_attr( $value ); ?>"
                                        <?php checked( $current_layout, $value ); ?> />
                                    <?php echo esc_html( $label ); ?>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Allergen Display', 'digital-menu-wp' ); ?></th>
                    <td>
                        <fieldset>
                            <?php
                            $current_allergen = $settings['allergen_display'] ?? 'names';
                            $allergen_modes = [
                                'names'   => __( 'Names — show translated allergen names', 'digital-menu-wp' ),
                                'numbers' => __( 'Numbers — numeric references with legend', 'digital-menu-wp' ),
                                'hidden'  => __( 'Hidden — do not show allergens', 'digital-menu-wp' ),
                            ];
                            foreach ( $allergen_modes as $value => $label ) : ?>
                                <label style="display:block;margin-bottom:6px;">
                                    <input type="radio" name="dmwp_settings[allergen_display]" value="<?php echo esc_attr( $value ); ?>"
                                        <?php checked( $current_allergen, $value ); ?> />
                                    <?php echo esc_html( $label ); ?>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Feature Display', 'digital-menu-wp' ); ?></th>
                    <td>
                        <fieldset>
                            <?php
                            $current_feature = $settings['feature_display'] ?? 'names';
                            $feature_modes = [
                                'names'  => __( 'Visible — show feature badges', 'digital-menu-wp' ),
                                'hidden' => __( 'Hidden — do not show features', 'digital-menu-wp' ),
                            ];
                            foreach ( $feature_modes as $value => $label ) : ?>
                                <label style="display:block;margin-bottom:6px;">
                                    <input type="radio" name="dmwp_settings[feature_display]" value="<?php echo esc_attr( $value ); ?>"
                                        <?php checked( $current_feature, $value ); ?> />
                                    <?php echo esc_html( $label ); ?>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Currency -->
        <div class="dmwp-settings-card">
            <h2><?php echo esc_html__( 'Currency', 'digital-menu-wp' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="dmwp_currency_symbol"><?php echo esc_html__( 'Currency Symbol', 'digital-menu-wp' ); ?></label></th>
                    <td>
                        <input type="text" id="dmwp_currency_symbol" name="dmwp_settings[currency_symbol]"
                               value="<?php echo esc_attr( $settings['currency_symbol'] ?? '€' ); ?>"
                               class="small-text" />
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Symbol Position', 'digital-menu-wp' ); ?></th>
                    <td>
                        <select name="dmwp_settings[currency_position]">
                            <?php
                            $current_pos = $settings['currency_position'] ?? 'after';
                            $positions = [
                                'before'       => __( 'Before — $10.00', 'digital-menu-wp' ),
                                'after'        => __( 'After — 10.00€', 'digital-menu-wp' ),
                                'before_space' => __( 'Before with space — $ 10.00', 'digital-menu-wp' ),
                                'after_space'  => __( 'After with space — 10.00 €', 'digital-menu-wp' ),
                            ];
                            foreach ( $positions as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_pos, $value ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Decimal Separator', 'digital-menu-wp' ); ?></th>
                    <td>
                        <input type="text" name="dmwp_settings[decimal_separator]"
                               value="<?php echo esc_attr( $settings['decimal_separator'] ?? ',' ); ?>"
                               class="small-text" />
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Thousand Separator', 'digital-menu-wp' ); ?></th>
                    <td>
                        <input type="text" name="dmwp_settings[thousand_separator]"
                               value="<?php echo esc_attr( $settings['thousand_separator'] ?? '.' ); ?>"
                               class="small-text" />
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Decimal Places', 'digital-menu-wp' ); ?></th>
                    <td>
                        <select name="dmwp_settings[decimal_places]">
                            <?php $current_dec = (int) ( $settings['decimal_places'] ?? 2 ); ?>
                            <option value="0" <?php selected( $current_dec, 0 ); ?>>0</option>
                            <option value="1" <?php selected( $current_dec, 1 ); ?>>1</option>
                            <option value="2" <?php selected( $current_dec, 2 ); ?>>2</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Hide Zero Prices', 'digital-menu-wp' ); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="dmwp_settings[hide_zero_prices]" value="1"
                                <?php checked( $settings['hide_zero_prices'] ?? true ); ?> />
                            <?php echo esc_html__( 'Do not show prices with value 0', 'digital-menu-wp' ); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Appearance -->
        <div class="dmwp-settings-card">
            <h2><?php echo esc_html__( 'Appearance', 'digital-menu-wp' ); ?></h2>
            <table class="form-table">
                <?php
                $color_fields = [
                    'accent'        => __( 'Accent Color', 'digital-menu-wp' ),
                    'section_bg'    => __( 'Section Background', 'digital-menu-wp' ),
                    'hover_bg'      => __( 'Hover Background', 'digital-menu-wp' ),
                    'section_title' => __( 'Section Title Color', 'digital-menu-wp' ),
                    'expanded_bg'   => __( 'Expanded Background', 'digital-menu-wp' ),
                    'description'   => __( 'Description Text Color', 'digital-menu-wp' ),
                    'allergen_bg'   => __( 'Allergen Badge Background', 'digital-menu-wp' ),
                    'allergen_text' => __( 'Allergen Badge Text', 'digital-menu-wp' ),
                    'feature_bg'    => __( 'Feature Badge Background', 'digital-menu-wp' ),
                    'feature_text'  => __( 'Feature Badge Text', 'digital-menu-wp' ),
                ];
                foreach ( $color_fields as $key => $label ) : ?>
                    <tr>
                        <th><label><?php echo esc_html( $label ); ?></label></th>
                        <td>
                            <input type="text" class="dmwp-color-picker"
                                   name="dmwp_settings[colors][<?php echo esc_attr( $key ); ?>]"
                                   value="<?php echo esc_attr( $colors[ $key ] ?? '' ); ?>" />
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th><?php echo esc_html__( 'Size Scale', 'digital-menu-wp' ); ?></th>
                    <td>
                        <select name="dmwp_settings[scale]">
                            <option value="small" <?php selected( $settings['scale'] ?? '', 'small' ); ?>><?php echo esc_html__( 'Small', 'digital-menu-wp' ); ?></option>
                            <option value="medium" <?php selected( $settings['scale'] ?? 'medium', 'medium' ); ?>><?php echo esc_html__( 'Medium', 'digital-menu-wp' ); ?></option>
                            <option value="large" <?php selected( $settings['scale'] ?? '', 'large' ); ?>><?php echo esc_html__( 'Large', 'digital-menu-wp' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Border Radius', 'digital-menu-wp' ); ?></th>
                    <td>
                        <select name="dmwp_settings[radius]">
                            <option value="none" <?php selected( $settings['radius'] ?? '', 'none' ); ?>><?php echo esc_html__( 'None', 'digital-menu-wp' ); ?></option>
                            <option value="light" <?php selected( $settings['radius'] ?? '', 'light' ); ?>><?php echo esc_html__( 'Light', 'digital-menu-wp' ); ?></option>
                            <option value="medium" <?php selected( $settings['radius'] ?? 'medium', 'medium' ); ?>><?php echo esc_html__( 'Medium', 'digital-menu-wp' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Font Sizes -->
        <div class="dmwp-settings-card">
            <h2><?php echo esc_html__( 'Font Sizes', 'digital-menu-wp' ); ?></h2>
            <table class="form-table">
                <?php
                $font_fields = [
                    'menu_title'    => __( 'Menu Title', 'digital-menu-wp' ),
                    'section_title' => __( 'Section Title', 'digital-menu-wp' ),
                    'dish_name'     => __( 'Dish Name', 'digital-menu-wp' ),
                    'price'         => __( 'Price', 'digital-menu-wp' ),
                    'description'   => __( 'Description', 'digital-menu-wp' ),
                    'badges'        => __( 'Badges', 'digital-menu-wp' ),
                ];
                foreach ( $font_fields as $key => $label ) :
                    $current = $font_sizes[ $key ] ?? 'medium';
                ?>
                    <tr>
                        <th><?php echo esc_html( $label ); ?></th>
                        <td>
                            <select name="dmwp_settings[font_sizes][<?php echo esc_attr( $key ); ?>]">
                                <option value="small" <?php selected( $current, 'small' ); ?>><?php echo esc_html__( 'Small', 'digital-menu-wp' ); ?></option>
                                <option value="medium" <?php selected( $current, 'medium' ); ?>><?php echo esc_html__( 'Medium', 'digital-menu-wp' ); ?></option>
                                <option value="large" <?php selected( $current, 'large' ); ?>><?php echo esc_html__( 'Large', 'digital-menu-wp' ); ?></option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Advanced -->
        <div class="dmwp-settings-card">
            <h2><?php echo esc_html__( 'Advanced', 'digital-menu-wp' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php echo esc_html__( 'Cache TTL', 'digital-menu-wp' ); ?></th>
                    <td>
                        <select name="dmwp_settings[cache_ttl]">
                            <?php foreach ( [ 5, 15, 30, 60 ] as $ttl ) : ?>
                                <option value="<?php echo $ttl; ?>" <?php selected( (int) ( $settings['cache_ttl'] ?? 15 ), $ttl ); ?>>
                                    <?php printf( esc_html__( '%d minutes', 'digital-menu-wp' ), $ttl ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo esc_html__( 'Language', 'digital-menu-wp' ); ?></th>
                    <td>
                        <select name="dmwp_settings[language]">
                            <option value="auto" <?php selected( $settings['language'] ?? 'auto', 'auto' ); ?>>
                                <?php echo esc_html__( 'Automatic (WordPress locale)', 'digital-menu-wp' ); ?>
                            </option>
                            <option value="it" <?php selected( $settings['language'] ?? '', 'it' ); ?>>Italiano</option>
                            <option value="en" <?php selected( $settings['language'] ?? '', 'en' ); ?>>English</option>
                            <option value="de" <?php selected( $settings['language'] ?? '', 'de' ); ?>>Deutsch</option>
                            <option value="fr" <?php selected( $settings['language'] ?? '', 'fr' ); ?>>Fran&ccedil;ais</option>
                            <option value="es" <?php selected( $settings['language'] ?? '', 'es' ); ?>>Espa&ntilde;ol</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <button type="button" id="dmwp-clear-cache" class="button button-secondary">
                            <?php echo esc_html__( 'Clear Cache', 'digital-menu-wp' ); ?>
                        </button>
                        <button type="button" id="dmwp-refresh-menus" class="button button-secondary">
                            <?php echo esc_html__( 'Refresh Menus Now', 'digital-menu-wp' ); ?>
                        </button>
                        <span id="dmwp-cache-status" class="dmwp-status"></span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Shortcode Help -->
        <div class="dmwp-settings-card">
            <h2><?php echo esc_html__( 'Usage', 'digital-menu-wp' ); ?></h2>
            <p><?php echo esc_html__( 'Add the shortcode to any page or post:', 'digital-menu-wp' ); ?></p>
            <code>[menu-digitale]</code>
            <p class="description" style="margin-top:10px;">
                <?php echo esc_html__( 'Optional attributes:', 'digital-menu-wp' ); ?>
                <code>menu="440,441"</code>
                <code>layout="tabs"</code>
                <code>lang="en"</code>
            </p>
        </div>

        <?php submit_button(); ?>
    </form>
</div>
