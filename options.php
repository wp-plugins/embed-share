<?php

// Menu
function cpes_create_menu() {
	add_submenu_page( 'options-general.php', 'Embed Share Settings', 'Embed Share', 'administrator', __FILE__,'cpes_settings_page');
	add_action( 'admin_init', 'cpes_register_mysettings' );
}
add_action('admin_menu', 'cpes_create_menu');

// Settings
function cpes_register_mysettings() {
	register_setting( 'cpes-settings-group', 'cpes_options' );
	
}

// Options Page
function cpes_settings_page() {
	
	// define
	$options = get_option('cpes_options');	
	
	$checked = array();
	
	// use branding?
	$checked['branding'] = '';
	$hide = ' style="display:none"';
	if ( $options['use_branding'] == 'on' ) {
		$checked['branding'] = ' checked="yes"';
		$hide = ' style="display:block"';
	}
	
	// use post prefix?
	// use post?
	$checked['post'] = '';
	$hide_post = ' style="display:none"';
	if ( $options['use_post'] == 'on' ) {		
		$checked['post'] = ' checked="yes"';
		$hide_post = ' style="display:row"';
	}	
	
	// embed format
	$checked['iframe'] 		= $options['use_iframe'] == 'on' 	? ' checked="yes"' : '';
	
	// add social sharing services
	// see a complete list at http://www.addthis.com/services/list
	$services = array (	'email', 'twitter', 'facebook', 'linkedin', 'google_plusone' );
	
	// button
	$button = $options['button'] ? $options['button'] : __('Share');
	
	// message
	$message = $options['message'] ? $options['message'] : __('Copy and paste the embed code above if you have made your selection.');
	
	// adjective
	$prefix = $options['prefix'] ? $options['prefix'] : __('Video about');
	
	// adjective
	$adjective = $options['adjective'] ? $options['adjective'] : __('by');
	
	// admin page
	echo '
	<div class="wrap">
		<h2>Embed Share: General</h2>
		<div class="postbox-container" style="width:70%;">
			<div class="metabox-holder">	
				<div class="meta-box-sortables">
					<form method="post" action="options.php">';
					settings_fields( 'cpes-settings-group' );
					
					echo '
						<div id="general-cpes-settings" class="postbox">
							<div class="handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle"><span>General Settings</span></h3>
							<div class="inside">			
								<table class="form-table">
									<tr valign="top">
										<th scope="row">Button Name</th>
										<td>
											<input size="60" type="text" id="cpes_field_button" name="cpes_options[button]" value="'.$button.'" />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">iFrame Embed Format</th>
										<td colspan="2">
											<input type="checkbox" id="cpes_use_iframe" name="cpes_options[use_iframe]"'.$checked['iframe'].'>
											<label for="cpes_use_iframe">Force the use of the iFrame format for sharing.</label>
											<p class="description">Youtube, Vimeo and Dailymotion supports iFrame usage, which allows use of the HTML5 video player.</p>
											<p class="description hidden">( No iframe support for Blip.t, Hulu and Viddler. )</p>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">Message</th>
										<td colspan="2">
											<textarea id="cpes_field_message" name="cpes_options[message]" rows="2" cols="80">'.$message.'</textarea>
											<p class="description">This message will appear below the textarea.</p>
										</td>
									</tr>
								</table>
								<p class="submit">
									<input type="submit" class="button-primary" value="'.__('Save Changes').'" />
								</p>
							</div>
						</div>
						<div id="customlink-cpes-settings" class="postbox">
							<div class="handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle"><span>Custom Link</span></h3>
							<div class="inside">						
								<p>
									<input type="checkbox" id="cpes_use_branding" name="cpes_options[use_branding]"'.$checked['branding'].'>
									<label for="cpes_use_branding">Add a custom link to the embed code</label>
								</p>
								<table class="form-table" id="embed_branding_fields"'.$hide.'>									
									<tr valign="top">
										<th scope="row">Snippet Preview:</th>
										<td id="cpes_preview">
											
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">Link Title</th>
										<td>
											<input size="60" type="text" id="cpes_field_title" name="cpes_options[title]" value="'.$options['title'].'" />
										</td>
									</tr>									
									<tr valign="top" id="cpes_options_url">
										<th scope="row">Link Url</th>
										<td>
											<input size="60" type="text" id="cpes_field_url" name="cpes_options[url]" value="'.$options['url'].'" />
										</td>
									</tr>
									<tr valign="top">				
										<td colspan="2">
											<input type="checkbox" id="cpes_use_post" name="cpes_options[use_post]"'.$checked['post'].'>
											<label for="cpes_use_post">Add the Post Title and Link to the custom link</label>
										</td>
									</tr>
									<tr valign="top" id="cpes_options_prefix"'.$hide_post.'>
										<th scope="row">Prefix</th>
										<td>
											<input size="60" type="text" id="cpes_field_prefix" name="cpes_options[prefix]" value="'.$prefix.'" />
										</td>
									</tr>
									<tr valign="top" id="cpes_options_adjective"'.$hide_post.'>
										<th scope="row">Adjective</th>
										<td>
											<input size="60" type="text" id="cpes_field_adjective" name="cpes_options[adjective]" value="'.$adjective.'" />
										</td>
									</tr>									
								</table>								
								<p class="submit">
									<input type="submit" class="button-primary" value="'.__('Save Changes').'" />
								</p>
							</div>
						</div>';

$socialrows = '';						
foreach ( $services as $service ) {
	$checked 	 = $options['use_social'][$service] == 'on' ? ' checked="yes"' : '' ;
	$stitle 	 = ucfirst(str_replace('_', ' ', $service));
	$socialrows .= "
		<tr valign='top'>				
			<td colspan='2'>
				<input type='checkbox' id='cpes_use_social_{$service}' name='cpes_options[use_social][{$service}]'{$checked}>
				<label for='cpes_use_social_{$service}'>Add {$stitle}</label>
			</td>
		</tr>";	
}
						
						echo'
						<div id="socialsharing-cpes-settings" class="postbox">
							<div class="handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle"><span>Social Sharing</span></h3>
							<div class="inside">
								<p>This will add a social sharing button from addThis to your video\'s.</p>
								<table class="form-table" id="embed_social_fields">						
									'.$socialrows.'	
									<tr valign="top" id="cpes_options_more_socials">
										<th scope="row">More social services:</th>
										<td>											
											<input style="width:100%" type="text" id="cpes_field_more_socials" name="cpes_options[more_social]" value="'.$options['more_social'].'" />
											<p class="description">Add more social sharing with comma seperation. Example: print, stumbleupon, tweet. </p>
											<p class="description">See full list ( +100 ) for all supported social services at <a href="http://www.addthis.com/services/list" target="_blank">addThis full list</a></p>
										</td>
									</tr>
								</table>								
								<p class="submit">
									<input type="submit" class="button-primary" value="'.__('Save Changes').'" />
								</p>
							</div>
						</div>
					</form>
				</div><!--.meta-box-sortables-->				
			</div>
		</div>
	</div>
	';
}
?>