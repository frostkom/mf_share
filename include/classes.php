<?php

class mf_share
{
	function __construct()
	{

	}

	function language_attributes($html)
	{
		if(is_correct_page())
		{
			$html .= " xmlns:og='http://opengraphprotocol.org/schema/' xmlns:fb='http://www.facebook.com/2008/fbml'";
		}

		return $html;
	}

	function wp_head()
	{
		global $post;

		if(is_correct_page())
		{
			mf_enqueue_style('style_share', plugin_dir_url(__FILE__)."style.css", get_plugin_version(__FILE__));

			echo "<meta property='og:site_name' content='".get_bloginfo('name')."'>";

			if(isset($post->ID))
			{
				echo "<meta property='og:title' content='".$post->post_title."'>
				<meta property='og:url' content='".get_permalink($post)."'>";

				if(has_post_thumbnail($post->ID))
				{
					echo "<meta property='og:image' content='".get_the_post_thumbnail_url($post->ID, 'thumbnail')."'>";
				}

				if(isset($post->post_excerpt) && $post->post_excerpt != '')
				{
					echo "<meta property='og:description' content='".$post->post_excerpt."'>";
				}

				//echo "<meta property='og:type' content='article'>";
			}
		}
	}

	function the_content_meta($html, $post)
	{
		if(is_correct_page())
		{
			$option = get_option('setting_share_options_visible');

			if(is_array($option) && count($option) > 0 && in_array('after_post_heading', $option))
			{
				$html .= get_share_content(array('type' => 'options', 'url' => get_permalink($post)));
			}

			$option = get_option('setting_share_visible');

			if(is_array($option) && count($option) > 0 && in_array('after_post_heading', $option))
			{
				$html .= get_share_content(array('type' => 'services', 'url' => get_permalink($post)));
			}
		}

		return $html;
	}
}

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