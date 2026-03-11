<?php
/**
 * Legend template for allergens and features.
 *
 * Available variables: $legends, $allergen_display, $allergen_number_map
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$a_mode    = $allergen_display ?? 'names';
$f_mode    = $feature_display ?? 'names';
$allergens = $legends['allergens'] ?? [];
$features  = $legends['features'] ?? [];

// Hide allergens section entirely when display is hidden.
if ( $a_mode === 'hidden' ) {
    $allergens = [];
}

// Hide features section entirely when display is hidden.
if ( $f_mode === 'hidden' ) {
    $features = [];
}

if ( empty( $allergens ) && empty( $features ) ) {
    return;
}
?>
<div class="dmwp-legend">
    <?php if ( ! empty( $allergens ) ) : ?>
        <div class="dmwp-legend-group">
            <h4 class="dmwp-legend-title"><?php echo esc_html__( 'Allergens', 'digital-menu-wp' ); ?></h4>
            <div class="dmwp-legend-items">
                <?php foreach ( $allergens as $allergen ) :
                    $number = $allergen_number_map[ $allergen['id'] ] ?? null;
                ?>
                    <div class="dmwp-legend-item">
                        <?php if ( $a_mode === 'numbers' && $number !== null ) : ?>
                            <span class="dmwp-allergen-badge dmwp-badge-number"><?php echo esc_html( $number ); ?></span>
                            <span class="dmwp-legend-name"><?php echo esc_html( $allergen['name'] ); ?></span>
                        <?php else : ?>
                            <span class="dmwp-allergen-badge"><?php echo esc_html( $allergen['name'] ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $features ) ) : ?>
        <div class="dmwp-legend-group">
            <h4 class="dmwp-legend-title"><?php echo esc_html__( 'Features', 'digital-menu-wp' ); ?></h4>
            <div class="dmwp-legend-items">
                <?php foreach ( $features as $feature ) : ?>
                    <div class="dmwp-legend-item">
                        <span class="dmwp-feature-badge"><?php echo esc_html( $feature['name'] ); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
