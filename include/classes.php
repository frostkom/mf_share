<?php

class widget_share extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array(
			'classname' => 'mf_share',
			'description' => __("Display Social Buttons", 'lang_share')
		);

		$this->arr_default = array(
			'share_services' => array(),
		);

		parent::__construct('share-widget', __("Share", 'lang_share'), $widget_ops);
	}

	function widget($args, $instance)
	{
		extract($args);

		$instance = wp_parse_args((array)$instance, $this->arr_default);

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

		$new_instance = wp_parse_args((array)$new_instance, $this->arr_default);

		$instance['share_services'] = is_array($new_instance['share_services']) ? $new_instance['share_services'] : array();

		return $instance;
	}

	function form($instance)
	{
		$instance = wp_parse_args((array)$instance, $this->arr_default);

		echo "<div class='mf_form'>"
			.show_select(array('data' => get_share_services_for_select(), 'name' => $this->get_field_name('share_services')."[]", 'value' => $instance['share_services']))
		."</div>";
	}
}