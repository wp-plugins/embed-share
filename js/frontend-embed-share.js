/**
 *	fires when the dom is ready
 *
 */
jQuery(document).ready(function() {
	cpesJS();
});

/**
 * button
 *
 */
function cpesJS() {
	// hide textarea
	jQuery('.video_embed_textarea').hide();
	
	// button click
	jQuery('.video_embed_share_button').click(function(e){
		e.preventDefault();
		var embed = jQuery(this).parent().find('.video_embed_textarea');
		if ( embed.is(":visible") ) {
			embed.slideUp();			
		} else {
			embed.slideDown();
		}
	});
		
	// textarea focus
	jQuery(".video_embed_textarea textarea").focus(function(){
		jQuery(this).select();
	});
}