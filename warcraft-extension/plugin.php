<?php
/*
Plugin Name: Warcraft Review Extension
Plugin URI: 
Description: Addon
Author: Not your business Inc
Author URI: 
Version: 1.0.0
*/

DEFINE('WARCRAFT_PLUGIN_BASE', dirname( __FILE__ ) );





require_once( 'classes/podcast-directory.php' );

require_once( 'events/extra-events.php' );
require_once( 'events/extra-functions.php' );
require_once( 'events/main.php' );
require_once( 'events/custom-post-types.php' );

//require_once( 'inc/popups.php' ); // White screen issue is here
require_once( 'inc/cron.php' );

require_once( 'shortcodes/all-shows.php' );
require_once( 'shortcodes/my-shows.php' );
require_once( 'shortcodes/podcast-reviews.php' );
require_once( 'shortcodes/popups.php' );


if( $_GET['update_podcasts'] ) {
	
	// https://dev.warcraftradio.com/?update_podcasts=y
	
	add_action('init', 'update_podcasts');
	function update_podcasts() {
		
		$PD = new PodcastDirectory;
	
		//$PD->ClearPodcasts();
		$PD->UpdatePodcasts();
		
		die();
		
	}
	
}












