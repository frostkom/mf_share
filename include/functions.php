<?php

function init_share()
{
	wp_enqueue_style('style_share', plugins_url()."/mf_share/include/style.css");
}

function settings_share()
{
	$options_page = "settings_mf_base";
	$options_area = "settings_share";

	add_settings_section(
		$options_area,
		__("Share", 'lang_webshop'),
		$options_area.'_callback',
		$options_page
	);

	$setting_share_options = get_option('setting_share_options');
	$setting_share_services = get_option('setting_share_services');

	$arr_settings = array();

	$arr_settings["setting_share_options"] = __("Show these options", 'lang_share');
	$arr_settings["setting_share_options_visible"] = __("Show these here", 'lang_share');
	$arr_settings["setting_share_services"] = __("Share on", 'lang_share');

	if(is_array($setting_share_services) && in_array("twitter", $setting_share_services))
	{
		$arr_settings["setting_share_twitter"] = __("Twitter handle", 'lang_share');
	}

	if(is_array($setting_share_services) && count($setting_share_services) > 0)
	{
		$arr_settings["setting_share_visible"] = __("Show these here", 'lang_share');
	}

	/*if(is_plugin_active('mf_form/index.php'))
	{
		$arr_settings["setting_share_form"] = __("Form for sharing", 'lang_share');
	}*/

	if(is_array($setting_share_options) && in_array("email_link", $setting_share_options))
	{
		$arr_settings["setting_share_email_subject"] = __("E-mail subject", 'lang_share');
		$arr_settings["setting_share_email_content"] = __("E-mail content", 'lang_share');
	}

	foreach($arr_settings as $handle => $text)
	{
		add_settings_field($handle, $text, $handle."_callback", $options_page, $options_area);

		register_setting($options_page, $handle);
	}
}

function settings_share_callback(){}

function setting_share_form_callback()
{
	global $wpdb;

	$is_super_admin = current_user_can('update_core');

	$option = get_option('setting_share_form');

	echo "<label>
		<select name='setting_share_form'>
			<option value=''>-- ".__("Choose here", 'lang_share')." --</option>";

			$result = $wpdb->get_results("SELECT queryID, queryName FROM ".$wpdb->base_prefix."query WHERE queryDeleted = '0'".($is_super_admin ? "" : " AND (blogID = '".$wpdb->blogid."' OR blogID IS null)")." ORDER BY queryCreated DESC");

			foreach($result as $r)
			{
				$result = get_page_from_form($r->queryID);

				if(count($result) > 0)
				{
					echo "<option value='".$r->queryID."'".($option == $r->queryID ? " selected" : "").">".$r->queryName."</option>";
				}
			}

		echo "</select>
	</label>";
}

function setting_share_options_callback()
{
	$arr_data = array();

	$arr_data[] = array("email_link", __("E-mail link", 'lang_share'));

	/*if(get_option('setting_share_form') > 0)
	{
		$arr_data[] = array("email_form", __("E-mail form", 'lang_share'));
	}*/

	$arr_data[] = array("print", __("Print", 'lang_share'));

	$option = get_option('setting_share_options');

	echo "<label>"
		.show_select(array('data' => $arr_data, 'name' => 'setting_share_options[]', 'compare' => $option))
	."</label>";
}

function setting_share_options_visible_callback()
{
	$option = get_option('setting_share_options_visible');

	$arr_data = array();

	$arr_data[] = array("above_content", __("Above page content", 'lang_share'));
	$arr_data[] = array("below_content", __("Below page content", 'lang_share'));
	$arr_data[] = array("end_of_page", __("End of page", 'lang_share'));

	echo "<label>"
		.show_select(array('data' => $arr_data, 'name' => 'setting_share_options_visible[]', 'compare' => $option))
		."<span class='description'>".__("Can also be displayed by adding the shortcode", 'lang_share')." [mf_share type='options']</span>"
	."</label>";
}

function setting_share_services_callback()
{
	$option = get_option('setting_share_services');

	$arr_data = array();

	$arr_data[] = array("facebook", "Facebook");
	$arr_data[] = array("google-plus", "Google+");
	$arr_data[] = array("linkedin", "LinkedIn");
	$arr_data[] = array("pinterest", "Pinterest");
	$arr_data[] = array("reddit", "Reddit");
	$arr_data[] = array("twitter", "Twitter");

	echo "<label>"
		.show_select(array('data' => $arr_data, 'name' => 'setting_share_services[]', 'compare' => $option))
	."</label>";
}

function setting_share_twitter_callback()
{
	$option = get_option('setting_share_twitter');

	echo "<label>"
		.show_textfield(array('name' => 'setting_share_twitter', 'value' => $option, 'placeholder' => "@twitter"))
	."</label>";
}

function setting_share_visible_callback()
{
	$option = get_option('setting_share_visible');

	$arr_data = array();

	$arr_data[] = array("above_content", __("Above page content", 'lang_share'));
	$arr_data[] = array("below_content", __("Below page content", 'lang_share'));
	$arr_data[] = array("end_of_page", __("End of page", 'lang_share'));

	echo "<label>"
		.show_select(array('data' => $arr_data, 'name' => 'setting_share_visible[]', 'compare' => $option))
			."<span class='description'>".__("Can also be displayed by adding the shortcode", 'lang_share')." [mf_share type='services']</span>"
	."</label>";
}

function setting_share_email_subject_callback()
{
	$option = get_option('setting_share_email_subject');

	echo "<label>
		<input type='text' name='setting_share_email_subject' value='".$option."' class='widefat'>
	</label>";
}

function setting_share_email_content_callback()
{
	$option = get_option('setting_share_email_content');

	echo "<label>";

		wp_editor($option, "setting_share_email_content");

		//<input type='text' name='setting_share_email_content' value='".$option."' class='widefat'>

	echo "</label>";
}

function get_share_content($type = "")
{
	$setting_share_options = get_option('setting_share_options');
	$setting_share_services = get_option('setting_share_services');

	$count_options = count($setting_share_options);
	$count_services = count($setting_share_services);

	$out = "";

	if($count_options > 0 || $count_services > 0)
	{
		$url_to_share = urlencode(get_site_url().$_SERVER['REQUEST_URI']);

		$out .= "<ul class='mf_share'>";

			if(($type == "" || $type == "options") && is_array($setting_share_options) && $count_options > 0)
			{
				if(in_array("email_link", $setting_share_options) || in_array("email_form", $setting_share_options))
				{
					$setting_share_email_subject = get_option('setting_share_email_subject');
					$setting_share_email_content = get_option('setting_share_email_content');

					$out .= "<li class='share_text'>".__("Recommend to a friend", 'lang_share')."</li>";
				
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

						$out .= "<li class='email_link'><a href='mailto:".$link_extra."' title='".__("Recommend this page to a friend", 'lang_share')."'><i class='fa fa-envelope'></i></a></li>";
					}

					if(in_array("email_form", $setting_share_options))
					{
						$form_url = get_form_url(get_option('setting_share_form'));

						if($form_url != '')
						{
							$out .= "<li class='email_form'><a href='".$form_url."' title='".__("Recommend this page to a friend", 'lang_share')."'><i class='fa fa-envelope'></i></a></li>";
						}
					}
				}

				if(in_array("print", $setting_share_options))
				{
					$out .= "<li class='share_text'>".__("Print", 'lang_share')."</li>";
					$out .= "<li class='print'><a href='#' onclick='window.print()' title='".__("Print this page", 'lang_share')."'><i class='fa fa-print'></i></a></li>";
				}
			}

			if(($type == "" || $type == "services") && is_array($setting_share_services) && $count_services > 0)
			{
				$out .= "<li class='share_text'>".__("Share", 'lang_share')."</li>";

				if(in_array("facebook", $setting_share_services))
				{
					$out .= "<li class='facebook'><a href='//www.facebook.com/sharer/sharer.php?u=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Facebook'><i class='fa fa-facebook'></i></a></li>";
				}

				if(in_array("google-plus", $setting_share_services))
				{
					$out .= "<li class='google-plus'><a href='//plus.google.com/share?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Google+'><i class='fa fa-google-plus'></i></a></li>";
				}

				if(in_array("linkedin", $setting_share_services))
				{
					$out .= "<li class='linkedin'><a href='//www.linkedin.com/shareArticle?url=".$url_to_share."&mini=true' target='_blank' title='".__("Share on", 'lang_share')." LinkedIn'><i class='fa fa-linkedin'></i></a></li>";
					//&source=".$url_to_share."
					//&title=Jonathan%20Suh
					//&summary=Short%20summary
				}

				if(in_array("pinterest", $setting_share_services))
				{
					$out .= "<li class='pinterest'><a href='//www.pinterest.com/pin/create/button/?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Pinterest'><i class='fa fa-pinterest'></i></a></li>";
					//&media=https%3A%2F%2Fjonsuh.com%2Ficon.png
					//&description=Short%20description
					//&hashtags=web,development
				}

				if(in_array("reddit", $setting_share_services))
				{
					$out .= "<li class='reddit'><a href='//www.reddit.com/submit/?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Reddit'><i class='fa fa-reddit'></i></a></li>";
				}

				if(in_array("twitter", $setting_share_services))
				{
					$setting_share_twitter = get_option('setting_share_twitter');

					$out .= "<li class='twitter'>
						<a href='//twitter.com/intent/tweet?url=".$url_to_share."' target='_blank' title='".__("Share on", 'lang_share')." Twitter'>
							<i class='fa fa-twitter'></i>";

							if($setting_share_twitter != '')
							{
								$out .= "<span>".$setting_share_twitter."</span>";
							}

						$out .= "</a>
					</li>";
					//&text=TWEET_TO_SHARE
					//&via=USERNAME_TO_SHARE
					//&hashtags=web,development
				}
			}

		$out .= "</ul>";
	}

	return $out;
}

function content_share($html)
{
	$option = get_option('setting_share_options_visible');

	if(is_array($option) && count($option) > 0)
	{
		$html_addon = get_share_content('options');

		if(in_array("above_content", $option))
		{
			$html = $html_addon.$html;
		}

		if(in_array("below_content", $option))
		{
			$html .= $html_addon;
		}
	}

	$option = get_option('setting_share_visible');

	if(is_array($option) && count($option) > 0)
	{
		$html_addon = get_share_content('services');

		if(in_array("above_content", $option))
		{
			$html = $html_addon.$html;
		}

		if(in_array("below_content", $option))
		{
			$html .= $html_addon;
		}
	}

	return $html;
}

function footer_share()
{
	$option = get_option('setting_share_options_visible');

	if(is_array($option) && count($option) > 0)
	{
		$html_addon = get_share_content('options');

		if(in_array("end_of_page", $option))
		{
			echo $html_addon;
		}
	}

	$option = get_option('setting_share_visible');

	if(is_array($option) && count($option) > 0)
	{
		$html_addon = get_share_content('services');

		if(in_array("end_of_page", $option))
		{
			echo $html_addon;
		}
	}
}