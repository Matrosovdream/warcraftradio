jQuery(document).ready(function() {

	if( jQuery('.popup-open').length > 0 ) {
		
		jQuery('.popup-open').magnificPopup({
			type: 'inline'
		});

	}

});

jQuery(document).on('click', '.podcasts li .content .popup-open', function() {
	
	var id = jQuery(this).attr('data-id');
	
	var data = {
		action: 'podcast_details',
		id: id
	};
	
	jQuery('#text-popup').html( 'Loading...' );

	jQuery.post( '/wp-admin/admin-ajax.php', data, function(response) {
		jQuery('#text-popup').html( response );
	});		
	
});		


jQuery(document).on('click', '.edit-podcast-link', function() {
	
	var id = jQuery(this).attr('data-id');
	
	var data = {
		action: 'podcast_edit',
		id: id
	};
	
	jQuery('#popup-edit-podcast').html( 'Loading...' );

	jQuery.post( '/wp-admin/admin-ajax.php', data, function(response) {
		jQuery('#popup-edit-podcast').html( response );
	});		
	
});		


jQuery(document).on('click', '.submit-review-link', function() {
	
	var id = jQuery(this).attr('data-id');
	
	jQuery('#popup-leave-review input[name="podcast_id"]').val( id );	
	
});	


jQuery(document).on('click', '#link-edit-review', function() {
	
	var id = jQuery(this).attr('data-id');
	
	var data = {
		action: 'podcast_edit_review',
		id: id
	};
	
	console.log(data);
	
	jQuery('#popup-edit-review').html( 'Loading...' );

	jQuery.post( '/wp-admin/admin-ajax.php', data, function(response) {
		jQuery('#popup-edit-review').html( response );
	});		
	
});		


jQuery(document).on('click', '.podcast-filter input[type="checkbox"], .podcast-filter input[type="radio"]', function() {
	
	submitFilterForm();
	
});	


jQuery(document).on('click', '#popup-edit-podcast .filter-tags input[type="checkbox"]', function() {
	
	var checked = jQuery('#popup-edit-podcast .filter-tags input[type="checkbox"]:checked');
	
	if( checked.length > 4 ) { return false; }
	
	console.log(checked);
	
});	


jQuery(document).on('change', '.podcast-filter select', function() {
	
	submitFilterForm();

});	


jQuery(document).on('submit', '#popup-leave-review form, #popup-edit-review form', function() {
	
	var review_sending = true;
	
	//if( review_sending ) { return false; }
	
	var data = jQuery(this).serialize();
	var val = jQuery('textarea[name="review"]').val();
	var stars = jQuery('input[name="star"]:checked');
	
	//console.log(stars.length);
	
	
	if( stars.length == 0 ) {
		
		review_sending = false;
		jQuery('.notice-stars.red').show();
		
	} else {
		jQuery('.notice-stars.red').hide();
	}
	
	
	if( val.length < 25 || val.length > 2000 ) {
		
		review_sending = false;
		jQuery('.notice.red').show();
	
	} else {
		jQuery('.notice.red').hide();
	}
	
	
	if( review_sending ) {
		
		//review_sending = true;
	
		jQuery('.notice.red').hide();
	
		//jQuery(this).find('input[type="submit"]').val('Saving...');
		jQuery(this).find('button').attr('disabled', true);

		jQuery.post( '/wp-admin/admin-ajax.php', data, function(response) {
			
			jQuery('.form-review').hide();
			jQuery('.success').show();
			
			//console.log( data );
			
			window.location.reload();
			
		});
		
	}
	
	return false;
	
	/*
	if( val.length < 25 || val.length > 2000 ) {
		
		jQuery('.notice.red').show();
	
	} else {	 
	
		review_sending = true;
	
		jQuery('.notice.red').hide();
	
		//jQuery(this).find('input[type="submit"]').val('Saving...');
		jQuery(this).find('button').attr('disabled', true);

		jQuery.post( '/wp-admin/admin-ajax.php', data, function(response) {
			
			jQuery('.form-review').hide();
			jQuery('.success').show();
			
			//console.log( data );
			
			window.location.reload();
			
		});	
		
	}
	*/
	
});


jQuery(document).on('submit', '#popup-edit-podcast form', function() {
	
	var data = jQuery(this).serialize();
	var val = jQuery('textarea[name="review"]').val();
	

	jQuery(this).find('button').val('Saving...');

	jQuery.post( '/wp-admin/admin-ajax.php', data, function(response) {
		
		jQuery('.form-review').hide();
		jQuery('.success').show();
		
		//console.log( data );
		
		window.location.reload();
		
	});	
		
	return false;
	
});


function openReviewPopup( id ) {
	
	jQuery('#popup-leave-review input[name="podcast_id"]').val( id );
	
	jQuery('.form-review').show();
	jQuery('.success').hide();
	
	
	jQuery('.form-review').trigger("reset");
	
}


function submitFilterForm() {
	
	var data = jQuery('.podcast-filter').serialize();
	
	console.log( window.location.href + '?' + data );

	jQuery.get( window.location.href + '?' + data, '', function(response) {
		
		var html = jQuery(response).find('.podcasts').html();
		jQuery('.podcasts').html( html );
		
		jQuery('.popup-open').magnificPopup({
			type: 'inline'
		});
		
		console.log( html );
		
	});	

	return false;
	
	
	/*
	var data = jQuery('.podcast-filter').serialize();
	
	jQuery('.podcast-filter').submit();
	
	return false;
	
	jQuery.get( window.location.href, data, function(response) {

		console.log( response );
		
	});	
	
	console.log(data);
	
	//jQuery('#popup-leave-review input[name="podcast_id"]').val( id );	
	*/
	
}
	