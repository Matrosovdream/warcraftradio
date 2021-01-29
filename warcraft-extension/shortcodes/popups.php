<?php
function wp_footer_warcraft() {
	
	ob_start(); 
	?>
	
		<div id="text-popup" class="white-popup mfp-hide podcast-single-popup"></div>

		<div id="popup-edit-review" class="white-popup mfp-hide podcast-single-popup"></div>
		
		<div id="popup-edit-podcast" class="white-popup mfp-hide podcast-single-popup"></div>
		
		<div id="popup-edit-response" class="white-popup mfp-hide podcast-single-popup"></div>

		<? if( is_user_logged_in() ) { ?>

			<div id="popup-leave-review" class="white-popup mfp-hide podcast-single-popup">

				<h1>Leave review</h1>
				
				<p class="success" style="text-align: center; display: none;">Your review has been published!</p>
				
				<form action="" method="POST" class="form-review">
				
					<input type="hidden" name="action" value="save_review" />
					<input type="hidden" name="podcast_id" value="" />
					
					<div class="block">
					
						<p>Your Star Rating</p>
					
						<div class="select-stars">
							<input class="star star-5" id="star-5" type="radio" name="star" value="5"/>
							<label class="star star-5" for="star-5"></label>
							<input class="star star-4" id="star-4" type="radio" name="star" value="4"/>
							<label class="star star-4" for="star-4"></label>
							<input class="star star-3" id="star-3" type="radio" name="star" value="3"/>
							<label class="star star-3" for="star-3"></label>
							<input class="star star-2" id="star-2" type="radio" name="star" value="2"/>
							<label class="star star-2" for="star-2"></label>
							<input class="star star-1" id="star-1" type="radio" name="star" value="1"/>
							<label class="star star-1" for="star-1"></label>
							
							<div style="clear: both;"></div>
							
						</div>
						
					</div>
					
					<div class="block">
					
						<p>Your message</p>
						<textarea name="review"></textarea>
						
						<p class="notice red" style="display: none;"> Minimum 25 letters and maximum 2000 </p>
						<p class="notice-stars red" style="display: none;"> Leave a star rating </p>
						
					</div>	
					
					<div class="block">
						<button class="btnpd"><i class="fa fa-send"></i> Submit!</button>
						<!--<input type="submit" value="Send!" />-->
					</div>
				
				</form>

			</div>
			
			
			<div id="popup-response-to-review" class="white-popup mfp-hide podcast-single-popup">

				<h1>Response to Review</h1>
				
				<p class="success" style="text-align: center; display: none;">Your message has been sent!</p>
				
				<form action="" method="POST" class="form-review">
				
					<input type="hidden" name="action" value="response_to_review" />
					<input type="hidden" name="review_id" value="" />
					<input type="hidden" name="podcast_id" value="<? echo $_GET['podcast']; ?>" />
					
					<div class="block">
					
						<p>Your message</p>
						<textarea name="review" style="height: 250px;"></textarea>
						
						<p class="notice red" style="display: none;"> Minimum 25 letters and maximum 2000 </p>
						<p class="notice-stars red" style="display: none;"> Leave a star rating </p>
						
					</div>	
					
					<br/>
					
					<div class="block">
						<input type="submit" value="Send!" />
					</div>
				
				</form>

			</div>
			

		<? } ?>

		<!-- jQuery 1.7.2+ or Zepto.js 1.0+ -->

		<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" />
	
	<?
	$html = ob_get_contents();
	ob_end_clean();
	
	echo $html;
	
}
?>