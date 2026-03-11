/**
 * Digital Menu Gutenberg block — editor entry point.
 */
(function (blocks, element, blockEditor, components) {
    var el = element.createElement;
    var InspectorControls = blockEditor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;

    blocks.registerBlockType('dmwp/digital-menu', {
        edit: function (props) {
            var attributes = props.attributes;

            return el(
                element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: 'Menu Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Menu IDs',
                            help: 'Comma-separated menu IDs. Leave empty for all menus.',
                            value: attributes.menuIds,
                            onChange: function (val) {
                                props.setAttributes({ menuIds: val });
                            },
                        }),
                        el(SelectControl, {
                            label: 'Layout',
                            value: attributes.layout,
                            options: [
                                { label: 'Default (from settings)', value: '' },
                                { label: 'Accordion', value: 'accordion' },
                                { label: 'Tabs', value: 'tabs' },
                                { label: 'List', value: 'list' },
                            ],
                            onChange: function (val) {
                                props.setAttributes({ layout: val });
                            },
                        }),
                        el(SelectControl, {
                            label: 'Language',
                            value: attributes.language,
                            options: [
                                { label: 'Default (from settings)', value: '' },
                                { label: 'Italiano', value: 'it' },
                                { label: 'English', value: 'en' },
                                { label: 'Deutsch', value: 'de' },
                                { label: 'Français', value: 'fr' },
                                { label: 'Español', value: 'es' },
                            ],
                            onChange: function (val) {
                                props.setAttributes({ language: val });
                            },
                        })
                    )
                ),
                el(
                    'div',
                    { className: 'dmwp-block-placeholder' },
                    el('div', {
                        style: {
                            padding: '2em',
                            textAlign: 'center',
                            backgroundColor: '#f0fdf4',
                            border: '2px dashed #059669',
                            borderRadius: '8px',
                            color: '#059669',
                            fontSize: '1.1em',
                            fontWeight: 600,
                        },
                    },
                        '🍽 Digital Menu',
                        el('p', {
                            style: { fontSize: '0.8em', fontWeight: 400, color: '#6b7280', marginTop: '0.5em' },
                        }, attributes.menuIds ? 'Menu: ' + attributes.menuIds : 'All menus')
                    )
                )
            );
        },

        save: function () {
            // Server-side rendering
            return null;
        },
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
);
