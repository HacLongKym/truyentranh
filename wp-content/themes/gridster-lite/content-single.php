<?php
/**
 * @package Gridster
 */
?>

<div id="main">
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>> <a href="<?php the_permalink(); ?>">
<?php the_post_thumbnail('post-full', array('class' => 'postimage')); ?>
</a>
<div id="content">
<div id="postheading">
<h1>
<?php the_title(); ?>
</h1>

<?php dynamic_sidebar( 'main-sidebar-luandt' ); ?>
<?php
	//LuanDT
	if (strpos($_SERVER[REQUEST_URI], '/comic') === 0) {
		$path_chapter = '/resource' . $_SERVER[REQUEST_URI];
		$arr_filename = scandir(BASE_PATH . $path_chapter);
		foreach ($arr_filename as $filename) {
			if ($filename == '.' || $filename == '..') {
				continue;
			}
			$path_img = $path_chapter . $filename;
			$title = str_replace("-", " ", $filename);
			echo "<img width=\"100%\" src=\"{$path_img}\" class=\"postimage wp-post-image img-to-read\" alt=\"{$title}\" class=\"alignnone size-large\">";
		}
	} else {
		$path_comic = '/comic' . $_SERVER[REQUEST_URI];
		$arr_filename = scandir(BASE_PATH . '/resource' . $path_comic);
		foreach ($arr_filename as $filename) {
			if ($filename == '.' || $filename == '..') {
				continue;
			}
			$path_chapter = $path_comic . $filename;
			$title = str_replace("-", " ", $filename);
			echo "<p><a href=\"{$path_chapter}\" title=\"{$title}\">{$title}</a></p>";
		}
	}
?>
</div>
<ul id="meta">
<li class="datemeta"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></li>
<li class="categorymeta">
<?php _e('Posted in:','gridster-lite') ?>
<?php the_category(', ') ?>
</li>
<li class="commentsmeta"><a href="<?php comments_link(); ?>">
<?php comments_number( '0', '1', '%' ); ?>
</a></li>
<li class="authormeta">
<?php _e('Author:','gridster-lite') ?>
<a class="url fn n" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
<?php the_author(); ?>
</a></li>
<li class="tagmeta">
<?php the_tags('Tags:  ',', ',''); ?>
</li>
</ul>
<?php the_content(); ?>
<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'gridster-lite' ),
				'after'  => '</div>',
			) );
?>
<?php edit_post_link( __( 'Edit', 'gridster-lite' ), '<span class="edit-link">', '</span>' ); ?>
<div id="comments">
<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template();
?>
</div>
<!-- #post-## -->
</div>
<!-- comments -->
<?php gridster_content_nav( 'nav-below' ); ?>
</div>
<!-- content -->
