<?php
/*
Plugin Name: MF Share
Plugin URI: 
Version: 1.0.5
Author: Martin Fors
Author URI: www.frostkom.se
*/

register_activation_hook(__FILE__, 'activate_share');

add_action('init', 'init_share');
add_action('admin_init', 'settings_share');
add_action('wp_footer', 'footer_share');
add_filter('the_content', 'content_share');
add_shortcode('mf_share', 'shortcode_share');

load_plugin_textdomain('lang_share', false, dirname(plugin_basename(__FILE__)).'/lang/');

function activate_share()
{
	require_plugin("mf_base/index.php", "MF Base");
}

require_once("include/functions.php");

function shortcode_share($atts)
{
	extract(shortcode_atts(array(
		'type' => ''
	), $atts));

	return get_share_content($type);
}