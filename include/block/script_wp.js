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
		icon: 'share', /* https://developer.wordpress.org/resource/dashicons/ */
		category: 'widgets', /* common, formatting, layout, widgets, embed */
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
			}
		},
		edit: function(props)
		{
			var arr_out = [];

			/* Select */
			/* ################### */
			var arr_options = [];

			jQuery.each(script_share_block_wp.share_services, function(index, value)
			{
				if(index == "")
				{
					index = 0;
				}

				arr_options.push({label: value, value: index});
			});

			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					SelectControl,
					{
						label: __("Social Buttons", 'lang_share'),
						value: props.attributes.share_services,
						options: arr_options,
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