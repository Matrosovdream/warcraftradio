jQuery(document).on('click', '.update-podcasts', function() {
	
	var data = {

	};
	
	jQuery('#ajax-response').html( 'Loading...' );

	jQuery.post( '/?update_podcasts=y', data, function(response) {
		jQuery('#ajax-response').html( response );
	});		
	
	return false;
	
});		
