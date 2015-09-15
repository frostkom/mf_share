<?php
/*
Plugin Name: MF Share
Plugin URI: www.github.com/frostkom/mf_share
Version: 1.0.6
Author: Martin Fors
Author URI: www.frostkom.se
*/

add_action('init', 'init_share');
add_action('admin_init', 'settings_share');
add_action('wp_footer', 'footer_share');
add_filter('the_content', 'content_share');
add_shortcode('mf_share', 'shortcode_share');

load_plugin_textdomain('lang_share', false, dirname(plugin_basename(__FILE__)).'/lang/');

require_once("include/functions.php");

function shortcode_share($atts)
{
	extract(shortcode_atts(array(
		'type' => ''
	), $atts));

	return get_share_content($type);
}