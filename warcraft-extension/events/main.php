<?php
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
// add_action('wp_print_styles', 'theme_name_scripts'); // можно использовать этот хук он более поздний
function theme_name_scripts() {
	wp_enqueue_style( 'warcraft-extension-styles', plugins_url('warcraft-extension/style.css?t='.time() ) );
	
	wp_enqueue_script( 'extra.js', plugins_url('warcraft-extension/js/extra.js?t='.time() ) );
	
}


function my_enqueue($hook) {
	
    // Only this page
    if ('toplevel_page_warcraft-extension' !== $hook) {
        return;
    }
    wp_enqueue_script( 'extra.js', plugins_url('warcraft-extension/js/admin.js?t='.time() ) );
}

add_action('admin_enqueue_scripts', 'my_enqueue');


add_action('init', 'remove_review');
function remove_review() {
	
	if( $_GET['action'] == 'delete_review' ) {
		
		$PC = new PodcastDirectory;
		
		$podcast_id = $_GET['podcast'];
		
		if( $PC->isUserAdmin() ) {
			
			wp_trash_post( $_GET['id'] );
			
			calculate_podcast_rating( $podcast_id );
			
			wp_redirect( '/podcast-reviews/?podcast='.$podcast_id );
			exit;
		}

	}
	
}
?>