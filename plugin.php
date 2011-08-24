<?php
/*
Plugin Name: Embed Share
Plugin URI: http://www.codepress.nl/plugins/embed-share/
Description: Adds an easily shareable embed code to your videos. 
Author: Tobias Schutter
Version: 1.01
Author URI: http://www.codepress.nl

Updates:
1.01 - Custom Link markup changed
1.00 - First Version

	Copyright 2011 Tobias Schutter

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if(defined('CPES_VERSION')) return;

// Determine plugin directory
define( 'CPES_VERSION', '1.01' );
define( 'CPES_URL', plugin_dir_url(__FILE__) );
define( 'CPES_PATH', plugin_dir_path(__FILE__) );
define( 'CPES_BASENAME', plugin_basename( __FILE__ ) );

// get options
global $cpes_options;
$cpes_options = get_option('cpes_options');

// Dependencies
require CPES_PATH.'options.php';

// Enqueue scripts
wp_enqueue_script('frontend-embed-share-js', CPES_URL . 'js/frontend-embed-share.js', array('jquery'), CPES_VERSION, false);
wp_enqueue_style('frontend-embed-share-css', CPES_URL . 'css/frontend-embed-share.css', false, CPES_VERSION, 'all');

// Enqueue admin scripts
function cpes_admin_scripts() {    
    wp_enqueue_script( 'admin-embed-share-js', CPES_URL . 'js/admin-embed-share.js' );
	wp_enqueue_script( 'postbox' );
	wp_enqueue_script( 'dashboard' );
}
add_action('admin_print_scripts', 'cpes_admin_scripts');

/**
 *	Enable iFrame Embed Format
 */
function cpes_change_embed_format($oembed_providers) {
	global $cpes_options;
	foreach ( $oembed_providers as $mask => $data ) {
		list( $providerurl, $regex ) = $data;
		
		// apply iframe argument
		$iframe = $cpes_options['use_iframe'] == 'on' ? 1 : 0 ;
		$providerurl = "{$providerurl}?iframe={$iframe}&wmode=transparent";
		
		// put it back together
		$oembed_providers[$mask] = array( $providerurl, $regex);
	}
	return $oembed_providers;
}

/**
 *	Add Embed Sharing to Video's. With an optional custom link.
 */
function cpes_add_embed_share($return, $url, $data) {		
	$cpes_options = get_option('cpes_options');
		
	// branding
	$branding = '';
	if ( $cpes_options['use_branding'] == 'on' ) {	
		if ( $cpes_options['title'] ) {
			
			// post url			
			if ( $cpes_options['use_post'] == 'on' ) {
				global $post;				
				$prefix 	= $cpes_options['prefix'] ? $cpes_options['prefix'].' ' : '';	
				$post_link	= get_permalink($post->ID);
				$use_post 	= "{$prefix}<a href='{$post_link}'>{$post->post_title}</a> {$cpes_options['adjective']} ";
			}
			
			// branding
			$branding	= "<span style=\"width:500px;text-align:center;display:block\">{$use_post}<a href='{$cpes_options['url']}'>{$cpes_options['title']}</a></span>";
		}
	}
	
	//button
	$button = $cpes_options['button'] ? $cpes_options['button'] : __('Share');
	
	// forces usage of iframes for youtube
	$oembed 	= wp_oembed_get($url);
	
	// markup
	$markup = "{$return}<div class='video_embed_share'><a href='#' class='video_embed_share_button'>{$button}</a><div class='video_embed_textarea' style='display: none;'><textarea rows='8'>{$oembed}{$branding}</textarea><div class='video_embed_note'>{$cpes_options['message']}</div></div></div>";	

    return $markup;
}

/**
 *	Add "Rate this Plugin".
 */
function cpes_embed_share_rate($links,$file) {
		if (plugin_basename(__FILE__) == $file) {
			$links[] = '<a href="http://wordpress.org/extend/plugins/embed-share/">Rate this plugin</a>';
		}
	return $links;
}

add_filter('oembed_providers', 'cpes_change_embed_format', 1, true);
add_filter('embed_oembed_html', 'cpes_add_embed_share', 10, 3); 
add_filter('plugin_row_meta', 'cpes_embed_share_rate', 10, 2);
?>