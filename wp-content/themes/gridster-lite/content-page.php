<?php
/**
 * The template used for displaying page content in page.php
 *
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
</div>
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
