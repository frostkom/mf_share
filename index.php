<?php
/*
Plugin Name: MF Share
Plugin URI: https://github.com/frostkom/mf_share
Description:
Version: 2.6.14
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_share
Domain Path: /lang

Depends: MF Base
GitHub Plugin URI: frostkom/mf_share
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_share = new mf_share();

	add_action('cron_base', 'activate_share', mt_rand(1, 10));

	add_action('init', array($obj_share, 'init'));

	if(is_admin())
	{
		register_activation_hook(__FILE__, 'activate_share');
		register_uninstall_hook(__FILE__, 'uninstall_share');

		add_action('admin_init', array($obj_share, 'settings_share'));

		//add_filter('count_shortcode_button', array($obj_share, 'count_shortcode_button'));
		//add_filter('get_shortcode_output', array($obj_share, 'get_shortcode_output'));
	}

	else
	{
		add_filter('language_attributes', array($obj_share, 'language_attributes'));

		add_action('wp_head', array($obj_share, 'wp_head'), 0);
		add_action('wp_footer', array($obj_share, 'wp_footer'));

		add_filter('the_content', array($obj_share, 'the_content'));
		add_filter('the_content_meta', array($obj_share, 'the_content_meta'), 10, 2);

		add_shortcode('mf_share', array($obj_share, 'shortcode_share'));
	}

	if(wp_is_block_theme() == false)
	{
		add_action('widgets_init', array($obj_share, 'widgets_init'));
	}

	function activate_share()
	{
		mf_uninstall_plugin(array(
			'options' => array('setting_share_form'),
		));
	}

	function uninstall_share()
	{
		mf_uninstall_plugin(array(
			'options' => array('setting_share_options', 'setting_share_options_visible', 'setting_share_options_titles', 'setting_share_services', 'setting_share_twitter', 'setting_share_visible', 'setting_share_pages', 'setting_share_form', 'setting_share_email_subject', 'setting_share_email_content'),
		));
	}
}