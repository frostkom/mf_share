<?php

class widget_share extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array(
			'classname' => 'mf_share',
			'description' => __("Display Social Buttons", 'lang_share')
		);

		$control_ops = array('id_base' => 'share-widget');

		parent::__construct('share-widget', __("Share", 'lang_share'), $widget_ops, $control_ops);
	}

	function widget($args, $instance)
	{
		global $wpdb;

		extract($args);

		if(count($instance['share_services']) > 0)
		{
			$url_to_share = urlencode(get_site_url().$_SERVER['REQUEST_URI']);

			echo $before_widget
				."<ul>"
					.show_share_services($instance['share_services'], $url_to_share)
				."</ul>"
			.$after_widget;
		}
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['share_services'] = $new_instance['share_services'];

		return $instance;
	}

	function form($instance)
	{
		global $wpdb;

		$defaults = array(
			'share_services' => array(),
		);
		$instance = wp_parse_args((array)$instance, $defaults);

		echo "<p>"
			.show_select(array('data' => get_share_services_for_select(), 'name' => $this->get_field_name('share_services')."[]", 'value' => $instance['share_services'], 'xtra' => "class='widefat'"))
		."</p>";
	}
}