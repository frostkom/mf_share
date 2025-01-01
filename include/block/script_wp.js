(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl;

	registerBlockType('mf/share',
	{
		title: script_share_block_wp.block_title,
		description: script_share_block_wp.block_description,
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
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						el(
							SelectControl,
							{
								label: script_share_block_wp.share_services_label,
								value: props.attributes.share_services,
								options: convert_php_array_to_block_js(script_share_block_wp.share_services),
								multiple: true,
								onChange: function(value)
								{
									props.setAttributes({share_services: value});
								}
							}
						)
					),
					el(
						'strong',
						{className: props.className},
						script_share_block_wp.block_title
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});
})();