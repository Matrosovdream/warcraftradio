<?php
add_shortcode( 'my_shows', 'my_shows_func' );
function my_shows_func( $atts ) {
	
	
	$PC = new PodcastDirectory;
	
	if( !$PC->isUserShowOwner() ) { exit('No access'); }

	$podcasts = $PC->GetPodcasts();

	$field_code = 'category';
	$categories = $PC->GetSelectACF( $field_code );

	$field_code = 'tags';
	$tags = $PC->GetSelectACF( $field_code );


	foreach( $podcasts as $item ) {
		
		$show_owner = get_post_meta( $item->ID, 'show_owner' )[0];
		
		if( $show_owner == get_current_user_id() ) {
			$shows[] = $item;
		}
		
		//echo $show_owner;
		
	}
	
	$pages = $PC->GetShortcodePages();
	
	$tag_props = $PC->GetTagsProp();
	
	ob_start(); 
	?>
		
		
		<div class="podcast-directory-wrapper">

			<!--
			<h3> Click Edit to edit your shows. If your show is not listed here, please contact athalus@warcraftradio.com. 
			Also consider joining the non-public podcast creator discord for Blizzard podcasters. E-mail athalus for details. </h3>
			-->
			
			<h1> Edit your shows </h1>
			
			<a href="<? echo $pages['all_shows']; ?>"> <button class="btnpd"><i class="fa fa-angle-double-left"></i> Back To Directory</button> </a>
			<br/>
			<br/>
			<br/>

			<ul class="podcasts edit-list">
			
				<? foreach( $shows as $item ) { ?>
				
					<?
					$tags = $PC->GetPodcastTags( $item->ID );
					
					$meta = get_post_meta( $item->ID );
					$podcast_db = $PC->GetPodcastFromDB( $meta['database_id'][0] );
					
					$image = '/wp-content/uploads/podcast_images/podcast_db_'.$podcast_db['id'].'.jpg';
					if( !file_exists(  $_SERVER['DOCUMENT_ROOT'].$image ) ) {
						$image = '/wp-content/uploads/podcast_images/podcast_db_'.$podcast_db['id'].'.png';
					}
					
					if( !file_exists(  $_SERVER['DOCUMENT_ROOT'].$image ) ) {
						$image = false;
					}	
					?>
				
					<li>
						<div class="img">
							<img src="<? echo $image; ?>" />
						</div>
						<div class="content">
							<a href="#text-popup" class="popup-open" data-id="<? echo $item->ID; ?>">
								<p class="title">
									<? echo $item->post_title; ?>
								</p>
							</a>
							
							<p class="tags">
								<span class="yellow" style="float: left;">Tags:</span> 
								
								<? foreach( $tags as $code=>$tag ) { ?>
								
									<?
									$props = $tag_props[ $tag ];
									?>
								
									<span class="podcast-tag" style="background-color: <?=$props['background']?>; color: <?=$props['font_color']?>">
										<? echo $tag; ?>
									</span>
								<? } ?>
								
								<div style="clear: both;"></div>
							</p>
							
							<a href="#popup-edit-podcast" class="edit-podcast-link popup-open" data-id="<? echo $item->ID; ?>" >
								<button class="btnpd" style="padding: 12px 16px!important; float: right;"><i class="fa fa-angle-double-right"></i> Update my show </button>
							</a>
							
							<?/*?>
							<p class="tags">
								<span class="yellow">Tags:</span> 
								
								<? foreach( $tags as $code=>$tag ) { ?>
									<span class="podcast-tag <?=$code;?>">
										<? echo $tag; ?>
									</span>
								<? } ?>
								
								<!--
								<span class="podcast-tag humor">Humor</span>
								<span class="podcast-tag casual">Casual</span>
								<span class="podcast-tag esports">Esports</span>
								<span class="podcast-tag endgame">End game</span>
								-->
								
							</p>
							<?*/?>
							
						</div>

						<div style="clear: both;"></div>

					
					</li>
				
				<? } ?>
				
				<!--<div style="clear: both;"></div>-->
			
			</ul>

		</div>
		
		<? 
		wp_footer_warcraft(); 
		?>
		
		<style>
			.entry-header { display: none!important; }
			.entry-content { padding: 0!important; }
		
		</style>
	
	
	
	<?
	$html = ob_get_contents();
	ob_end_clean();
	
	return $html;

}
?>