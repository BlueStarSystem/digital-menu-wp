/**
 * Digital Menu WP — Frontend interactions (~4KB)
 * Accordion toggle, tabs switching, dish details expand/collapse
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        initAccordions();
        initTabs();
    }

    /**
     * Accordion: toggle sections on header click.
     */
    function initAccordions() {
        document.querySelectorAll('[data-dmwp-accordion] > button.dmwp-section-header').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var section = btn.closest('.dmwp-section');
                var body = section.querySelector('.dmwp-section-body');
                var isExpanded = section.classList.contains('dmwp-section-expanded');

                if (isExpanded) {
                    section.classList.remove('dmwp-section-expanded');
                    btn.setAttribute('aria-expanded', 'false');
                    body.style.display = 'none';
                } else {
                    section.classList.add('dmwp-section-expanded');
                    btn.setAttribute('aria-expanded', 'true');
                    body.style.display = '';
                }
            });
        });
    }

    /**
     * Tabs: switch between menus.
     */
    function initTabs() {
        document.querySelectorAll('.dmwp-tabs-nav').forEach(function (nav) {
            var container = nav.closest('.dmwp-menu-container');
            var buttons = nav.querySelectorAll('.dmwp-tab-btn');
            var panels = container.querySelectorAll('.dmwp-menu[id^="dmwp-tab-"]');

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var targetId = btn.getAttribute('data-dmwp-tab');

                    // Deactivate all
                    buttons.forEach(function (b) {
                        b.classList.remove('dmwp-tab-active');
                        b.setAttribute('aria-selected', 'false');
                    });
                    panels.forEach(function (p) {
                        p.classList.add('dmwp-tab-hidden');
                    });

                    // Activate selected
                    btn.classList.add('dmwp-tab-active');
                    btn.setAttribute('aria-selected', 'true');

                    var target = container.querySelector('#dmwp-tab-' + targetId);
                    if (target) {
                        target.classList.remove('dmwp-tab-hidden');
                    }
                });
            });
        });
    }
})();
