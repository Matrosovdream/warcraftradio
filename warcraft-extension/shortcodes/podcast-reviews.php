<?php
add_shortcode( 'podcast_reviews', 'podcast_reviews_func' );
function podcast_reviews_func( $atts ) {
	
	
	$PC = new PodcastDirectory;

	$podcast_id = $_GET['podcast'];

	$podcast_info = $PC->GetPodcastByID( $podcast_id );
	$reviews = $PC->GetReviewsByPodcast( $podcast_id );
	
	$pages = $PC->GetShortcodePages();
	
	if( get_current_user_id() == get_post_meta( $podcast_id, 'show_owner', true ) ) {
		$show_owner = true;
	}
	
	/*echo "<pre>";
	print_r($podcast_info);
	echo "</pre>";*/
	

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
							
							$child_reviews = $PC->GetChildReviews( $item['ID'] );
							
							//print_r($child_reviews);
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
								
								<? if( $show_owner ) { ?>
									
									<a 
										href="#popup-response-to-review" 
										class="popup-response-to-review popup-open" 
										id="link-response-to-review"
										data-review-id="<? echo $item['ID']; ?>"> 
										<button class="btnpd"><i class="fa fa-reply"></i> Respond</button>
									</a>
									
								<? } ?>
								
							</li>
							
							<? if( count( $child_reviews ) > 0 ) { ?>
							
								<? foreach( $child_reviews as $response ) { ?>
								
									<li class="response">
									
										<div>
											<p class="note" style="float: left; width: 75%;">
												<span class="yellow">Author's response on</span> 
												<? echo date( 'Y-m-d', strtotime($response['post_date']) ); ?>
											</p>
											
											<div style="clear: both;"></div>
											
										</div>
										
										<p class="content"> <? echo $response['content']; ?> </p>
										<br/>
									
										<? if( $can_edit ) { ?>
											
											<a 
												href="#popup-edit-response" 
												class="popup-edit-response link-edit-response popup-open" 
												data-id="<? echo $response['ID']; ?>"> 
												<button class="btnpd"><i class="fa fa-edit"></i> Edit Response</button>
											</a>
											
										<? } ?>
										
										<? if( $is_admin ) { ?>
											
											<a 
												href="?podcast=<? echo $podcast_id; ?>&action=delete_review&id=<? echo $response['ID']; ?>" 
												class="" onclick="if( confirm('Are you sure?') ) { return true; }"> 
												<? if( $can_edit ) { ?><? } ?>
												<button class="btnpd"><i class="fa fa-trash"></i> Delete Response</button>
											</a>
											
										<? } ?>
										
									</li>
								
								<? } ?>
							
							<? } ?>
						
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