<?php
add_action('save_post', 'calculate_podcast_rating');
add_action('before_delete_post', 'calculate_podcast_rating');
function calculate_podcast_rating( $post_id ) {
	
	$post = get_post( $post_id );

	if( $post->post_type != 'podcast_reviews' ) { return true; }
	
	$rating = get_post_meta( $post_id, 'rating' )[0];
	
	if( !$rating ) { return true; }
	
	$PC = new PodcastDirectory;

	$podcast_id = get_post_meta( $post_id, 'podcast_id' )[0];
	$reviews = $PC->GetReviewsByPodcast( $podcast_id );
	
	$rating = 0;
	
	foreach( $reviews as $review ) {
		if( $review['rating'] == 'NAN' ) { continue; }
		
		$rating += $review['rating'];
	}
	
	$average = $rating / count($reviews);
	
	$average = number_format((float)$average, 2, '.', ''); 
	
	update_post_meta( $podcast_id, 'rating', $average );
	update_post_meta( $podcast_id, 'reviews_count', count($reviews) );
	
	//print_r($average);
	
	//die();
	
	//print_r($reviews);
	
}


add_action('init', 'save_review_form');
function save_review_form() {
	
	if( $_POST['action'] == 'save_review' ) {
	
		if( $_POST['review'] != '' ) {
			
			$current_user = wp_get_current_user();
			
			$title = "Review #".time();
			
			$post_data = array(
				'post_title'    => wp_strip_all_tags( $title ),
				'post_content'  => $_POST['review'],
				'post_status'   => 'publish',
				'post_type'   => 'podcast_reviews',
				'post_author'   => get_current_user_id(),
			);

			// Insert
			$post_id = wp_insert_post( $post_data );
			
			update_post_meta( $post_id, 'podcast_id', $_POST['podcast_id'] );
			update_post_meta( $post_id, 'user_id', get_current_user_id() );
			update_post_meta( $post_id, 'rating', $_POST['star'] );
			update_post_meta( $post_id, 'user_ip', $_SERVER['REMOTE_ADDR'] );
			update_post_meta( $post_id, 'username', $current_user->user_login );
			
			calculate_podcast_rating( $post_id );
			
		}
		
		die();
	
	}
	
	
	if( $_POST['action'] == 'edit_review' ) {
		
		$PC = new PodcastDirectory;
		
		$can_edit = $PC->canUserEditReview( $_POST['ID'] );
		$post_id = $_POST['ID'];
	
		if( $_POST['review'] != '' && $can_edit ) {
			
			$title = "Review #".time();
			
			$post_data = array(
				'ID'    => $_POST['ID'],
				'post_title'    => wp_strip_all_tags( $title ),
				'post_content'  => $_POST['review'],
				'post_status'   => 'publish',
				'post_type'   => 'podcast_reviews',
				'post_author'   => get_current_user_id(),
			);

			// Update
			wp_update_post( $post_data );
			update_post_meta( $post_id, 'rating', $_POST['star'] );
			calculate_podcast_rating( $post_id );
			
		}
		
		die();
	
	}
	
	
	if( $_POST['action'] == 'update_podcast' ) {
		
		$PC = new PodcastDirectory;
		
		$post_id = $_POST['ID'];
		
		$my_post = array();
		$my_post['ID'] = $post_id;
		$my_post['post_content'] = $_POST['description'];

		// Обновляем данные в БД
		wp_update_post( wp_slash($my_post) );
		
		update_post_meta( $post_id, 'explicit', $_POST['explicit'] );
		update_post_meta( $post_id, 'tags', $_POST['tags'] );
				
		update_post_meta( $post_id, 'youtube', $_POST['youtube'] );
		update_post_meta( $post_id, 'discord', $_POST['discord'] );
		update_post_meta( $post_id, 'twitter', $_POST['twitter'] );
		update_post_meta( $post_id, 'liverecordingurl', $_POST['liverecordingurl'] );
		update_post_meta( $post_id, 'twitch', $_POST['twitch'] );
		update_post_meta( $post_id, 'rssfeed', $_POST['rssfeed'] );
		update_post_meta( $post_id, 'website', $_POST['website'] );
		update_post_meta( $post_id, 'facebook', $_POST['facebook'] );
		update_post_meta( $post_id, 'instagram', $_POST['instagram'] );
		update_post_meta( $post_id, 'patreon', $_POST['patreon'] );
		update_post_meta( $post_id, 'itunes_author', $_POST['itunes_author'] );
		
		
		// Loading audio file
		if( $_FILES['audio']['name'] ) {
			
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			
			$attachment_id = media_handle_upload( 'audio', $post_id );
			update_post_meta( $post_id, 'audio', $attachment_id );
			
			/*echo "<pre>";
			print_r( $_FILES );
			echo "</pre>";*/
		
		}
		
		if( $_POST['remove_audio'] == 'Y' ) {
			update_post_meta( $post_id, 'audio', '' );
		}
		
		wp_redirect( $_SERVER['REQUEST_URI'] ); 
		exit;
	
	}
	
	
}


add_action('wp_ajax_response_to_review', 'response_to_review_callback');
add_action('wp_ajax_nopriv_response_to_review', 'response_to_review_callback');
function response_to_review_callback() {
	
	$review_id = $_POST['review_id'];
	$podcast_id = $_POST['podcast_id'];
	$review = $_POST['review'];

	$current_user = wp_get_current_user();
	$title = "Review #".time();
	
	$post_data = array(
		'post_title'    => wp_strip_all_tags( $title ),
		'post_content'  => $review,
		'post_status'   => 'publish',
		'post_type'   => 'podcast_reviews',
		'post_author'   => get_current_user_id(),
	);

	// Insert
	$post_id = wp_insert_post( $post_data );
	
	echo $post_id;
	
	update_post_meta( $post_id, 'podcast_id', $_POST['podcast_id'] );
	update_post_meta( $post_id, 'parent_id', $review_id );
	update_post_meta( $post_id, 'user_id', get_current_user_id() );
	update_post_meta( $post_id, 'user_ip', $_SERVER['REMOTE_ADDR'] );
	update_post_meta( $post_id, 'username', $current_user->user_login );

	wp_die();
}


add_action('wp_ajax_edit_response', 'edit_response_callback');
add_action('wp_ajax_nopriv_edit_response', 'edit_response_callback');
function edit_response_callback() {
	
	$response_id = $_POST['ID'];
	$review = $_POST['review'];
	
	$post_data = array(
		'ID' => $response_id,
		'post_content'  => $review,
	);

	// Update
	$post_id = wp_update_post( $post_data );

	wp_die();
}





?>