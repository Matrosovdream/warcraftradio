<?php
function my_admin_menu() {
	add_menu_page(
		__( 'Podcast settings', 'warcraft-extension' ),
		__( 'Podcast settings', 'warcraft-extension' ),
		'manage_options',
		'warcraft-extension',
		'warcraft_extension_page_contents',
		'dashicons-schedule',
		10
	);
}
add_action( 'admin_menu', 'my_admin_menu' );


function warcraft_extension_page_contents() {
	?>
		<h1>
			<?php esc_html_e( 'Warcraft Settings', 'warcraft-extension' ); ?>
		</h1>
		
		
		<hr/>
		
		<a class="update-podcasts" style="font-size: 20px; cursor: pointer;">Upload a new Podcasts from DB</a>
		<div id="ajax-response" style="margin-top: 8px; font-size: 17px;"></div>
		
	<?php
}