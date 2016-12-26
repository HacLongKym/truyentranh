<?php
/*
Plugin Name: Most Popular Posts Widget
Plugin URI: http://smartfan.pl/
Description: Most Popular Posts is a widget that is able to display a list of the most popular posts visited/commented by the readers of your site.
Author: Piotr Pesta
Version: 1.2.0
Author URI: http://smartfan.pl/
License: GPL12
*/

include 'functions.php';

$options = get_option('widget_popular_posts_statistics');

register_activation_hook(__FILE__, 'popular_posts_statistics_activate');
register_uninstall_hook(__FILE__, 'popular_posts_statistics_uninstall');
add_shortcode("most-popular-posts", "most_popular_posts_shortcode_handler"); // shortcode hook

// installation and mysql table creation
function popular_posts_statistics_activate() {
	global $wpdb;
	$popular_posts_statistics_table = $wpdb->prefix . 'popular_posts_statistics';
		$wpdb->query("CREATE TABLE IF NOT EXISTS $popular_posts_statistics_table (
		id BIGINT(50) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		post_id BIGINT(50) NOT NULL,
		hit_count BIGINT(50),
		date DATETIME
		);");
}

// if uninstalling - remove mysql table
function popular_posts_statistics_uninstall() {
	global $wpdb;
	$popular_posts_statistics_table = $wpdb->prefix . 'popular_posts_statistics';
	delete_option('widget_popular_posts_statistics');
	$wpdb->query( "DROP TABLE IF EXISTS $popular_posts_statistics_table" );
}

class popular_posts_statistics extends WP_Widget {

	// widget constructor
	function popular_posts_statistics() {

		$widget_ops = array('description' => __('Most Popular Posts is a widget that is able to display a list of the most popular posts visited/commented by the readers of your site.'));
		parent::__construct(false, $name = __('Most Popular Posts Widget', 'wp_widget_plugin'), $widget_ops);

	}

	// widget back end (UI)
	function form($instance) {

	// nadawanie i łączenie defaultowych wartości
		$defaults = array('cachetime' => '0', 'commentsorvisits' => '1', 'cleandatabase' => '', 'visitstext' => 'visit(s)', 'ignoredcategories' => '', 'ignoredpages' => '', 'hitsonoff' => '1', 'cssselector' => '1', 'numberofdays' => '7', 'posnumber' => '5', 'title' => 'Popular Posts By Views In The Last 7 Days');
		$instance = wp_parse_args( (array) $instance, $defaults );
?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('commentsorvisits'); ?>">Rank posts popularity by number of comments or visits?</label>
		<select id="<?php echo $this->get_field_id('commentsorvisits'); ?>" name="<?php echo $this->get_field_name('commentsorvisits'); ?>" value="<?php echo $instance['commentsorvisits']; ?>" style="width:100%;">
			<option value="1" <?php if ($instance['commentsorvisits']==1) {echo "selected";} ?>>Visits</option>
			<option value="2" <?php if ($instance['commentsorvisits']==2) {echo "selected";} ?>>Comments</option>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('posnumber'); ?>">Number of positions:</label>
		<select id="<?php echo $this->get_field_id('posnumber'); ?>" name="<?php echo $this->get_field_name('posnumber'); ?>" value="<?php echo $instance['posnumber']; ?>" style="width:100%;">
			<option value="2" <?php if ($instance['posnumber']==2) {echo "selected";} ?>>1</option>
			<option value="3" <?php if ($instance['posnumber']==3) {echo "selected";} ?>>2</option>
			<option value="4" <?php if ($instance['posnumber']==4) {echo "selected";} ?>>3</option>
			<option value="5" <?php if ($instance['posnumber']==5) {echo "selected";} ?>>4</option>
			<option value="6" <?php if ($instance['posnumber']==6) {echo "selected";} ?>>5</option>
			<option value="7" <?php if ($instance['posnumber']==7) {echo "selected";} ?>>6</option>
			<option value="8" <?php if ($instance['posnumber']==8) {echo "selected";} ?>>7</option>
			<option value="9" <?php if ($instance['posnumber']==9) {echo "selected";} ?>>8</option>
			<option value="10" <?php if ($instance['posnumber']==10) {echo "selected";} ?>>9</option>
			<option value="11" <?php if ($instance['posnumber']==11) {echo "selected";} ?>>10</option>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'numberofdays' ); ?>">Only include articels that were visited/created in last:</label>
		<select id="<?php echo $this->get_field_id( 'numberofdays' ); ?>" name="<?php echo $this->get_field_name('numberofdays'); ?>" value="<?php echo $instance['numberofdays']; ?>" style="width:100%;">
			<option value="1" <?php if ($instance['numberofdays']==1) {echo "selected"; } ?>>1 day</option>
			<option value="2" <?php if ($instance['numberofdays']==2) {echo "selected"; } ?>>2 days</option>
			<option value="3" <?php if ($instance['numberofdays']==3) {echo "selected"; } ?>>3 days</option>
			<option value="4" <?php if ($instance['numberofdays']==4) {echo "selected"; } ?>>4 days</option>
			<option value="5" <?php if ($instance['numberofdays']==5) {echo "selected"; } ?>>5 days</option>
			<option value="6" <?php if ($instance['numberofdays']==6) {echo "selected"; } ?>>6 days</option>
			<option value="7" <?php if ($instance['numberofdays']==7) {echo "selected"; } ?>>7 days</option>
			<option value="15" <?php if ($instance['numberofdays']==15) {echo "selected"; } ?>>15 days</option>
			<option value="30" <?php if ($instance['numberofdays']==30) {echo "selected"; } ?>>30 days</option>
			<option value="180" <?php if ($instance['numberofdays']==180) {echo "selected"; } ?>>180 days</option>
			<option value="365" <?php if ($instance['numberofdays']==365) {echo "selected"; } ?>>1 year</option>
			<option value="730" <?php if ($instance['numberofdays']==730) {echo "selected"; } ?>>2 years</option>
			<option value="1825" <?php if ($instance['numberofdays']==1825) {echo "selected"; } ?>>5 years</option>
			<option value="3650" <?php if ($instance['numberofdays']==3650) {echo "selected"; } ?>>10 years</option>
		</select>
		</p>

		<p>
		<input type="checkbox" id="<?php echo $this->get_field_id('hitsonoff'); ?>" name="<?php echo $this->get_field_name('hitsonoff'); ?>" value="1" <?php checked($instance['hitsonoff'], 1); ?>/>
		<label for="<?php echo $this->get_field_id('hitsonoff'); ?>">Show hit count/comments number?</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('ignoredpages'); ?>">If you would like to exclude any pages from being displayed, you can enter the Page IDs (comma separated, e.g. 34, 25, 439):</label>
			<input id="<?php echo $this->get_field_id('ignoredpages'); ?>" name="<?php echo $this->get_field_name('ignoredpages'); ?>" value="<?php echo $instance['ignoredpages']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('ignoredcategories'); ?>">If you would like to exclude any categories from being displayed, you can enter the Category IDs (comma separated, e.g. 3, 5, 10):</label>
			<input id="<?php echo $this->get_field_id('ignoredcategories'); ?>" name="<?php echo $this->get_field_name('ignoredcategories'); ?>" value="<?php echo $instance['ignoredcategories']; ?>" style="width:100%;" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('cssselector'); ?>">Style Select:</label>
		<select id="<?php echo $this->get_field_id('cssselector'); ?>" name="<?php echo $this->get_field_name('cssselector'); ?>" value="<?php echo $instance['cssselector']; ?>" style="width:100%;" onchange="
			if (this.options[this.selectedIndex].value == 'http://smartfan.pl/most-popular-posts-widget-premium-styles/') {
				window.open( this.options[ this.selectedIndex ].value, '_blank');
			}
		">
			<option value="1" <?php if ($instance['cssselector']==1) {echo "selected";} ?>>Standard Style No. 1 (color bars)</option>
			<option value="2" <?php if ($instance['cssselector']==2) {echo "selected";} ?>>Standard Style No. 2 (color bars + text with white outline)</option>
			<option value="3" <?php if ($instance['cssselector']==3) {echo "selected";} ?>>Standard Style No. 3 (grey numbered list)</option>
			<option value="4" <?php if ($instance['cssselector']==4) {echo "selected";} ?>>Standard Style No. 4 (grey list with numbers in blue circle)</option>
			<option value="5" <?php if ($instance['cssselector']==5) {echo "selected";} ?>>Standard Style No. 5 (grey list with red numbers)</option>
			<option value="6" <?php if ($instance['cssselector']==6) {echo "selected";} ?>>Standard Style No. 6 (simple grey list with grey numbers)</option>
			<option value="7" <?php if ($instance['cssselector']==7) {echo "selected";} ?>>Custom Style (edit custom.css file)</option>
			<?php
				if(file_exists(plugin_dir_path(__FILE__).'style-popular-posts-statistics-1-premium.css')){
			?>		<option value="8" <?php if ($instance['cssselector']==8) {echo "selected";} ?>>Premium Style No. 1</option>
			<?php
					}
				if(file_exists(plugin_dir_path(__FILE__).'style-popular-posts-statistics-2-premium.css')){
			?>		<option value="9" <?php if ($instance['cssselector']==9) {echo "selected";} ?>>Premium Style No. 2</option>
			<?php
					}
				if(file_exists(plugin_dir_path(__FILE__).'style-popular-posts-statistics-3-premium.css')){
			?>		<option value="10" <?php if ($instance['cssselector']==10) {echo "selected";} ?>>Premium Style No. 3</option>
			<?php
					}
				if(file_exists(plugin_dir_path(__FILE__).'style-popular-posts-statistics-4-premium.css')){
			?>		<option value="11" <?php if ($instance['cssselector']==11) {echo "selected";} ?>>Premium Style No. 4</option>
			<?php
					}
				if(file_exists(plugin_dir_path(__FILE__).'style-popular-posts-statistics-5-premium.css')){
			?>		<option value="12" <?php if ($instance['cssselector']==12) {echo "selected";} ?>>Premium Style No. 5</option>
			<?php
					}
				if(file_exists(plugin_dir_path(__FILE__).'style-popular-posts-statistics-6-premium.css')){
			?>		<option value="13" <?php if ($instance['cssselector']==13) {echo "selected";} ?>>Premium Style No. 6</option>
			<?php
					}
			?>
			<option value="http://smartfan.pl/most-popular-posts-widget-premium-styles/">More styles... (for $1 only!)</option>
		</select>

		<p>
			<label for="<?php echo $this->get_field_id('visitstext'); ?>">If you would like to change "visit(s)" text, you can do it here:</label>
			<input id="<?php echo $this->get_field_id('visitstext'); ?>" name="<?php echo $this->get_field_name('visitstext'); ?>" value="<?php echo $instance['visitstext']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('cachetime'); ?>">Cache refresh time in minutes (default value is 0 minutes, which means cache is off):</label>
			<input id="<?php echo $this->get_field_id('cachetime'); ?>" name="<?php echo $this->get_field_name('cachetime'); ?>" value="<?php echo $instance['cachetime']; ?>" style="width:100%;" />
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('cleandatabase'); ?>" name="<?php echo $this->get_field_name('cleandatabase'); ?>" value="1" <?php checked($instance['cleandatabase'], 1); ?>/>
			<label for="<?php echo $this->get_field_id('cleandatabase'); ?>"><b>Delete all widget collected data?</b> (Check it only if you feel that database data is too large and makes widget run slow!)</label>
		</p>

<?php

	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		// available fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['posnumber'] = strip_tags($new_instance['posnumber']);
		$instance['numberofdays'] = strip_tags($new_instance['numberofdays']);
		$instance['cssselector'] = strip_tags($new_instance['cssselector']);
		$instance['hitsonoff'] = strip_tags($new_instance['hitsonoff']);
		$instance['ignoredpages'] = strip_tags($new_instance['ignoredpages']);
		$instance['cachetime'] = strip_tags($new_instance['cachetime']);
		$instance['ignoredcategories'] = strip_tags($new_instance['ignoredcategories']);
		$instance['visitstext'] = strip_tags($new_instance['visitstext']);
		$instance['cleandatabase'] = strip_tags($new_instance['cleandatabase']);
		$instance['commentsorvisits'] = strip_tags($new_instance['commentsorvisits']);
		return $instance;
	}

	// widget front end
	function widget($args, $instance) {
	extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		$posnumber = $instance['posnumber'];
		$numberofdays = $instance['numberofdays'];
		$cssselector = $instance['cssselector'];
		$hitsonoff = $instance['hitsonoff'];
		$ignoredpages = $instance['ignoredpages'];
		$ignoredpages = trim(preg_replace('/\s+/', '', $ignoredpages));
		$ignoredpages = explode(",",$ignoredpages);
		$cachetime = $instance['cachetime'];
		$cachetime = trim(preg_replace('/\s+/', '', $cachetime));
		$ignoredcategories = $instance['ignoredcategories'];
		$ignoredcategories = trim(preg_replace('/\s+/', '', $ignoredcategories));
		$ignoredcategories = explode(",",$ignoredcategories);
		$visitstext = $instance['visitstext'];
		$cleandatabase = $instance['cleandatabase'];
		$commentsorvisits = $instance['commentsorvisits'];
		echo $before_widget;

		// table clean up if user decided
		if ($cleandatabase == 1){
			clean_up_database();
			$update_options = get_option('widget_popular_posts_statistics');
			$update_options[2]['cleandatabase'] = '';
			update_option('widget_popular_posts_statistics', $update_options);
		}

		// title check
		if ($title) {
			echo $before_title . $title . $after_title;
		}
		$postID = get_the_ID();
		//LuanDT get category id
		$cat_id = wp_get_post_categories($postID);
		if (!empty($cat_id)) {
			$cat_id = $cat_id[0];
			//LuanDT get postID parent
			$post = get_posts(array('category' => $cat_id, 'post_type' => 'post'));
			if (!empty($post)) {
				$postID = $post[0]->ID;
			}
		}
		$cache_file = plugin_dir_path(__FILE__).'popular_posts.cache';

		if(file_exists($cache_file) && (filesize($cache_file) > 50) && (filemtime($cache_file) > (time() - 60 * $cachetime ))){ //cache
			$cached = file_get_contents($cache_file);
		}else{
			$to_file = show_views($postID, $posnumber, $numberofdays, $hitsonoff, $ignoredpages, $ignoredcategories, $visitstext, $commentsorvisits);
			file_put_contents($cache_file, $to_file);
			$cached = file_get_contents($cache_file);
		}
		echo '<div id="pp-container">';
		echo $cached;
		echo '</div>';

		add_views($postID);

		echo $after_widget;
	}
}

// shortcode function
function most_popular_posts_shortcode_handler() {
	$widgetOptions = get_option('widget_popular_posts_statistics');

	$postID = get_the_ID();
	if($widgetOptions != NULL){ //if widget options are not null
		$posnumber = array_column($widgetOptions, 'posnumber');
		$numberofdays = array_column($widgetOptions, 'numberofdays');
		$hitsonoff = array_column($widgetOptions, 'hitsonoff');
		$ignoredpages = array(array_column($widgetOptions, 'ignoredpages'));
		$ignoredcategories = array(array_column($widgetOptions, 'ignoredcategories'));
		$visitstext = array_column($widgetOptions, 'visitstext');
		$commentsorvisits = array_column($widgetOptions, 'commentsorvisits');

		ob_start();
		echo '<div id="pp-container">';
		echo show_views($postID, $posnumber[0], $numberofdays[0], $hitsonoff[0], $ignoredpages, $ignoredcategories, $visitstext[0], $commentsorvisits[0]);
		echo '</div>';
		$ret = ob_get_contents();
		ob_end_clean();
		return $ret;
	}
}

// widget registration
add_action('widgets_init', create_function('', 'return register_widget("popular_posts_statistics");'));

add_action('wp_enqueue_scripts', function () {
	$css_select = get_option('widget_popular_posts_statistics'); // choose CSS file
	if($css_select != NULL){ //if widget options are not null
		$css_sel = array();
		foreach($css_select as $css_selector){
			$css_sel[] = $css_selector['cssselector'];
		}
		wp_enqueue_style('popular_posts_statistics', plugins_url(choose_style($css_sel[0]), __FILE__)); // CSS file selector
	}
});

?>