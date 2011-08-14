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
	
	// use branding?
	$checked_branding = '';
	$hide = ' style="display:none"';
	if ( $options['use_branding'] == 'on' ) {
		$checked_branding = ' checked="yes"';
		$hide = ' style="display:block"';
	}
	
	// use post prefix?
	//$checked_post = $options['use_post'] == 'on' ? ' checked="yes"' : '';	
	// use post?
	$checked_post = '';
	$hide_post = ' style="display:none"';
	if ( $options['use_post'] == 'on' ) {		
		$checked_post = ' checked="yes"';
		$hide_post = ' style="display:row"';
	}	
	
	// embed format
	$checked_iframe = $options['use_iframe'] == 'on' ? ' checked="yes"' : '';

	/* $embed_formats = array(
		'iframe'	=> 'iFrame',
		'object'	=> 'Object'
	);
	foreach ( $embed_formats as $fk => $fv ) {
		$selected = $options['format'] == $fk ? ' selected="selected"' : '' ;
		$embed_format .= "<option{$selected} value='{$fk}'>{$fv}</option>";
	} */
	
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
											<input type="checkbox" id="cpes_use_iframe" name="cpes_options[use_iframe]"'.$checked_iframe.'>
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
						<div id="general-cpes-settings" class="postbox">
							<div class="handlediv" title="Click to toggle"><br /></div>
							<h3 class="hndle"><span>Custom Link</span></h3>
							<div class="inside">						
								<p>
									<input type="checkbox" id="cpes_use_branding" name="cpes_options[use_branding]"'.$checked_branding.'>
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
											<input type="checkbox" id="cpes_use_post" name="cpes_options[use_post]"'.$checked_post.'>
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
						</div>
					</form>
				</div><!--.meta-box-sortables-->				
			</div>
		</div>
	</div>
	';
}

/**
 * Create a potbox widget
 */
function postbox($id, $title, $content) {
?>
	<div id="<?php echo $id; ?>" class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php echo $title; ?></span></h3>
		<div class="inside">
			<?php echo $content; ?>
		</div>
	</div>
<?php
}
?>