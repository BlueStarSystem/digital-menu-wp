<?php
/**
 * Main menu wrapper template.
 *
 * Available variables: $menus, $legends, $store, $layout, $css_vars, $this (DMWP_Renderer)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="dmwp-menu-container dmwp-layout-<?php echo esc_attr( $layout ); ?>" style="<?php echo $css_vars; ?>">

    <?php if ( $layout === 'tabs' && count( $menus ) > 1 ) : ?>
        <div class="dmwp-tabs-nav" role="tablist">
            <?php foreach ( $menus as $i => $menu ) : ?>
                <button
                    class="dmwp-tab-btn<?php echo $i === 0 ? ' dmwp-tab-active' : ''; ?>"
                    role="tab"
                    aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                    aria-controls="dmwp-tab-<?php echo esc_attr( $menu['id'] ); ?>"
                    data-dmwp-tab="<?php echo esc_attr( $menu['id'] ); ?>"
                >
                    <?php echo esc_html( $menu['name'] ); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php foreach ( $menus as $i => $menu ) : ?>
        <div
            class="dmwp-menu<?php echo ( $layout === 'tabs' && $i > 0 ) ? ' dmwp-tab-hidden' : ''; ?>"
            id="dmwp-tab-<?php echo esc_attr( $menu['id'] ); ?>"
            <?php if ( $layout === 'tabs' ) : ?>
                role="tabpanel"
                aria-labelledby="dmwp-tab-btn-<?php echo esc_attr( $menu['id'] ); ?>"
            <?php endif; ?>
        >
            <?php if ( $layout !== 'tabs' || count( $menus ) === 1 ) : ?>
                <div class="dmwp-menu-header">
                    <h2 class="dmwp-menu-title"><?php echo esc_html( $menu['name'] ); ?></h2>
                    <?php if ( ! empty( $menu['info'] ) ) : ?>
                        <p class="dmwp-menu-info"><?php echo esc_html( $menu['info'] ); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $menu['image'] ) ) : ?>
                <div class="dmwp-menu-image">
                    <img src="<?php echo esc_url( $menu['image'] ); ?>" alt="<?php echo esc_attr( $menu['name'] ); ?>" loading="lazy" />
                </div>
            <?php endif; ?>

            <?php foreach ( $menu['sections'] ?? [] as $section ) : ?>
                <?php include DMWP_PLUGIN_DIR . 'templates/menu-section.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <?php include DMWP_PLUGIN_DIR . 'templates/menu-legend.php'; ?>
</div>
