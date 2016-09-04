<?php
/**
 * @package Gridster
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class("poste"); ?>> <a href="<?php the_permalink(); ?>">
<?php if ( has_post_thumbnail() ) {
the_post_thumbnail('post-thumb', array('class' => 'postimg'));
} else { ?>
<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/defaultthumb.png" class="postimg" alt="<?php the_title(); ?>" />

<?php } ?>
</a>
<div class="portfoliooverlay"><a href="<?php the_permalink(); ?>"><span>+</span></a></div>
<h2 class="posttitle"><a href="<?php the_permalink(); ?>" rel="bookmark">
<?php the_title(); ?>
</a></h2>
<p class="postmeta" style="text-align: left;display: -webkit-box;overflow: hidden;-webkit-line-clamp: 3;-webkit-box-orient: vertical;">
<?php //if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
<?php
				//LuanDT fix to display descr
				/* translators: used between list items, there is a space after the comma */
				//$categories_list = get_the_category_list( __( ', ', 'gridster-lite' ) );
				$categories_list = get_the_content();
				if ( $categories_list && gridster_categorized_blog() ) :
			?>
<?php printf( __( '%1$s', 'gridster-lite' ), $categories_list ); ?>
<?php //endif; // End if categories ?>
<?php endif; // End if 'post' == get_post_type() ?>
</p>
</div>
<!-- post -->
