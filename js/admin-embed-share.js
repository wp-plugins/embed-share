/**
 *	fires when the dom is ready
 *
 */
jQuery(document).ready(function() {
	cpesAdminJS();
	cpesPreview();
});

/**
 * Options JS
 *
 */
function cpesAdminJS() {
	var check = jQuery('#cpes_use_branding');
	check.click(function(){
		var cpes = jQuery('#embed_branding_fields');
		if ( check.is(':checked') )
			cpes.show();
		else
			cpes.hide();			
	});
	
	var check_post = jQuery('#cpes_use_post');
	check_post.click(function(){
		var cpes2 = jQuery('#cpes_options_adjective, #cpes_options_prefix');
		if ( check_post.is(':checked') )
			cpes2.show();
		else
			cpes2.hide();			
	});
}

function cpesPreview() {
	
	var fields 	= jQuery('#embed_branding_fields input');
	var preview = jQuery('#cpes_preview');
	
	// init
	updatePreview();
	
	// on change
	fields.keyup(function() {
		updatePreview();
	});
	fields.change(function() {
		updatePreview();
	});
	
	// set preview
	function updatePreview() {
		var field_title = jQuery("#cpes_field_title").val();
		var field_url 	= jQuery("#cpes_field_url").val();		
		var field_prefi	= jQuery("#cpes_field_prefix").val();		
		var field_adj 	= jQuery("#cpes_field_adjective").val();		
		var use_post 	= jQuery("#cpes_use_post");		
		
		// check post usage
		var post = '';		
		if ( use_post.is(':checked') === true ) {
			var prefix = field_prefi + ' ';
			var post = prefix + '<a href="http://permalink">Post Title</a> ' + field_adj + ' ';
		}
		
		// set html
		preview.html( post + '<a href="' + field_url + '">' + field_title + '</a>');
	};
	
}