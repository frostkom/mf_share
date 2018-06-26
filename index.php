<?php
/*
Plugin Name: MF Share
Plugin URI: https://github.com/frostkom/mf_share
Description: 
Version: 2.4.8
Licence: GPLv2 or later
Author: Martin Fors
Author URI: http://frostkom.se
Text Domain: lang_share
Domain Path: /lang

Depends: MF Base
GitHub Plugin URI: frostkom/mf_share
*/

include_once("include/classes.php");
include_once("include/functions.php");

$obj_share = new mf_share();

if(is_admin())
{
	register_uninstall_hook(__FILE__, 'uninstall_share');

	add_action('admin_init', 'settings_share');

	add_filter('count_shortcode_button', 'count_shortcode_button_share');
	add_filter('get_shortcode_output', 'get_shortcode_output_share');
}

else
{
	add_filter('language_attributes', array($obj_share, 'language_attributes'));

	add_action('wp_head', array($obj_share, 'wp_head'), 0);
	add_action('wp_footer', 'footer_share');

	add_filter('the_content', 'content_share');
	add_filter('the_content_meta', array($obj_share, 'content_meta'), 10, 2);

	add_shortcode('mf_share', 'shortcode_share');
}

add_action('widgets_init', 'widgets_share');

load_plugin_textdomain('lang_share', false, dirname(plugin_basename(__FILE__)).'/lang/');

function uninstall_share()
{
	mf_uninstall_plugin(array(
		'options' => array('setting_share_options', 'setting_share_options_visible', 'setting_share_services', 'setting_share_twitter', 'setting_share_visible', 'setting_share_form', 'setting_share_email_subject', 'setting_share_email_content'),
	));
}