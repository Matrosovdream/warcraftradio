<?php
add_shortcode( 'podcast_reviews', 'podcast_reviews_func' );
function podcast_reviews_func( $atts ) {
	
	
	$PC = new PodcastDirectory;

	$podcast_id = $_GET['podcast'];

	$podcast_info = $PC->GetPodcastByID( $podcast_id );
	$reviews = $PC->GetReviewsByPodcast( $podcast_id );
	
	$pages = $PC->GetShortcodePages();

	/*
	echo "<pre>";
	print_r($reviews);
	echo "</pre>";
	*/

	ob_start(); 
	?>
	
		<div class="podcast-directory-wrapper">

			<div class="list-reviews">
	
				<? if( !$reviews ) { ?>
				
					<p class="no-reviews"> No reviews yet. </p>
					
					<a href="<? echo $pages['all_shows']; ?>">
						<button class="btnpd"><i class="fa fa-angle-double-left"></i> Back To Directory</button>
					</a>
				
				<? } else { ?>
				
					<h1> <? echo $podcast_info['post_title'] ?>: reviews </h1>
					
					<a href="<? echo $pages['all_shows']; ?>">
						<button class="btnpd"><i class="fa fa-angle-double-left"></i> Back To Directory</button>
					</a>
					
					<ul>
					
						<? foreach( $reviews as $item ) { ?>
						
							<?
							$can_edit = $PC->canUserEditReview( $item['ID'] );
							$is_admin = current_user_can('administrator');
							
							$user_id = get_post_meta( $item['ID'], 'user_id')[0];
							$user_info = get_user_by( 'ID', $user_id );
							
							//print_r($item);
							?>
						
							<li class="">
							
								<div>
									<p class="note" style="float: left; width: 75%;">
										<span class="yellow">Reviewed By</span> 
										<? echo $user_info->display_name; ?> 
										<span class="yellow">on</span> 
										<? echo date( 'Y-m-d', strtotime($item['post_date']) ); ?>
									</p>
									
									<p style="float: right; width: 25%;">
										<span class="yellow">Star Rating:</span> <? echo $item['rating']; ?>
									</p>
									
									<div style="clear: both;"></div>
									
								</div>
								
								<p class="content"> <? echo $item['content']; ?> </p>
								<br/>
							
								<?/*?>
								<!--<p class="title"> <? echo $item['title']; ?> </p>-->
								<p class="date"> <? echo date( 'Y-m-d H:i', strtotime($item['post_date']) ); ?>, <? echo $user_info->display_name; ?> </p>
								<!--<p class="user"> User: <? echo $user_info->display_name; ?> </p>-->
								<p class="content"> <? echo $item['content']; ?> </p>
								<p class="rating1"> <span class="yellow">Rating:</span> <? echo $item['rating']; ?> </p>
								<?*/?>

								<? if( $can_edit ) { ?>
									
									
									<a 
										href="#popup-edit-review" 
										class="popup-leave-review popup-open" 
										id="link-edit-review"
										data-id="<? echo $item['ID']; ?>"> 
										<button class="btnpd"><i class="fa fa-edit"></i> Edit Review</button>
									</a>
									
								<? } ?>
								
								<? if( $is_admin ) { ?>
									
									<a 
										href="?podcast=<? echo $podcast_id; ?>&action=delete_review&id=<? echo $item['ID']; ?>" 
										class="" onclick="if( confirm('Are you sure?') ) { return true; }"> 
										<? if( $can_edit ) { ?><? } ?>
										<button class="btnpd"><i class="fa fa-trash"></i> Delete Review</button>
									</a>
									
								<? } ?>
								
							</li>
						
						<? } ?>
					
					</ul>

				
				<? } ?>
		
			</div>

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