<?php
Class PodcastDirectory {
	
	public $PODCAST_DB_TABLE = 'PodcastDirectory';
	public $full_star = '/wp-content/uploads/icons/star-gold.png';
	public $half_star = '/wp-content/uploads/icons/star-half.png';
	
	
	function UpdatePodcasts() {
		
		global $wpdb;

		$sql = "SELECT * FROM ".$this->PODCAST_DB_TABLE." LIMIT 500";
		$items = $wpdb->get_results( $sql );
		
		$podcasts_raw = $this->GetPodcasts();
		foreach( $podcasts_raw as $item ) {
			$podcasts[ $item->post_title ] = $item;
		}

		/*echo "<pre>";
		print_r($podcasts);
		echo "</pre>";
		die();*/
		
		foreach( $items as $item ) {
			
			$page = $this->GetPodcastByDbID( $item->id );
			
			//echo $page->ID; echo "<br/>";
			//continue;
			
			if( $page->ID ) { continue; }
			
			$post_data = array(
				'post_title'    => wp_strip_all_tags( $item->title ),
				'post_content'  => $item->description,
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'   => 'podcasts',
				//'post_category' => array( 8,39 )
			);
			
			if( $page->ID ) {
				$post_data['ID'] = $page->ID;
			}
			
			if( $item->itunes_explicit == 'yes' ) {
				$explicit = 1;
			} else { $explicit = 0; }

			// Вставляем запись в базу данных
			$post_id = wp_insert_post( $post_data );
			
			if( !$page->ID ) {
				
				update_post_meta( $post_id, 'category', $item->Category );
				update_post_meta( $post_id, 'link', $item->link );
				update_post_meta( $post_id, 'explicit', $explicit );
				
				update_post_meta( $post_id, 'youtube', $item->Youtube );
				update_post_meta( $post_id, 'discord', $item->Discord );
				update_post_meta( $post_id, 'twitter', $item->Twitter );
				update_post_meta( $post_id, 'liverecordingurl', $item->LiveRecordingURL );
				update_post_meta( $post_id, 'twitch', $item->Twitch );
				update_post_meta( $post_id, 'rssfeed', $item->RSSFeed );
				update_post_meta( $post_id, 'website', $item->Website );
				update_post_meta( $post_id, 'database_id', $item->id );
			
			}
			
		}
		
	}
	
	
	function GetPodcastByDbID( $db_id ) {
		
		$args = array(
			'numberposts' => 500,
			'category'    => 0,
			'orderby'     => 'date',
			'order'       => 'desc',
			'include'     => array(),
			'exclude'     => array(),
			'meta_key'    => 'database_id',
			'meta_value'  => $db_id,
			'post_type'   => 'podcasts',
			'post_status'   => 'publish',
		);

		$items = get_posts( $args )[0];
		
		return $items;
		
	}
	
	
	function ClearPodcasts() {
		
		$items = $this->GetPodcasts();
		
		foreach( $items as $item ) {
			wp_delete_post( $item->ID, true );
		}
		
	}
	
	
	function GetPodcasts( $filter=false, $sorting=false ) {
		
		$args = array(
			'numberposts' => 500,
			'category'    => 0,
			'orderby'     => 'date',
			'order'       => 'desc',
			'include'     => array(),
			'exclude'     => array(),
			//'meta_key'    => 'tags',
			//'meta_value'  => array('Humor'),
			'post_type'   => 'podcasts',
			'post_status'   => 'publish',
			//'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
		);
		
		/*
		echo "<pre>";
		print_r( $args );
		echo "</pre>";
		*/
		
		if( $sorting ) {
			$args['orderby'] = $sorting['orderby'];
			$args['order'] = $sorting['order'];
		}
		
		if( $filter ) {
			/*$args['meta_query'] = array(
				'relation' => 'AND',
				array(
						'key' => 'tags',
						//'value' => array('Humor'),
						'value' => 'Humor',
						'compare' => 'LIKE'
						),
				array(
						'key' => 'category',
						'value' => 'General Blizzard',
						'compare' => '='
						),
				array(
						'key' => 'explicit',
						'value' => 1,
						'compare' => '='
						),
            );*/
			
			$args['meta_query']['relation'] = 'AND';
			
			foreach( $filter as $item ) {
				$args['meta_query'][] = $item;
			}
			
		}
		
		$items = get_posts( $args );

		return $items;
		
	}
	
	
	function UserReviewsByPodcast( $podcast_id ) {
		
		$args = array(
			'numberposts' => 500,
			'category'    => 0,
			'orderby'     => 'date',
			'order'       => 'desc',
			'include'     => array(),
			'exclude'     => array(),
			'post_type'   => 'podcast_reviews',
		);
		
		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
					'key' => 'podcast_id',
					'value' => $podcast_id,
					'compare' => '='
					),
			array(
					'key' => 'user_id',
					'value' => get_current_user_id(),
					'compare' => '='
					),
		);
		
		$args['meta_query']['relation'] = 'AND';
		
		foreach( $filter as $item ) {
			$args['meta_query'][] = $item;
		}
		
		$items = get_posts( $args );

		return $items;
		
	}
	
	
	function GetReviewsByPodcast( $podcast_id ) {
		
		$reviews = array();
		
		if( !$podcast_id ) {
			return $reviews;
		}
		
		$items = get_posts( array(
			'numberposts' => 500,
			'category'    => 0,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'include'     => array(),
			'exclude'     => array(),
			'meta_key'    => 'podcast_id',
			'meta_value'  => $podcast_id,
			'post_type'   => 'podcast_reviews',
			'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
		) );
		
		foreach( $items as $item ) {
			
			$item_parent_id = get_post_meta( $item->ID, "parent_id" )[0];
			
			if( $item_parent_id ) { continue; }
			
			$reviews[] = array(
								"ID" => $item->ID,
								"title" => $item->post_title,
								"content" => $item->post_content,
								"post_date" => $item->post_date,
								"podcast_id" => get_post_meta( $item->ID, "podcast_id" )[0],
								"rating" => get_post_meta( $item->ID, "rating" )[0],
								"user_id" => get_post_meta( $item->ID, "user_id" )[0],
								"parent_id" => $item_parent_id,
								);
			
		}

		return $reviews;
		
	}
	
	
	function GetChildReviews( $review_id ) {
		
		$reviews = array();
		
		if( !$review_id ) {
			return $reviews;
		}
		
		$items = get_posts( array(
			'numberposts' => 500,
			'category'    => 0,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'include'     => array(),
			'exclude'     => array(),
			'meta_key'    => 'parent_id',
			'meta_value'  => $review_id,
			'post_type'   => 'podcast_reviews',
			'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
		) );
		
		foreach( $items as $item ) {
			
			$reviews[] = array(
								"ID" => $item->ID,
								"title" => $item->post_title,
								"content" => $item->post_content,
								"post_date" => $item->post_date,
								"podcast_id" => get_post_meta( $item->ID, "podcast_id" )[0],
								"rating" => get_post_meta( $item->ID, "rating" )[0],
								"user_id" => get_post_meta( $item->ID, "user_id" )[0],
								"parent_id" => $item_parent_id,
								);
			
		}

		return $reviews;
		
	}
	
	
	function GetReviewByID( $review_id ) {
		
		$item = get_post( $review_id );
		
		$review = array(
							"ID" => $item->ID,
							"title" => $item->post_title,
							"content" => $item->post_content,
							"post_date" => $item->post_date,
							"podcast_id" => get_post_meta( $item->ID, "podcast_id" )[0],
							"rating" => get_post_meta( $item->ID, "rating" )[0],
							"user_id" => get_post_meta( $item->ID, "user_id" )[0],
							);

		return $review;
		
	}
	
	
	function GetPodcastByID( $ID ) {
		
		$post = get_post( $ID, ARRAY_A );
		
		$post['RATING'] = $this->GetPostRating( $post['ID'] );
		
		$post['DB'] = $this->GetPodcastFromDB( $post['post_title'] );
		
		// Active or not, expires period is 6 months
		$post['STATUS'] = 'Active';
		
		if( $post['DB']['lastbuilddate'] != '' ) {
			$seconds = 60 * 60 * 24 * 30 * 6;
			$diff = time() - strtotime( $post['DB']['lastbuilddate'] );
			
			if( $diff > $seconds ) {
				$post['STATUS'] = 'Active';
			} else {
				$post['STATUS'] = 'Inactive';
			}
			
		}
		
		// Tags
		$tags_raw = get_post_meta( $post['ID'], 'tags' )[0];
		foreach( $tags_raw as $item ) {
			
			$tag_code = strtolower( str_replace( ' ', '', $item ) );
			
			$tags[ $tag_code ] = $item;
		}
		
		
		$post['TAGS'] = $tags;
		
		/*echo "<pre>";
		print_r($post);
		echo "</pre>";*/

		return $post;
		
		
		
	}
	
	
	function GetPostRating( $post_id ) {
		
		return array(
					"mark" => rand(1, 5),
					"amount" => rand(10, 50),
					);
		
	}
	
	
	function GetPodcastFromDB( $id ) {
		
		global $wpdb;

		$sql = "SELECT * FROM ".$this->PODCAST_DB_TABLE." WHERE `id`='".$id."' ";
		$item = $wpdb->get_results( $sql, ARRAY_A )[0];
		
		return $item;
		
	}
	
	
	function GetSelectACF( $field_code ) {
		
		global $wpdb;
		
		$sql = "SELECT * FROM ".$wpdb->posts." WHERE `post_type`='acf-field' AND `post_excerpt`='".$field_code."' ";
		$raw = $wpdb->get_results( $sql, ARRAY_A )[0];
		$items = unserialize( $raw['post_content'] )['choices'];
		
		
		return $items;
		
		
	}
	
	
	function ShowRatingHTML( $count ) {
		
		$full_star = '<img src="'.$this->full_star.'" alt="" width="16" height="16">';
		$half_star = '<img src="'.$this->half_star.'" alt="" width="16" height="16">';;
		
		$html .= '<div class="rating">';

		//$count = 4.5;
		
		if( $count >= 4.75 ) {
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
		}
		
		if( $count >= 4.25 && $count <=4.74 ) {
			$html .= $half_star;
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
			
		}	
		
		if( $count >= 3.75 && $count <=4.24 ) {
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
		}	
		
		if( $count >= 3.25 && $count <=3.74 ) {
			$html .= $half_star;
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
		}
		
		if( $count >= 2.75 && $count <=3.24 ) {
			$html .= $full_star;
			$html .= $full_star;
			$html .= $full_star;
		}
		
		if( $count >= 2.25 && $count <=2.74 ) {
			$html .= $half_star;
			$html .= $full_star;
			$html .= $full_star;
		}
		
		if( $count >= 1.75 && $count <=2.24 ) {
			$html .= $full_star;
			$html .= $full_star;
		}
		
		if( $count >= 1.25 && $count <=1.74 ) {
			$html .= $half_star;
			$html .= $full_star;
			
		}
		
		if( $count < 1.25 ) {
			$html .= $full_star;
		}
		
		/*
		while ( $count > 0)
		{
			$html .= '<img src="https://dev.warcraftradio.com/wp-content/uploads/2019/11/featured.png" alt="" width="12" height="12">';
			$count--;
		}
		*/
		
		$html .= '</div>';	
			
		return $html;			
		
	}
	
	
	function GetPodcastTags( $ID ) {
		
		$tags_raw = get_post_meta( $ID, 'tags' )[0];
		foreach( $tags_raw as $item2 ) {
			
			$tag_code = strtolower( str_replace( ' ', '', $item2 ) );
			
			$tags[ $tag_code ] = $item2;
		}
		
		return $tags;
		
	}
	
	
	function canUserEditReview( $review_id ) {
		
		$post = get_post( $review_id );
		
		if( $post->post_author == get_current_user_id() ) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
	function isUserAdmin() {
		
		return current_user_can('administrator');
		
	}
	
	
	function isUserShowOwner() {
		
		$meta = get_user_meta( get_current_user_id() );
		
		if( $meta['show_owner'][0] ) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
	function GetShortcodePages() {
		
		global $wpdb;
		
		$arr['my_shows'] = '[my_shows]'; 
		$arr['podcast_reviews'] = '[podcast_reviews]'; 
		$arr['all_shows'] = '[all_shows]'; 
		
		foreach( $arr as $key=>$item ) {
			
			$sql = "SELECT * FROM $wpdb->posts WHERE post_content LIKE '%".$item."%' AND post_type='page' ";
			//echo $sql;
			
			$post = $wpdb->get_results( $sql )[0];
			
			$arr[ $key ] = get_permalink( $post->ID );
			
			
		}
		
		return $arr;
		
	}
	
	
	function GetTagsProp() {
		
		$items = get_posts( array(
			'numberposts' => 500,
			'category'    => 0,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'include'     => array(),
			'exclude'     => array(),
			'post_type'   => 'tags',
		) );
		
		foreach( $items as $item ) {
			
			$tags[ $item->post_title ] = array(
								"ID" => $item->ID,
								"title" => $item->post_title,
								"background" => get_post_meta( $item->ID, "background" )[0],
								"font_color" => get_post_meta( $item->ID, "font_color" )[0],
								);
			
		}
		
		return $tags;
		
	}
	
	
	function GetCategoriesProp() {
		
		$items = get_posts( array(
			'numberposts' => 500,
			'category'    => 0,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'include'     => array(),
			'exclude'     => array(),
			'post_type'   => 'podcast_categories',
		) );
		
		foreach( $items as $item ) {
			
			$cats[ $item->post_title ] = array(
								"ID" => $item->ID,
								"title" => $item->post_title,
								"icon" => get_post_meta( $item->ID, "icon" )[0],
								);
			
		}
		
		return $cats;
		
	}
	
	
}


if( $_GET['action'] == 'update-podcast' ) {
	
	$PC = new PodcastDirectory;
	
	$PC->UpdatePodcasts();
	
}








?>