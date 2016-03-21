<?php
/*
Plugin Name: MF Share
Plugin URI: http://github.com/frostkom/mf_share
Description: 
Version: 1.3.1
Author: Martin Fors
Author URI: http://frostkom.se
*/

include_once("include/functions.php");

if(is_admin())
{
	register_uninstall_hook(__FILE__, 'uninstall_share');

	add_action('admin_init', 'settings_share');
}

else
{
	add_action('init', 'init_share');
	add_action('wp_footer', 'footer_share');
	add_filter('the_content', 'content_share');
	add_shortcode('mf_share', 'shortcode_share');
}

load_plugin_textdomain('lang_share', false, dirname(plugin_basename(__FILE__)).'/lang/');

function uninstall_share()
{
	mf_uninstall_plugin(array(
		'options' => array('setting_share_options', 'setting_share_options_visible', 'setting_share_services', 'setting_share_twitter', 'setting_share_visible', 'setting_share_form', 'setting_share_email_subject', 'setting_share_email_content'),
	));
}