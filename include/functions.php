<?php

function init_share()
{
	wp_enqueue_style('style_share', plugin_dir_url(__FILE__)."style.css");
}

function widgets_share()
{
	register_widget('widget_share');
}

function get_share_options_for_select()
{
	$arr_data = array();

	$arr_data["email_link"] = __("E-mail link", 'lang_share');
	/*if(get_option('setting_share_form') > 0){$arr_data["email_form"] = __("E-mail form", 'lang_share');}*/
	$arr_data["print"] = __("Print", 'lang_share');

	return $arr_data;
}

function get_share_services_for_select()
{
	return array(
		'facebook' => "Facebook",
		'google-plus' => "Google+",
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

function settings_share()
{
	$options_area = __FUNCTION__;

	add_settings_section($options_area, "", $options_area.'_callback', BASE_OPTIONS_PAGE);

	$setting_share_options = get_option('setting_share_options');
	$setting_share_services = get_option('setting_share_services');

	$arr_settings = array();
	$arr_settings['setting_share_options'] = __("Show these options", 'lang_share');

	if(is_array($setting_share_options) && count($setting_share_options) > 0)
	{
		$arr_settings['setting_share_options_visible'] = __("Show these here", 'lang_share');
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

	/*if(is_plugin_active("mf_form/index.php"))
	{
		$arr_settings['setting_share_form'] = __("Form for sharing", 'lang_share');
	}*/

	if(is_array($setting_share_options) && in_array("email_link", $setting_share_options))
	{
		$arr_settings['setting_share_email_subject'] = __("E-mail subject", 'lang_share');
		$arr_settings['setting_share_email_content'] = __("E-mail content", 'lang_share');
	}

	show_settings_fields(array('area' => $options_area, 'settings' => $arr_settings));
}

function settings_share_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);

	echo settings_header($setting_key, __("Share", 'lang_share'));
}

function setting_share_form_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	$obj_form = new mf_form();
	$arr_data = $obj_form->get_form_array();

	echo show_select(array('data' => $arr_data, 'name' => $setting_key, 'value' => $option, 'suffix' => "<a href='".admin_url("admin.php?page=mf_form/create/index.php")."'><i class='fa fa-lg fa-plus'></i></a>"));
}

function setting_share_options_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	echo show_select(array('data' => get_share_options_for_select(), 'name' => $setting_key.'[]', 'value' => $option, 'xtra' => "class='multiselect'"));
}

function setting_share_options_visible_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	echo show_select(array('data' => get_share_place_for_select(), 'name' => $setting_key.'[]', 'value' => $option, 'xtra' => "class='multiselect'", 'description' => __("Can also be displayed by adding the shortcode", 'lang_share')." [mf_share type='options']"));
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

	echo show_select(array('data' => get_share_services_for_select(), 'name' => $setting_key.'[]', 'value' => $option, 'xtra' => "class='multiselect'"));
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

	echo show_select(array('data' => get_share_place_for_select(), 'name' => $setting_key."[]", 'value' => $option, 'xtra' => "class='multiselect'", 'description' => __("Can also be displayed by adding the shortcode", 'lang_share')." [mf_share type='services']"));
}

function setting_share_email_subject_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	echo show_textfield(array('name' => $setting_key, 'value' => $option, 'xtra' => "class='widefat'"));
}

function setting_share_email_content_callback()
{
	$setting_key = get_setting_key(__FUNCTION__);
	$option = get_option($setting_key);

	echo show_wp_editor(array('name' => $setting_key, 'value' => $option,
		'class' => "hide_media_button hide_tabs",
		'mini_toolbar' => true,
		'textarea_rows' => 5,
		'statusbar' => false,
	));
}

function shortcode_share($atts)
{
	extract(shortcode_atts(array(
		'type' => ''
	), $atts));

	return get_share_content(array('type' => $type));
}

function show_share_services($setting_share_services, $url_to_share)
{
	$out = "";

	//$out .= "<li class='share_text'>".__("Share", 'lang_share')."</li>";

	if(in_array("facebook", $setting_share_services))
	{
		$out .= "<li class='social facebook'><a href='//www.facebook.com/sharer/sharer.php?u=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Facebook'><i class='fa fa-facebook'></i></a></li>";
	}

	if(in_array("google-plus", $setting_share_services))
	{
		$out .= "<li class='social google-plus'><a href='//plus.google.com/share?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Google+'><i class='fa fa-google-plus'></i></a></li>";
	}

	if(in_array("linkedin", $setting_share_services))
	{
		$out .= "<li class='social linkedin'><a href='//www.linkedin.com/shareArticle?url=".$url_to_share."&mini=true' target='_blank' title='".__("Share on", 'lang_share')." LinkedIn'><i class='fa fa-linkedin'></i></a></li>";
		//&source=".$url_to_share."&title=Jonathan%20Suh&summary=Short%20summary
	}

	if(in_array("pinterest", $setting_share_services))
	{
		$out .= "<li class='social pinterest'><a href='//www.pinterest.com/pin/create/button/?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Pinterest'><i class='fa fa-pinterest'></i></a></li>";
		//&media=https%3A%2F%2Fjonsuh.com%2Ficon.png&description=Short%20description&hashtags=web,development
	}

	if(in_array("reddit", $setting_share_services))
	{
		$out .= "<li class='social reddit'><a href='//www.reddit.com/submit/?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Reddit'><i class='fa fa-reddit'></i></a></li>";
	}

	if(in_array("twitter", $setting_share_services))
	{
		$setting_share_twitter = get_option('setting_share_twitter');

		$out .= "<li class='social twitter'>
			<a href='//twitter.com/intent/tweet?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Twitter'>
				<i class='fa fa-twitter'></i>";

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

function get_share_content($data = array())
{
	if(!isset($data['type'])){	$data['type'] = "";}
	if(!isset($data['url'])){	$data['url'] = "";}

	$setting_share_options = get_option('setting_share_options');
	$setting_share_services = get_option('setting_share_services');

	$count_options = count($setting_share_options);
	$count_services = count($setting_share_services);

	$out = "";

	if($count_options > 0 || $count_services > 0)
	{
		$url_to_share = $data['url'] != '' ? urlencode($data['url']) : urlencode(get_site_url().$_SERVER['REQUEST_URI']);

		$out .= "<ul class='mf_share'>";

			if(($data['type'] == "" || $data['type'] == "options") && is_array($setting_share_options) && $count_options > 0)
			{
				if(in_array("email_link", $setting_share_options) || in_array("email_form", $setting_share_options))
				{
					$setting_share_email_subject = get_option('setting_share_email_subject');
					$setting_share_email_content = get_option('setting_share_email_content');

					//$out .= "<li class='share_text'>".__("Recommend to a friend", 'lang_share')."</li>";

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

						$out .= "<li class='contact email_link'><a href='mailto:".$link_extra."' title='".__("Recommend this page to a friend", 'lang_share')."'><i class='fa fa-envelope'></i></a></li>";
					}

					if(in_array("email_form", $setting_share_options))
					{
						$form_url = get_form_url(get_option('setting_share_form'));

						if($form_url != '')
						{
							$out .= "<li class='contact email_form'><a href='".$form_url."' title='".__("Recommend this page to a friend", 'lang_share')."'><i class='fa fa-envelope'></i></a></li>";
						}
					}
				}

				if(in_array("print", $setting_share_options))
				{
					//$out .= "<li class='share_text'>".__("Print", 'lang_share')."</li>";
					$out .= "<li class='contact print'><a href='#' onclick='window.print()' title='".__("Print this page", 'lang_share')."'><i class='fa fa-print'></i></a></li>";
				}
			}

			if(($data['type'] == "" || $data['type'] == "services") && is_array($setting_share_services) && $count_services > 0)
			{
				$out .= show_share_services($setting_share_services, $url_to_share);
			}

		$out .= "</ul>";
	}

	return $out;
}

function is_correct_page()
{
	$pages = get_option('setting_share_pages');

	return $pages == '' || count($pages) == 0 || is_array($pages) && eval("return (".implode(" || ", $pages).");");
}

function content_share($html)
{
	$option = get_option('setting_share_options_visible');

	if(is_array($option) && count($option) > 0)
	{
		$html_addon = get_share_content(array('type' => 'options'));

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
		if(is_correct_page())
		{
			$html_addon = get_share_content(array('type' => 'services'));

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

function content_meta_share($html, $post)
{
	if($post->post_type == 'post')
	{
		$option = get_option('setting_share_options_visible');

		if(is_array($option) && count($option) > 0 && in_array('after_post_heading', $option))
		{
			$html .= get_share_content(array('type' => 'options', 'url' => get_permalink($post)));
		}

		$option = get_option('setting_share_visible');

		if(is_array($option) && count($option) > 0 && in_array('after_post_heading', $option))
		{
			if(is_correct_page())
			{
				$html .= get_share_content(array('type' => 'services', 'url' => get_permalink($post)));
			}
		}
	}

    return $html;
}

function footer_share()
{
	$option = get_option('setting_share_options_visible');

	if(is_array($option) && count($option) > 0 && in_array("end_of_page", $option))
	{
		$html_addon = get_share_content(array('type' => 'options'));

		echo $html_addon;
	}

	$option = get_option('setting_share_visible');

	if(is_array($option) && count($option) > 0 && in_array("end_of_page", $option))
	{
		$html_addon = get_share_content(array('type' => 'services'));

		echo $html_addon;
	}
}