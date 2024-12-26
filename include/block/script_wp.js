(function()
{
	var __ = wp.i18n.__,
		el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl;

	registerBlockType('mf/share',
	{
		title: __("Share", 'lang_share'),
		description: __("Display Social Buttons", 'lang_share'),
		icon: 'share',
		category: 'widgets',
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'':
			{
                'type': 'array',
                'default': ''
            }
		},
		'supports':
		{
			'html': false,
			'multiple': false,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			},
			"__experimentalBorder":
			{
				"radius": true
			}
		},
		edit: function(props)
		{
			var arr_out = [];

			/* Select */
			/* ################### */
			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					SelectControl,
					{
						label: __("Social Buttons", 'lang_share'),
						value: props.attributes.share_services,
						options: convert_php_array_to_block_js(script_share_block_wp.share_services),
						multiple: true,
						onChange: function(value)
						{
							props.setAttributes({share_services: value});
						}
					}
				)
			));
			/* ################### */

			return arr_out;
		},
		save: function()
		{
			return null;
		}
	});
})();