<?php
add_action('wp_ajax_podcast_details', 'podcast_details_callback');
add_action('wp_ajax_nopriv_podcast_details', 'podcast_details_callback');
function podcast_details_callback() {
	
	$post_id = $_POST['id'];
	
	$PC = new PodcastDirectory;

	$podcast = $PC->GetPodcastByID( $post_id );	
	
	$meta = get_post_meta( $post_id );
	
	$user_reviews = $PC->UserReviewsByPodcast( $post_id );
	
	$podcast_db = $PC->GetPodcastFromDB( $meta['database_id'][0] );
					
	$image = '/wp-content/uploads/podcast_images/podcast_db_'.$podcast_db['id'].'.jpg';
	if( !file_exists(  $_SERVER['DOCUMENT_ROOT'].$image ) ) {
		$image = '/wp-content/uploads/podcast_images/podcast_db_'.$podcast_db['id'].'.png';
	}
	
	if( !file_exists(  $_SERVER['DOCUMENT_ROOT'].$image ) ) {
		$image = false;
	}	
	
	$audio_url = wp_get_attachment_url( $meta['audio'][0] );
	
	/*echo "<pre>";
	print_r($podcast_db);
	echo "</pre>";*/

	ob_start();
	?>
	 
		<div class="top">
			<div class="left">
				<? if( $image ) { ?>
					<img src="<?=$image;?>" />
				<? } ?>
			</div>
			<div class="right">
				<p class="title">
					<? echo $podcast['post_title']; ?>
				</p>
				
				<hr />
				
				<?
				$rating = get_post_meta( $post_id, 'rating' )[0];
				$revies_count = get_post_meta( $post_id, 'reviews_count' )[0];
				?>
				
				<? if( $revies_count > 0 ) { ?>
				
					<div class="rating-block">

						<div class="left">
							<p> <? echo number_format((float)$rating, 1, '.', ''); ?> </p>
						</div>
					
						<div class="center">

							<? if( $revies_count > 0 ) { ?>
								<? echo $PC->ShowRatingHTML( $rating ); ?>
							<? } ?>
							
						</div>
						
						<div class="right">
							<? echo $revies_count; ?> RATINGS
						</div>

					</div>
				
				<? } else { ?>
				
					<br/>
					<br/>
				
				<? } ?>
				
				<script>
				
					jQuery(document).ready(function($) {
						$('.popup-leave-review').magnificPopup({
							type: 'inline'
						});
					});
				
				</script>
				
				<div class="buttons-block">
				
					<? if( is_user_logged_in() ) { ?>
					
						<? if( count( $user_reviews ) == 0 ) { ?>
							<a href="#popup-leave-review" class="popup-leave-review submit-review-link" data-id="<?=$post_id;?>" onclick="openReviewPopup(<?=$post_id;?>);">
								<button class="btnpd"><i class="fa fa-send"></i> Submit A Review</button>
							</a>
							
							<?/*?>
							<a href="#popup-leave-review" class="popup-leave-review submit-review-link" data-id="<?=$post_id;?>" onclick="openReviewPopup(<?=$post_id;?>);"> Submit a review </a>
							<?*/?>
							
						<? } ?>
						
					<? } else{ ?>
						<a href="/login/" class="submit-review-link">
							<button class="btnpd"><i class="fa fa-send"></i> Submit A Review</button>
						</a>
					<? } ?>
					
					<? if( $revies_count > 0 ) { ?>
						<a href="/podcast-reviews/?podcast=<?=$post_id;?>" target="_blank">
							<button class="btnpd"><i class="fa fa-book"></i> Read All Reviews</button>
						</a>
					<? } ?>
					
					<audio src="https://dev.warcraftradio.com/wp-content/uploads/2021/01/English-Languagecast-feat.-Capn-Tuni-%E2%80%94-Learn-English-Podcast_-Christmas-Episode-Intro-feat.-Capn-Tuni.mp3" autoplay loop></audio>
				
				</div>
				
				<? if( $audio_url ) { ?>
					<p class="audio-title">Preview this show</p>
					<div class="audio-player">
						<? echo do_shortcode('[audio mp3="'.$audio_url.'"]'); ?>
					</div>
				<? } ?>
				
			</div>
			<div style="clear: both;"></div>
		</div>
		
		<hr class="popup-middle" />
		
		<div class="popup-content">
		
			<div class="text">
				<? echo $podcast['post_content']; ?>
			</div>
			
			<div class="stats">
				<div class="block">
					<p class="yellow">
						<i class="fas fa-users" style="margin-right: 3px;     color: #f0efef;"></i>
						Hosted By
					</p>
					<p class="value">
						<? if( $meta['itunes_author'][0] ) { ?>
							<? echo $meta['itunes_author'][0]; ?>
						<? } else { ?>
							<? echo $podcast_db['itunes_author']; ?>
						<? } ?>
					</p>
				</div>
				
				<? if( $meta['liverecordingurl'][0] ) { ?>
					<div class="block">
						<p class="yellow">
							<i class="fas fa-play" style="margin-right: 3px;     color: #f0efef;"></i>
							Live Recordings
						</p>
						<p class="value">
							<a href="<? echo $meta['liverecordingurl'][0]; ?>" target="_blank">
								Visit
							</a>
						</p>
					</div>
				<? } ?>
				
				<div class="block">
					<p class="yellow">
						<i class="far fa-check-square" style="margin-right: 3px;    color: #f0efef;"></i>
						Status
					</p>
					<p class="value">
						<? echo $podcast['STATUS']; ?>
					</p>
				</div>
			</div>
			
		</div>
		
		<div class="popup-bottom">
			
			<div class="left">
				<p class="title">Listen via</p>
				<div class="links">
				
					<? if( $meta['rssfeed'][0] ) { ?>
						<span class="block">
							<i class="fas fa-rss"></i>
							<a href="<? echo $meta['rssfeed'][0]; ?>" target="_blank">
								<span class="text">RSS Feed</span>
							</a>
						</span>
					<? } ?>
					
					<? if( $podcast_db['website'] ) { ?>
						<span class="block">
							<i class="fas fa-globe"></i>
							<a href="<? echo $podcast_db['website']; ?>" target="_blank">
								<span class="text">Website</span>
							</a>	
						</span>
					<? } ?>
					
					<?/*?>
					<? if( $podcast_db['RSSFeed'] ) { ?>
						<span class="block">
							<i class="fab fa-spotify"></i>
							<a href="<? echo $podcast_db['RSSFeed']; ?>" target="_blank">
								<span class="text">Spotify</span>
							</a>	
						</span>
					<? } ?>
					<?*/?>
					
					<? if( $meta['twitch'][0] ) { ?>
						<span class="block">
							<i class="fab fa-twitch"></i>
							<a href="<? echo $meta['twitch'][0]; ?>" target="_blank">
								<span class="text">Twitch.tv</span>
							</a>	
						</span>
					<? } ?>
					
					<? if( $meta['youtube'][0] ) { ?>
						<span class="block">
							<i class="fab fa-youtube"></i>
							<a href="<? echo $meta['youtube'][0]; ?>" target="_blank">
								<span class="text">Youtube</span>
							</a>	
						</span>
					<? } ?>
					
				</div>
			</div>
			
			<div class="right">
				<p class="title">Follow them on</p>
				<div class="links">
				
					<? if( $meta['twitter'][0] ) { ?>
						<span class="block">
							<i class="fab fa-twitter"></i>
							<a href="<? echo $meta['twitter'][0]; ?>" target="_blank">
								<span class="text">Twitter</span>
							</a>	
						</span>
					<? } ?>
					
					<? if( $meta['discord'][0] ) { ?>
						<span class="block" style="margin-right: 0;">
							<i class="fab fa-discord"></i>
							<a href="<? echo $meta['discord'][0]; ?>" target="_blank">
								<span class="text">Discord</span>
							</a>	
						</span>
					<? } ?>
					
					<? if( $meta['facebook'][0] ) { ?>
						<span class="block" style="margin-right: 0;">
							<i class="fab fa-facebook"></i>
							<a href="<? echo $meta['facebook'][0]; ?>" target="_blank">
								<span class="text">Facebook</span>
							</a>	
						</span>
					<? } ?>
					
					<? if( $meta['instagram'][0] ) { ?>
						<span class="block" style="margin-right: 0;">
							<i class="fab fa-instagram"></i>
							<a href="<? echo $meta['instagram'][0]; ?>" target="_blank">
								<span class="text">Instagram</span>
							</a>	
						</span>
					<? } ?>
					
					<? if( $meta['patreon'][0] ) { ?>
						<span class="block" style="margin-right: 0;">
							<i class="fab fa-patreon"></i>
							<a href="<? echo $meta['patreon'][0]; ?>" target="_blank">
								<span class="text">Patreon</span>
							</a>	
						</span>
					<? } ?>
					
				</div>
			</div>
			
			<div style="clear: both;"></div>
			
		</div>
		
		<button title="Close (Esc)" type="button" class="mfp-close">×</button>
		
	 
	<?
	$content = ob_get_contents();
	ob_end_clean();
	
	echo $content;

	wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}


add_action('wp_ajax_podcast_edit', 'podcast_edit_callback');
add_action('wp_ajax_nopriv_podcast_edit', 'podcast_edit_callback');
function podcast_edit_callback() {
	
	$post_id = $_POST['id'];
	
	$PC = new PodcastDirectory;

	$podcast = $PC->GetPodcastByID( $post_id );	
	$meta = get_post_meta( $post_id );
	
	$field_code = 'tags';
	$tags = $PC->GetSelectACF( $field_code );
	
	$post_tags = $PC->GetPodcastTags( $post_id );
	
	//print_r($podcast);
	
	ob_start();
	?>
	
		<h1>Edit podcast - <?=$podcast['post_title'];?></h1>
	 
		<form action="" enctype="multipart/form-data" method="post" class="edit-podcast">
		
			<input type="hidden" name="action" value="update_podcast" />
			<input type="hidden" name="ID" value="<? echo $post_id; ?>" />
			
			<div class="block">
				<p>Youtube</p>
				<input type="text" name="youtube" value="<? echo $meta['youtube'][0] ?>" />
			</div>	
			<div class="block">
				<p>Discord</p>
				<input type="text" name="discord" value="<? echo $meta['discord'][0] ?>" />
			</div>	
			<div class="block">
				<p>Twitter</p>
				<input type="text" name="twitter" value="<? echo $meta['twitter'][0] ?>" />
			</div>
			<div class="block">
				<p>LiveRecordingURL</p>
				<input type="text" name="liverecordingurl" value="<? echo $meta['liverecordingurl'][0] ?>" />
			</div>	
			<div class="block">
				<p>Twitch</p>
				<input type="text" name="twitch" value="<? echo $meta['twitch'][0] ?>" />
			</div>	
			<div class="block">
				<p>RSSFeed</p>
				<input type="text" name="rssfeed" value="<? echo $meta['rssfeed'][0] ?>" />
			</div>
			<div class="block">
				<p>Website</p>
				<input type="text" name="website" value="<? echo $meta['website'][0] ?>" />
			</div>	
			<div class="block">
				<p>Hosted by</p>
				<input type="text" name="itunes_author" value="<? echo $meta['itunes_author'][0] ?>" />
			</div>	
			<div class="block">
				<p>Facebook</p>
				<input type="text" name="facebook" value="<? echo $meta['facebook'][0] ?>" />
			</div>	
			<div class="block">
				<p>Instagram</p>
				<input type="text" name="instagram" value="<? echo $meta['instagram'][0] ?>" />
			</div>
			<div class="block">
				<p>Patreon</p>
				<input type="text" name="patreon" value="<? echo $meta['patreon'][0] ?>" />
			</div>
			<div class="block">
				<p>Contains Explicit Content</p>
				<input type="checkbox" name="explicit" value="1" <? if( $meta['explicit'][0] ) { ?> checked <? } ?> />
			</div>
			
			<div class="block" style="width: 100%; height: auto;">
			
				<?
				$audio_url = wp_get_attachment_url( $meta['audio'][0] );
				$audio_name = basename( $audio_url );
				?>
			
				<p>Audio</p>
				<input name="audio" type="file" accept="audio/mpeg3" />
				
				<input name="remove_audio" type="hidden" value="" />
				
				<? if( $audio_name ) { ?>
					<p class="audio-name"> 
						<? echo $audio_name; ?>  
						<a href="#" class="remove-audiofile"> (remove) </a>
					</p>
				<? } ?>
				
			</div>
			
			<div class="block tags">
				<p>Tags (up to 4)</p>
				
				<ul class="filter-tags">
				
					<? foreach( $tags as $key=>$tag ) { ?>
					
						<li>
							<input type="checkbox" name="tags[]" value="<? echo $tag; ?>" id="filter-tag-<?=$key;?>"
							<? if( in_array( $tag, $post_tags ) ) { ?> checked <? } ?>
							/>
							<label for="filter-tag-<?=$key;?>"><? echo $tag; ?></span>
						</li>
					
					<? } ?>
					
					<div style="clear: both;"></div>
				
				</ul>
				
			</div>	
			
			<div class="block" style="width: 100%; margin-bottom: 20px; height: auto;">
				<p>Show description</p>
				
				<textarea name="description" style="height: 200px;"><? echo $podcast['post_content']; ?></textarea>
				
			</div>
			
			<div style="clear: both;"></div>
			
			<div class="block">
				<button class="btnpd"><i class="fa fa-send"></i> Update </button>
				<!--<input type="submit" value="Update" />-->
			</div>
			
			<br/>
			<br/>
			
		</form>	
		
		<button title="Close (Esc)" type="button" class="mfp-close">×</button>
		
	 
	<?
	$content = ob_get_contents();
	ob_end_clean();
	
	echo $content;

	wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}


add_action('wp_ajax_podcast_edit_review', 'podcast_edit_review_callback');
add_action('wp_ajax_nopriv_podcast_edit_review', 'podcast_edit_review_callback');
function podcast_edit_review_callback() {
	
	$post_id = $_POST['id'];
	
	$PC = new PodcastDirectory;

	if( $post_id ) {
		$review = $PC->GetReviewByID( $post_id );	
		//print_r($review);
	}

	ob_start();
	?>
	
		<? if( !$PC->canUserEditReview( $post_id ) || !$post_id ) { ?>
		
			<p class="no-rights">No access, genius</p>
		
		<? } else { ?>
	 
			<h1>Edit review #<?=$post_id;?></h1>
			
			<p class="success" style="text-align: center; display: none;">Your review has been edited!</p>
			
			<form action="" method="POST" class="form-review">
			
				<input type="hidden" name="action" value="edit_review" />
				<input type="hidden" name="ID" value="<? echo $review['ID']; ?>" />
				
				<div class="block">
				
					<p>Leave the stars</p>
				
					<div class="select-stars">
						<input class="star star-5" id="star-5" type="radio" name="star" value="5" />
						<label class="star star-5" for="star-5"></label>
						<input class="star star-4" id="star-4" type="radio" name="star" value="4"/>
						<label class="star star-4" for="star-4"></label>
						<input class="star star-3" id="star-3" type="radio" name="star" value="3"/>
						<label class="star star-3" for="star-3"></label>
						<input class="star star-2" id="star-2" type="radio" name="star" value="2"/>
						<label class="star star-2" for="star-2"></label>
						<input class="star star-1" id="star-1" type="radio" name="star" value="1" />
						<label class="star star-1" for="star-1"></label>
						
						<div style="clear: both;"></div>
						
					</div>
					
				</div>
				
				<div class="block">
				
					<p>Your message</p>
					<textarea name="review"><? echo $review['content']; ?></textarea>
					
					<p class="notice red" style="display: none;"> Minimum 25 letters and maximum 2000 </p>
					<p class="notice-stars red" style="display: none;"> Leave a star rating </p>
					
				</div>	
				
				<div class="block">
					<button class="btnpd"><i class="fa fa-download"></i> Update</button>
				</div>
			
			</form>
			
			<button title="Close (Esc)" type="button" class="mfp-close">×</button>
		
		<? } ?>
	 
	<?
	$content = ob_get_contents();
	ob_end_clean();
	
	echo $content;

	wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}


add_action('wp_ajax_podcast_edit_response', 'podcast_edit_response_callback');
add_action('wp_ajax_nopriv_podcast_edit_response', 'podcast_edit_response_callback');
function podcast_edit_response_callback() {
	
	$post_id = $_POST['id'];
	
	$PC = new PodcastDirectory;

	if( $post_id ) {
		$review = $PC->GetReviewByID( $post_id );	
		//print_r($review);
	}

	ob_start();
	?>
	
		<? if( !$PC->canUserEditReview( $post_id ) || !$post_id ) { ?>
		
			<p class="no-rights">No access, genius</p>
		
		<? } else { ?>
	 
			<h1>Edit response #<?=$post_id;?></h1>
			
			<p class="success" style="text-align: center; display: none;">Your response has been edited!</p>
			
			<form action="" method="POST" class="form-review">
			
				<input type="hidden" name="action" value="edit_response" />
				<input type="hidden" name="ID" value="<? echo $review['ID']; ?>" />
				
				<div class="block">
				
					<p>Your message</p>
					<textarea name="review" style="height: 250px;"><? echo $review['content']; ?></textarea>
					
					<p class="notice red" style="display: none;"> Minimum 25 letters and maximum 2000 </p>
					<p class="notice-stars red" style="display: none;"> Leave a star rating </p>
					
				</div>	
				
				<br/>
				
				<div class="block">
					<input type="submit" value="Send!" />
				</div>
			
			</form>
			
			<button title="Close (Esc)" type="button" class="mfp-close">×</button>
		
		<? } ?>
	 
	<?
	$content = ob_get_contents();
	ob_end_clean();
	
	echo $content;

	wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}











?>