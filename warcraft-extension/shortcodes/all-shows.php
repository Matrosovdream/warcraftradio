<?php
add_shortcode( 'all_shows', 'all_shows_func' );
function all_shows_func( $atts ) {
	
	$PC = new PodcastDirectory;
	
	if( $_REQUEST['sections'] && $_REQUEST['sections'] != 'Blizzard - all' ) {
		$filter[] = array(
						'key' => 'category',
						'value' => $_REQUEST['sections'],
						'compare' => '='
						);
	}
	if( $_REQUEST['explicit'] ) {
		$filter[] = array(
						'key' => 'explicit',
						'value' => 1,
						'compare' => '!='
						);
	}
	
	$sorting = array();
	if( $_REQUEST['sort_by'] ) {
		if( $_REQUEST['sort_by'] == 'Most recent' ) {
			$sorting = array(
							'orderby'     => 'date',
							'order'       => 'desc',
							);
		}
		if( $_REQUEST['sort_by'] == 'Alphabetically' ) {
			$sorting = array(
							'orderby'     => 'title',
							'order'       => 'asc',
							);
		}
	}
	
	$podcasts = $PC->GetPodcasts( $filter, $sorting );
	
	if( $_REQUEST['tag'] ) {
		
		/*$filter[] = array(
						'key' => 'tags',
						'value' => $_REQUEST['tags'],
						//'value' => 'Humor',
						'compare' => 'LIKE'
						);*/
		
		// Looks stupid, I know
		foreach( $podcasts as $key=>$item ) {
			
			$tags = $PC->GetPodcastTags( $item->ID );
			
			if( !array_intersect($tags, $_REQUEST['tag']) ) {
				unset( $podcasts[ $key ] );
			}

		}	
	
	}
	

	$field_code = 'category';
	$categories = $PC->GetSelectACF( $field_code );

	$field_code = 'tags';
	$tags = $PC->GetSelectACF( $field_code );
	
	$pages = $PC->GetShortcodePages();
	
	$tag_props = $PC->GetTagsProp();
	$cats_props = $PC->GetCategoriesProp();
	
	//print_r( $podcasts );

	ob_start(); 
	?>
		

		<div class="podcast-directory-wrapper">
		
			<? if( $PC->isUserShowOwner() ) {  ?>
				
				<a class="to-my-shows" href="<? echo $pages['my_shows']; ?>">
					<button class="btnpd"><i class="fa fa-angle-double-right"></i> Update My Show Profiles </button>
				</a>
			<? } ?>

			<form class="podcast-filter"> 
			
				<input type="hidden" name="action" value="apply_filter" />

				<div class="filter">
				
					<div class="block-left">
					
						<p class="title">
							Find <span class="yellow">your</span> perfect podcast:
						</p>
						
						<span>SORT BY</span>
						<select name="sort_by">
							<option name="alphabet"> Alphabetically </option>
							<option name="recent"> Most recent </option>
						</select>
						
						<span>GAMES</span>
						<select name="sections">
							<option> Blizzard - all </option>
							
							<? foreach( $categories as $cat ) { ?>
								<option value="<?=$cat;?>"
									<? if( $cat == $_REQUEST['sections'] ) { ?> selected <? } ?>
								> <?=$cat;?> </option>
							<? } ?>
							
						</select>
						
						<br/>
						<br/>
						<span class="explicit-title">Contains Explicit Content: </span>
						
						<div class="container">
							<div class="switch white">

								<input type="radio" name="explicit" value="on" id="switch-off">
								<input type="radio" name="explicit" value="" id="switch-on" checked>

								<label for="switch-off">Off</label>
								<label for="switch-on">On</label>

								<span class="toggle"></span>

							</div> <!-- end switch -->
						</div> <!-- end container -->
						
						<div style="clear: both;"></div>
						
						<?/*?>
						<input type="checkbox" name="explicit" value="on" class="explicit-checkbox"
							<? if( $_REQUEST['explicit'] ) { ?> checked <? } ?>
						/>
						<div style="clear: both;"></div>
						<?*/?>
						
						
						<p class="note">
							<span class="yellow">Note </span> 
							If a podcast is not tagged explicit, but you feel it should be, 
							please <a href="/contact/"> <span class="blue">contact us</span> </a> and we will be sure to take a look and tag if necessary!
							
							<!--
							if a podcast is not tagged explicit, but you feel it should be, please 
							<a href="/contact/"> <span class="blue">contact us</span> </a>
							and we will be sure to take a look and tag if necassary!
							-->
							
						</p>
					
					</div>
					
					<div class="block-right">
				
						<p class="filter-right-title">
							<span style="font-size: 18px;">TAGS </span>
							<span style="font-size: 14px;">(Select all to see all tags, or select only those you want)</span>
						</p>
						
						<?
						/*$tags[] = 'Show all';
						$tags[] = 'PvP';
						$tags[] = 'End Game';
						$tags[] = 'Humor';
						$tags[] = 'Casual';
						$tags[] = 'lore & Story';*/
						
						/*
						echo "<pre>";
						print_r( $_REQUEST );
						echo "</pre>";
						*/
						?>
						
						<ul class="filter-tags">
						
							<? foreach( $tags as $key=>$tag ) { ?>
							
								<li>
									<input type="checkbox" name="tag[]" value="<? echo $tag; ?>" id="filter-tag-<?=$key;?>"
									<? if( in_array( $tag, $_REQUEST['tags'] ) ) { ?> checked <? } ?>
									/>
									<label for="filter-tag-<?=$key;?>"><? echo $tag; ?></span>
								</li>
							
							<? } ?>
							
							<div style="clear: both;"></div>
						
						</ul>
				
					</div>
					
					<div class="block-player">
					
						<div class="left">
						
							<p style="text-align: center;">
								<img loading="lazy" class="wp-image-3240 aligncenter" style="text-align: center;" 
								src="https://warcraftradio.com/wp-content/uploads/2019/11/FM_LOGO_GRAD.png" alt="" 
								width="100" height="100"
								srcset="https://warcraftradio.com/wp-content/uploads/2019/11/FM_LOGO_GRAD.png 250w, https://warcraftradio.com/wp-content/uploads/2019/11/FM_LOGO_GRAD-150x150.png 150w" 
								sizes="(max-width: 100px) 100vw, 100px">
							</p>
						
					
							<p style="text-align: center;" class="left-text">
								POWERED BY
								<br>
								<a href="/shows/realm-maintenance/">
								<span style="color: #ffcc00;">REALM </span>
								<span style="color: #ffcc00;">MAINTENANCE</span></a>
							</p>
						
						</div>
					
						<div class="right">
							<p class="right-text">Latest <span style="color: #ffcc00;">Episode</span></p>
							
							<iframe style="border: solid 1px #dedede;" src="https://app.stitcher.com/splayer/f/27020/78514055" width="220" height="100" frameborder="0" scrolling="no"></iframe>
							
							<!--
							<iframe src="https://anchor.fm/realm-maintenance/embed" height="102px" width="400px" frameborder="0" scrolling="no"></iframe>
							-->
							
						</div>
						
						<div style="clear: both;"></div>
					
					</div>
					
					<div style="clear: both;"></div>
				
				</div>
			
			</form>

			<ul class="podcasts">
			
				<? foreach( $podcasts as $item ) { ?>
				
					<?
					$tags = $PC->GetPodcastTags( $item->ID );
					
					$podcast_db = $PC->GetPodcastFromDB( $item->post_title );					
					$image = '/wp-content/uploads/podcast_images/podcast_db_'.$podcast_db['id'].'.jpg';
					if( !file_exists(  $_SERVER['DOCUMENT_ROOT'].$image ) ) {
						$image = '/wp-content/uploads/podcast_images/podcast_db_'.$podcast_db['id'].'.png';
					}
					
					$cat_name = get_post_meta( $item->ID, 'category' )[0];
					$cat_svg = $cats_props[ $cat_name ]['icon'];
					
					
					//$cat = 
					?>
				
					<li>
						<div class="img">
							<img src="<?=$cat_svg;?>" />
							
							<svg width="6px" height="12px">
								<use xlink:href="<?=$cat_svg;?>"></use>
							</svg>
							
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
								
								<!--
								<span class="podcast-tag humor">Humor</span>
								<span class="podcast-tag casual">Casual</span>
								<span class="podcast-tag esports">Esports</span>
								<span class="podcast-tag endgame">End game</span>
								-->
								
							</p>
							
						</div>
						<div class="rate">
						
							<?
							$rating = get_post_meta( $item->ID, 'rating' )[0];
							$revies_count = get_post_meta( $item->ID, 'reviews_count' )[0];
							?>
						
							<? if( $revies_count > 0 ) { ?>
						
								<? echo $PC->ShowRatingHTML( $rating ); ?>
								<p class="text"> <? echo $revies_count; ?> ratings</p>
							
							<? } else { ?>
								<p class="text"></p>
							<? } ?>
							
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
	
	
	<?
	$html = ob_get_contents();
	ob_end_clean();
	
	return $html;

}
?>