<?php
/**
 * Single dish template.
 *
 * Available variables: $dish, $menu (parent menu for display flags),
 *                      $allergen_display, $allergen_number_map, $feature_display,
 *                      $hide_zero_prices, $this (DMWP_Renderer)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$show_figure    = $menu['show_figure'] ?? true;
$show_prices    = $menu['show_prices'] ?? true;
$show_allergens = ( $menu['show_allergens'] ?? true ) && ( $allergen_display ?? 'names' ) !== 'hidden';
$show_features  = ( $menu['show_features'] ?? true ) && ( $feature_display ?? 'names' ) !== 'hidden';
$show_desc      = $menu['show_description'] ?? true;
$stacked_figure = $menu['stacked_figure'] ?? false;
$stacked_prices = $menu['stacked_prices'] ?? false;

$a_mode         = $allergen_display ?? 'names';
$has_image      = $show_figure && ! empty( $dish['image'] );
$has_allergens  = $show_allergens && ! empty( $dish['allergens'] );
$has_features   = $show_features && ! empty( $dish['features'] );
$has_details    = ( $has_allergens && $a_mode !== 'numbers' ) || $has_features;

// Filter prices: remove zero prices if setting is on, and format with currency.
$raw_prices     = $dish['prices'] ?? [];
$prices         = [];
foreach ( $raw_prices as $p ) {
    $value_cents = (int) ( $p['price'] ?? 0 );
    if ( ( $hide_zero_prices ?? true ) && $value_cents === 0 ) {
        continue;
    }
    $p['price_display'] = $this->format_price( $value_cents / 100 );
    $prices[] = $p;
}

// Build superscript string for "numbers" mode.
$sup_numbers = '';
if ( $has_allergens && $a_mode === 'numbers' ) {
    $nums = [];
    foreach ( $dish['allergens'] as $a ) {
        $nums[] = $allergen_number_map[ $a['id'] ] ?? '?';
    }
    $sup_numbers = implode( ',', $nums );
}
?>
<div class="dmwp-dish<?php echo $stacked_figure ? ' dmwp-dish-stacked' : ''; ?>">

    <?php if ( $has_image && $stacked_figure ) : ?>
        <div class="dmwp-dish-image dmwp-dish-image-top">
            <img src="<?php echo esc_url( $dish['image'] ); ?>"
                 alt="<?php echo esc_attr( $dish['name'] ); ?>"
                 loading="lazy"
                 <?php if ( ! empty( $dish['image_thumb'] ) ) : ?>
                     srcset="<?php echo esc_url( $dish['image_thumb'] ); ?> 150w, <?php echo esc_url( $dish['image'] ); ?> 400w"
                     sizes="(max-width: 640px) 150px, 400px"
                 <?php endif; ?>
            />
        </div>
    <?php endif; ?>

    <div class="dmwp-dish-content">
        <div class="dmwp-dish-row">
            <?php if ( $has_image && ! $stacked_figure ) : ?>
                <div class="dmwp-dish-image dmwp-dish-image-side">
                    <img src="<?php echo esc_url( $dish['image_thumb'] ?? $dish['image'] ); ?>"
                         alt="<?php echo esc_attr( $dish['name'] ); ?>"
                         loading="lazy"
                    />
                </div>
            <?php endif; ?>

            <div class="dmwp-dish-main">
                <div class="dmwp-dish-header">
                    <span class="dmwp-dish-name"><?php echo esc_html( $dish['name'] ); ?><?php if ( $sup_numbers !== '' ) : ?><sup class="dmwp-allergen-sup"><?php echo esc_html( $sup_numbers ); ?></sup><?php endif; ?></span>

                    <?php if ( $show_prices && ! $stacked_prices && count( $prices ) === 1 ) : ?>
                        <span class="dmwp-dish-dots"></span>
                        <span class="dmwp-dish-price"><?php echo esc_html( $prices[0]['price_display'] ); ?></span>
                    <?php endif; ?>

                    <?php if ( $show_prices && ( $stacked_prices || count( $prices ) > 1 ) && ! empty( $prices ) ) : ?>
                        <span class="dmwp-dish-dots"></span>
                        <div class="dmwp-dish-prices">
                            <?php foreach ( $prices as $price ) : ?>
                                <div class="dmwp-dish-price-item">
                                    <?php if ( ! empty( $price['description'] ) ) : ?>
                                        <span class="dmwp-price-label"><?php echo esc_html( $price['description'] ); ?></span>
                                    <?php endif; ?>
                                    <span class="dmwp-price-value"><?php echo esc_html( $price['price_display'] ); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ( $show_desc && ! empty( $dish['description'] ) ) : ?>
                    <p class="dmwp-dish-description"><?php echo esc_html( $dish['description'] ); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( $has_details ) : ?>
            <div class="dmwp-dish-details">
                <?php if ( $has_allergens && $a_mode === 'names' ) : ?>
                    <div class="dmwp-dish-allergens">
                        <?php foreach ( $dish['allergens'] as $allergen ) : ?>
                            <span class="dmwp-allergen-badge" title="<?php echo esc_attr( $allergen['name'] ); ?>">
                                <?php echo esc_html( $allergen['name'] ); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $has_features ) : ?>
                    <div class="dmwp-dish-features">
                        <?php foreach ( $dish['features'] as $feature ) : ?>
                            <span class="dmwp-feature-badge" title="<?php echo esc_attr( $feature['name'] ); ?>">
                                <?php echo esc_html( $feature['name'] ); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
