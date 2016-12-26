<?php

// data gathering
function add_views($postID) {
	global $wpdb;
	$popular_posts_statistics_table = $wpdb->prefix . 'popular_posts_statistics';
	if (!$wpdb->query("SELECT hit_count FROM $popular_posts_statistics_table WHERE post_id = $postID") && $postID != 1 && !preg_match('/bot|spider|crawler|slurp|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])) { // if hit_count with ID doesn't exists and ID is not equal to 1 and visitor is not a bot, proceed
		$result = $wpdb->query("INSERT INTO $popular_posts_statistics_table (post_id, hit_count, date) VALUES ($postID, 1, NOW())"); // adds to tablle post ID, date and hit count
	}elseif ($postID != 1 && !preg_match('/bot|spider|crawler|slurp|curl|^$/i', $_SERVER['HTTP_USER_AGENT'])) {
		$hitsnumber = $wpdb->get_results("SELECT hit_count FROM $popular_posts_statistics_table WHERE post_id = $postID", ARRAY_A);
		$hitsnumber = $hitsnumber[0]['hit_count'];
		$result = $wpdb->query("UPDATE $popular_posts_statistics_table SET hit_count = $hitsnumber + 1, date =  NOW() WHERE post_id = $postID");
	}
}

// results displaying
function show_views($postID, $posnumber, $numberofdays, $hitsonoff, $ignoredpages, $ignoredcategories, $visitstext, $commentsorvisits) {
	global $wpdb;
	$popular_posts_statistics_table = $wpdb->prefix . 'popular_posts_statistics';
	$posts_table = $wpdb->prefix . 'posts';
	$to_return = '';
	if ($wpdb->query("SELECT hit_count FROM $popular_posts_statistics_table") && $commentsorvisits == 1) {
		$result = $wpdb->get_results("SELECT hit_count FROM $popular_posts_statistics_table WHERE date >= NOW() - INTERVAL $numberofdays DAY ORDER BY hit_count DESC LIMIT $posnumber", ARRAY_A);
		$post_id_number = $wpdb->get_results("SELECT post_id FROM $popular_posts_statistics_table WHERE date >= NOW() - INTERVAL $numberofdays DAY ORDER BY hit_count DESC LIMIT $posnumber", ARRAY_A);
		$to_return = "<ol>";
		for ($i = 0; $i < count($post_id_number); ++$i) {
			$post_number = $post_id_number[$i]['post_id'];
			$post_link = get_permalink($post_number); // get permalink from wordpress database
			$countbeginning = "<br /><span id=\"pp-count\">";
			$countending = "</span></span></li><br />";
			$cat_id = get_the_category($post_number);
			$post_cat_id = $cat_id[0]->cat_ID;
			$post_name_by_id = $wpdb->get_results("SELECT post_title FROM $posts_table WHERE ID = $post_number and 	post_type like 'post'", ARRAY_A);
			if (!$post_name_by_id){ // checks whether post with this ID exists, if not - delete record and break script
				$wpdb->query("DELETE FROM $popular_posts_statistics_table WHERE post_id = $post_number");
				break;
			}
			if (in_array($post_cat_id, $ignoredcategories) || in_array($post_number, $ignoredpages)) { // checks whether post ID or his category ID is not excluded by user
				$cat_or_post_check = TRUE;
			}else {
				$cat_or_post_check = FALSE;
			}
			if ($cat_or_post_check == FALSE) {
				$to_return .= '<li><span id="pp-' . $i . '-title">' . '<a href="' . $post_link . '">' . $post_name_by_id[0]['post_title'] . '</a>';
				if ($hitsonoff) { // if user turned on displaying number of visits
				$to_return .= $countbeginning . $result[$i]['hit_count'] . " " . $visitstext . $countending;
				}else {
					$to_return .= "</span></li><br />";
				}
			}
		}
		$to_return .= "</ol>";
	}elseif($commentsorvisits == 2) { //If user wants rank by comment count
		$posnumber = $posnumber - 1;
		$result = $wpdb->get_results("SELECT comment_count FROM $posts_table WHERE post_date >= NOW() - INTERVAL $numberofdays DAY ORDER BY comment_count DESC LIMIT $posnumber", ARRAY_A);
		$post_id_number = $wpdb->get_results("SELECT ID FROM $posts_table WHERE post_date >= NOW() - INTERVAL $numberofdays DAY ORDER BY comment_count DESC LIMIT $posnumber", ARRAY_A);
		$to_return .= "<ol>";
		for ($i = 0; $i < count($post_id_number); ++$i) {
			$post_number = $post_id_number[$i]['ID'];
			$post_link = get_permalink($post_number); // get permalink from wordpress database
			$countbeginning = "<br /><span id=\"pp-count\">";
			$countending = "</span></span></li><br />";
			$cat_id = get_the_category($post_number);
			$post_cat_id = $cat_id[0]->cat_ID;
			$post_name_by_id = $wpdb->get_results("SELECT post_title FROM $posts_table WHERE ID = $post_number", ARRAY_A);
			if($result[$i]['comment_count'] == 0) {
				break;
			}
			if (in_array($post_cat_id, $ignoredcategories) || in_array($post_number, $ignoredpages)) { // checks whether post ID or his category ID is not excluded by user
				$cat_or_post_check = TRUE;
			}else {
				$cat_or_post_check = FALSE;
			}
			if ($cat_or_post_check == FALSE) {
				$to_return .= '<li><span id="pp-' . $i . '-title">' . '<a href="' . $post_link . '">' . $post_name_by_id[0]['post_title'] . '</a>';
				if ($hitsonoff) { // if user turned on displaying number of visits
					$to_return .= $countbeginning . $result[$i]['comment_count'] . " " . $visitstext . $countending;
				}else {
					$to_return .= "</span></li><br />";
				}
			}
		}
		$to_return .= "</ol>";
		}
	return $to_return;
}

// style selection
function choose_style($css_sel) {
	if($css_sel == 1){
		return 'style-popular-posts-statistics-1.css';
	} elseif($css_sel == 2){
		return 'style-popular-posts-statistics-2.css';
	} elseif($css_sel == 3){
		return 'style-popular-posts-statistics-3.css';
	} elseif($css_sel == 4){
		return 'style-popular-posts-statistics-4.css';
	} elseif($css_sel == 5){
		return 'style-popular-posts-statistics-5.css';
	} elseif($css_sel == 6){
		return 'style-popular-posts-statistics-6.css';
	} elseif($css_sel == 7){
		return 'custom.css';
	} elseif($css_sel == 8){
		return 'style-popular-posts-statistics-1-premium.css';
	} elseif($css_sel == 9){
		return 'style-popular-posts-statistics-2-premium.css';
	} elseif($css_sel == 10){
		return 'style-popular-posts-statistics-3-premium.css';
	} elseif($css_sel == 11){
		return 'style-popular-posts-statistics-4-premium.css';
	} elseif($css_sel == 12){
		return 'style-popular-posts-statistics-5-premium.css';
	} elseif($css_sel == 13){
		return 'style-popular-posts-statistics-6-premium.css';
	}
}

// function responsible for delete of popular_posts_statistics table from database
function clean_up_database() {
	global $wpdb;
	$popular_posts_statistics_table = $wpdb->prefix . 'popular_posts_statistics';
	$wpdb->query("TRUNCATE TABLE $popular_posts_statistics_table");
}

?>