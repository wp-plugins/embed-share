<?php
/*
Plugin Name: Embed Share
Plugin URI: http://www.codepress.nl/plugins/embed-share/
Description: Adds an easily shareable embed code to your videos. 
Author: Tobias Schutter
Version: 1.10
Author URI: http://www.codepress.nl

Updates:
1.10 - Added Social sharing to video's from addThis
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
define( 'CPES_VERSION', '1.10' );
define( 'CPES_URL', plugin_dir_url(__FILE__) );
define( 'CPES_PATH', plugin_dir_path(__FILE__) );
define( 'CPES_BASENAME', plugin_basename( __FILE__ ) );

// get options
$options = get_option('cpes_options');

// Dependencies
require CPES_PATH.'options.php';

// Enqueue scripts
wp_enqueue_script('frontend-embed-share-js', CPES_URL . 'js/frontend-embed-share.js', array('jquery'), CPES_VERSION, false);
wp_enqueue_style('frontend-embed-share-css', CPES_URL . 'css/frontend-embed-share.css', false, CPES_VERSION, 'all');

if ( $options['use_social'] || $options['more_social'] )
	wp_enqueue_script('addthis-js', 'http://s7.addthis.com/js/250/addthis_widget.js');

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
	$options = get_option('cpes_options');
	foreach ( $oembed_providers as $mask => $data ) {
		list( $providerurl, $regex ) = $data;
		
		// apply iframe argument
		$iframe = $options['use_iframe'] == 'on' ? 1 : 0 ;
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
	$options = get_option('cpes_options');
	
	// forces usage of iframes for youtube
	$oembed 	= wp_oembed_get($url);
	
	// Use the original embed url to create a unique anchor ID.
	$unique_id = 'e' . preg_replace('#\W#', '', array_pop( explode('/', $url) ) );
	
	// branding
	$branding = '';
	if ( $options['use_branding'] == 'on' ) {	
		if ( $options['title'] ) {
			
			// post url			
			if ( $options['use_post'] == 'on' ) {
				global $post;				
				$prefix 	= $options['prefix'] ? $options['prefix'].' ' : '';	
				$post_link	= get_permalink($post->ID) . "#{$unique_id}";
				$use_post 	= "{$prefix}<a href='{$post_link}'>{$post->post_title}</a> {$options['adjective']} ";
			}
			
			// branding
			$branding	= "<span style=\"width:500px;text-align:center;display:block\">{$use_post}<a href='{$options['url']}'>{$options['title']}</a></span>";
		}
	}
	
	//button
	$button = $options['button'] ? $options['button'] : __('Share');	
	
	// social sharing
	$social = '';
	if ( $options['use_social'] ) {
		foreach ( $options['use_social'] as $service => $checked ) {	
			$social .= "<a class='addthis_button_{$service}'></a>";		
		}
	}	
	if ( $options['more_social'] ) {
		$mores = explode( ',', $options['more_social']);
		foreach ( $mores as $m ) {
			$m = trim(strip_tags($m));
			$social .= "<a class='addthis_button_{$m}'></a>";
		}
	}	
	
	// filter hooks are sexy
	$social = apply_filters('cpes_social_buttons', $social);
	
	if ( $social ) {		
		// add anchor to link
		$addthis_url = get_permalink($post->ID) . "#{$unique_id}" ;
		
		// use addThis for showing social icons
		$addthis = "<div class='addthis_toolbox addthis_default_style' addthis:url='{$addthis_url}'>{$social}</div>";
	}
	
	// markup
	$markup = "<a name='{$unique_id}'></a>{$return}<div class='video_embed_share'><a href='#' class='video_embed_share_button'>{$button}</a>{$addthis}<div class='video_embed_textarea' style='display: none;'><textarea rows='8'>{$oembed}{$branding}</textarea><div class='video_embed_note'>{$options['message']}</div></div></div>";	

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

/**
 *	Add Facebook meta property to overwrite the usage of the canonical url ( only applies when it is set )
 */
function cpes_head_add_facebook_properties() {	
	echo '<meta property="og:url" content="'.get_permalink($post->ID).'">';
}
add_action('wp_head', 'cpes_head_add_facebook_properties');	

/**
 *	Eaxmple usage of filters
 */
function cpes_social_filter( $anchors ) {	
	// Fill in your social urls
	$twitter_url 	= '';
	$facebook_url 	= '';
	
	if ( $twitter_url || $facebook_url ) {
		$link = get_bloginfo('template_url');	
		$anchors = "
			{$anchors}
			<a href=''><img src='{$facebook_url}/images/facebook.png' alt='' /></a>
			<a href=''><img src='{$twitter_url}/images/twitter.png' alt='' /></a>
		";
	}
	return $anchors;
}
add_filter('cpes_social_buttons', 'cpes_social_filter');

?>