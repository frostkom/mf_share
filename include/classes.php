<?php

class mf_share
{
	function __construct(){}

	function is_correct_page()
	{
		$pages = get_option('setting_share_pages');

		return ($pages == '' || count($pages) == 0 || is_array($pages) && eval("return (".implode(" || ", $pages).");"));
	}

	function get_share_options_for_select()
	{
		$arr_data = array();

		$arr_data['email_link'] = __("E-mail link", 'lang_share');
		$arr_data['print'] = __("Print", 'lang_share');

		return $arr_data;
	}

	function get_share_services_for_select()
	{
		return array(
			'facebook' => "Facebook",
			'linkedin' => "LinkedIn",
			'pinterest' => "Pinterest",
			'reddit' => "Reddit",
			'twitter' => "Twitter",
		);
	}

	function get_share_place_for_select()
	{
		return array(
			'above_content' => __("Above Content", 'lang_share'),
			'after_post_heading' => __("After Heading", 'lang_share'),
			'below_content' => __("Below Content", 'lang_share'),
			'end_of_page' => __("In Footer", 'lang_share'),
		);
	}

	function get_share_content($data = array())
	{
		if(!isset($data['type'])){	$data['type'] = "";}
		if(!isset($data['url'])){	$data['url'] = "";}

		$setting_share_options = get_option_or_default('setting_share_options', array());
		$setting_share_services = get_option_or_default('setting_share_services', array());

		$count_options = count($setting_share_options);
		$count_services = count($setting_share_services);

		$out = "";

		if($count_options > 0 || $count_services > 0)
		{
			$url_to_share = $data['url'] != '' ? urlencode($data['url']) : urlencode(get_site_url().$_SERVER['REQUEST_URI']);

			$out .= "<ul class='mf_share'>";

				if(($data['type'] == "" || $data['type'] == "options") && is_array($setting_share_options) && $count_options > 0)
				{
					$setting_share_options_titles = get_option('setting_share_options_titles');

					if(in_array("email_link", $setting_share_options) || in_array("email_form", $setting_share_options))
					{
						$setting_share_email_subject = get_option('setting_share_email_subject');
						$setting_share_email_content = get_option('setting_share_email_content');

						if(in_array("email_link", $setting_share_options))
						{
							$link_extra = "";

							if($setting_share_email_subject != '')
							{
								$link_extra .= ($link_extra != '' ? "&" : "?")."subject=".$setting_share_email_subject;
							}

							if($setting_share_email_content != '')
							{
								$link_extra .= ($link_extra != '' ? "&" : "?")."body=".str_replace("[url]", $url_to_share, $setting_share_email_content);
							}

							$out .= "<li class='contact email_link'><a href='mailto:".$link_extra."' title='".__("Recommend this page to a friend", 'lang_share')."'>".($setting_share_options_titles == 'yes' ? "<span>".__("Recommend", 'lang_share')."</span>" : "")."<i class='fa fa-envelope'></i></a></li>";
						}
					}

					if(in_array("print", $setting_share_options))
					{
						$out .= "<li class='contact print'><a href='#' onclick='window.print()' title='".__("Print this page", 'lang_share')."'>".($setting_share_options_titles == 'yes' ? "<span>".__("Print", 'lang_share')."</span>" : "")."<i class='fa fa-print'></i></a></li>";
					}
				}

				if(($data['type'] == "" || $data['type'] == "services") && is_array($setting_share_services) && $count_services > 0)
				{
					$out .= $this->show_share_services($setting_share_services, $url_to_share);
				}

			$out .= "</ul>";
		}

		return $out;
	}

	function show_share_services($setting_share_services, $url_to_share)
	{
		$out = "";

		if(in_array('facebook', $setting_share_services))
		{
			$out .= "<li class='social facebook'><a href='//facebook.com/sharer/sharer.php?u=".$url_to_share."' title='".__("Share on", 'lang_share')." Facebook'><i class='fab fa-facebook'></i></a></li>";
		}

		if(in_array('linkedin', $setting_share_services))
		{
			$out .= "<li class='social linkedin'><a href='//linkedin.com/shareArticle?url=".$url_to_share."&mini=true' title='".__("Share on", 'lang_share')." LinkedIn'><i class='fab fa-linkedin-in'></i></a></li>";
			//&source=".$url_to_share."&title=Jonathan%20Suh&summary=Short%20summary
		}

		if(in_array('pinterest', $setting_share_services))
		{
			$out .= "<li class='social pinterest'><a href='//pinterest.com/pin/create/button/?url=".$url_to_share."' title='".__("Share on", 'lang_share')." Pinterest'><i class='fab fa-pinterest'></i></a></li>";
			//&media=https%3A%2F%2Fjonsuh.com%2Ficon.png&description=Short%20description&hashtags=web,development
		}

		if(in_array('reddit', $setting_share_services))
		{
			$out .= "<li class='social reddit'><a href='//reddit.com/submit/?url=".$url_to_share."' title='".__("Share on", 'lang_share')." Reddit'><i class='fab fa-reddit'></i></a></li>";
		}

		if(in_array('twitter', $setting_share_services))
		{
			$setting_share_twitter = get_option('setting_share_twitter');

			$out .= "<li class='social twitter'>
				<a href='//twitter.com/intent/tweet?url=".$url_to_share."' title='".__("Share on", 'lang_share')." Twitter'>
					<i class='fab fa-twitter'></i>";

					if($setting_share_twitter != '')
					{
						$out .= "<span>".$setting_share_twitter."</span>";
					}

				$out .= "</a>
			</li>";
			//&text=TWEET_TO_SHARE&via=USERNAME_TO_SHARE&hashtags=web,development
		}

		return $out;
	}

	function block_render_callback($attributes)
	{
		if(!isset($attributes['share_services'])){			$attributes['share_services'] = array();}

		$out = "";

		if(count($attributes['share_services']) > 0)
		{
			$url_to_share = urlencode(get_site_url().$_SERVER['REQUEST_URI']);

			echo "<div".parse_block_attributes(array('class' => "widget mf_share", 'attributes' => $attributes)).">
				<ul>"
					.$this->show_share_services($attributes['share_services'], $url_to_share)
				."</ul>
			</div>";
		}

		return $out;
	}

	function init()
	{
		load_plugin_textdomain('lang_share', false, str_replace("/include", "", dirname(plugin_basename(__FILE__)))."/lang/");

		// Blocks
		#######################
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_share_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version, true);

		wp_localize_script('script_share_block_wp', 'script_share_block_wp', array(
			'block_title' => __("Share", 'lang_share'),
			'block_description' => __("Display Social Buttons", 'lang_share'),
			'share_services_label' => __("Social Buttons", 'lang_share'),
			'share_services' => $this->get_share_services_for_select(),
		));

		register_block_type('mf/share', array(
			'editor_script' => 'script_share_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_callback'),
			//'style' => 'style_base_block_wp',
		));
		#######################
	}

	function settings_share()
	{
		$options_area = __FUNCTION__;

		add_settings_section($options_area, "", array($this, $options_area.'_callback'), BASE_OPTIONS_PAGE);

		$setting_share_options = get_option('setting_share_options');
		$setting_share_services = get_option('setting_share_services');

		$arr_settings = array();
		$arr_settings['setting_share_options'] = __("Show these options", 'lang_share');

		if(is_array($setting_share_options) && count($setting_share_options) > 0)
		{
			$arr_settings['setting_share_options_visible'] = __("Show these here", 'lang_share');
			$arr_settings['setting_share_options_titles'] = __("Show titles", 'lang_share');
		}

		$arr_settings['setting_share_services'] = __("Share on", 'lang_share');

		if(is_array($setting_share_services) && in_array("twitter", $setting_share_services))
		{
			$arr_settings['setting_share_twitter'] = __("Twitter handle", 'lang_share');
		}

		if(is_array($setting_share_services) && count($setting_share_services) > 0)
		{
			$arr_settings['setting_share_visible'] = __("Show these here", 'lang_share');

			$setting_share_visible = get_option('setting_share_visible');

			if(is_array($setting_share_visible) && (in_array('above_content', $setting_share_visible) || in_array('after_post_heading', $setting_share_visible) || in_array('below_content', $setting_share_visible)))
			{
				$arr_settings['setting_share_pages'] = __("Only on these pages", 'lang_share');
			}
		}

		if(is_array($setting_share_options) && in_array("email_link", $setting_share_options))
		{
			$arr_settings['setting_share_email_subject'] = __("E-mail Subject", 'lang_share');
			$arr_settings['setting_share_email_content'] = __("E-mail Content", 'lang_share');
		}

		show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
	}

	function settings_share_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Share", 'lang_share'));
	}

	function setting_share_options_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_select(array('data' => $this->get_share_options_for_select(), 'name' => $setting_key.'[]', 'value' => $option, 'xtra' => "class='multiselect'"));
	}

	function setting_share_options_visible_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_select(array('data' => $this->get_share_place_for_select(), 'name' => $setting_key.'[]', 'value' => $option, 'xtra' => "class='multiselect'", 'description' => __("Can also be displayed by adding the shortcode", 'lang_share')." [mf_share type='options']"));
	}

	function setting_share_options_titles_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key, 'no');

		echo show_select(array('data' => get_yes_no_for_select(), 'name' => $setting_key, 'value' => $option));
	}

	function setting_share_pages_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_select(array('data' => get_post_types_for_select(), 'name' => $setting_key.'[]', 'value' => $option, 'xtra' => "class='multiselect'"));
	}

	function setting_share_services_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_select(array('data' => $this->get_share_services_for_select(), 'name' => $setting_key.'[]', 'value' => $option, 'xtra' => "class='multiselect'"));
	}

	function setting_share_twitter_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('name' => $setting_key, 'value' => $option, 'placeholder' => "@twitter"));
	}

	function setting_share_visible_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_select(array('data' => $this->get_share_place_for_select(), 'name' => $setting_key."[]", 'value' => $option, 'xtra' => "class='multiselect'", 'description' => __("Can also be displayed by adding the shortcode", 'lang_share')." [mf_share type='services']"));
	}

	function setting_share_email_subject_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_textfield(array('name' => $setting_key, 'value' => $option));
	}

	function setting_share_email_content_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option($setting_key);

		echo show_wp_editor(array('name' => $setting_key, 'value' => $option,
			'class' => "hide_media_button hide_tabs",
			'mini_toolbar' => true,
			'editor_height' => 100,
			//'statusbar' => false,
			'description' => sprintf(__("Use %s to automatically add the current page URL into the text", 'lang_share'), "[url]"),
		));
	}

	function language_attributes($html)
	{
		if($this->is_correct_page())
		{
			$html .= " xmlns:og='http://opengraphprotocol.org/schema/' xmlns:fb='http://www.facebook.com/2008/fbml'";
		}

		return $html;
	}

	function wp_head()
	{
		if($this->is_correct_page() || apply_filters('get_block_search', 0, 'mf/share') > 0 || (int)apply_filters('get_widget_search', 'share-widget') > 0)
		{
			$plugin_include_url = plugin_dir_url(__FILE__);

			mf_enqueue_style('style_share', $plugin_include_url."style.css");
		}
	}

	function wp_footer()
	{
		$option = get_option('setting_share_options_visible');

		if(is_array($option) && count($option) > 0 && in_array("end_of_page", $option))
		{
			$html_addon = $this->get_share_content(array('type' => 'options'));

			echo $html_addon;
		}

		$option = get_option('setting_share_visible');

		if(is_array($option) && count($option) > 0 && in_array("end_of_page", $option))
		{
			$html_addon = $this->get_share_content(array('type' => 'services'));

			echo $html_addon;
		}
	}

	function the_content($html)
	{
		if($this->is_correct_page())
		{
			$option = get_option('setting_share_options_visible');

			if(is_array($option) && count($option) > 0)
			{
				$html_addon = $this->get_share_content(array('type' => 'options'));

				if(in_array('above_content', $option))
				{
					$html = $html_addon.$html;
				}

				if(in_array('below_content', $option))
				{
					$html .= $html_addon;
				}
			}

			$option = get_option('setting_share_visible');

			if(is_array($option) && count($option) > 0)
			{
				$html_addon = $this->get_share_content(array('type' => 'services'));

				if(in_array('above_content', $option))
				{
					$html = $html_addon.$html;
				}

				if(in_array('below_content', $option))
				{
					$html .= $html_addon;
				}
			}
		}

		return $html;
	}

	function the_content_meta($html, $post)
	{
		if($this->is_correct_page())
		{
			$option = get_option('setting_share_options_visible');

			if(is_array($option) && count($option) > 0 && in_array('after_post_heading', $option))
			{
				$html .= $this->get_share_content(array('type' => 'options', 'url' => get_permalink($post)));
			}

			$option = get_option('setting_share_visible');

			if(is_array($option) && count($option) > 0 && in_array('after_post_heading', $option))
			{
				$html .= $this->get_share_content(array('type' => 'services', 'url' => get_permalink($post)));
			}
		}

		return $html;
	}

	function shortcode_share($atts)
	{
		global $post;

		$out = "";

		/*extract(shortcode_atts(array(
			'type' => ''
		), $atts));

		$out = $this->get_share_content(array('type' => $type));*/

		//do_log(__FUNCTION__.": Add a block instead (#".$post->ID.", ".var_export($atts, true).")", 'publish', false);

		return $out;
	}

	function widgets_init()
	{
		if(wp_is_block_theme() == false)
		{
			register_widget('widget_share');
		}
	}
}

class widget_share extends WP_Widget
{
	var $obj_share;
	var $widget_ops;
	var $arr_default = array(
		'share_services' => array(),
	);

	function __construct()
	{
		$this->obj_share = new mf_share();

		$this->widget_ops = array(
			'classname' => 'mf_share',
			'description' => __("Display Social Buttons", 'lang_share'),
		);

		parent::__construct('share-widget', __("Share", 'lang_share'), $this->widget_ops);
	}

	function widget($args, $instance)
	{
		do_log(__CLASS__."->".__FUNCTION__."(): Add a block instead", 'publish', false);

		extract($args);
		$instance = wp_parse_args((array)$instance, $this->arr_default);

		if(count($instance['share_services']) > 0)
		{
			$url_to_share = urlencode(get_site_url().$_SERVER['REQUEST_URI']);

			echo apply_filters('filter_before_widget', $before_widget)
				."<ul>"
					.$this->obj_share->show_share_services($instance['share_services'], $url_to_share)
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
			.show_select(array('data' => $this->obj_share->get_share_services_for_select(), 'name' => $this->get_field_name('share_services')."[]", 'value' => $instance['share_services']))
		."</div>";
	}
}