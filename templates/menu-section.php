<?php
/**
 * Menu section template.
 *
 * Available variables: $section, $layout
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_accordion  = $layout === 'accordion';
$is_expanded   = ! $is_accordion || ! empty( $section['expanded'] );
$hide_header   = ! empty( $section['hide_header'] );
$hide_name     = ! empty( $section['hide_name'] );
?>
<div class="dmwp-section<?php echo $is_expanded ? ' dmwp-section-expanded' : ''; ?>"
     <?php if ( $is_accordion ) : ?>data-dmwp-accordion<?php endif; ?>
>
    <?php if ( ! $hide_header ) : ?>
        <?php if ( $is_accordion ) : ?>
            <button class="dmwp-section-header" aria-expanded="<?php echo $is_expanded ? 'true' : 'false'; ?>">
                <?php if ( ! $hide_name ) : ?>
                    <span class="dmwp-section-title"><?php echo esc_html( $section['name'] ); ?></span>
                <?php endif; ?>
                <span class="dmwp-section-toggle" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/></svg>
                </span>
            </button>
        <?php else : ?>
            <div class="dmwp-section-header">
                <?php if ( ! $hide_name ) : ?>
                    <h3 class="dmwp-section-title"><?php echo esc_html( $section['name'] ); ?></h3>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="dmwp-section-body"<?php echo ! $is_expanded ? ' style="display:none"' : ''; ?>>
        <?php if ( ! empty( $section['description'] ) ) : ?>
            <p class="dmwp-section-description"><?php echo esc_html( $section['description'] ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $section['info'] ) ) : ?>
            <p class="dmwp-section-info"><?php echo esc_html( $section['info'] ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $section['image'] ) ) : ?>
            <div class="dmwp-section-image">
                <img src="<?php echo esc_url( $section['image'] ); ?>" alt="<?php echo esc_attr( $section['name'] ); ?>" loading="lazy" />
            </div>
        <?php endif; ?>

        <div class="dmwp-dishes">
            <?php foreach ( $section['dishes'] ?? [] as $dish ) : ?>
                <?php include DMWP_PLUGIN_DIR . 'templates/menu-dish.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
