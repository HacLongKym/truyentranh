<?php
/**
 * @package Gridster
 */
?>


<div id="post-<?php the_ID(); ?>" class="item  col-xs-4 col-lg-4 <?= join( ' ', get_post_class( "poste" ) ) ?>"> 
    <div class="thumbnail">
		<a href="<?php the_permalink(); ?>">
			<?php if ( has_post_thumbnail() ) {?>
				<img class="group list-group-image img-rotate-scale" src="<?=get_the_post_thumbnail_url()?>" alt="<?php the_title(); ?>" />
			<?php
				//the_post_thumbnail('post-thumb', array('class' => 'group list-group-image'));
			 } else { 
			?>
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/defaultthumb.png" class="group list-group-image" alt="<?php the_title(); ?>" />

			<?php } ?>
		</a>
        <div class="caption">
            <a href="<?php the_permalink(); ?>" rel="bookmark">
	            <h4 class="group inner list-group-item-heading posttitle">
					<?php the_title(); ?>
	            </h4>
			</a>
            <p class="group inner list-group-item-text">
			<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
				<?php
				$categories_list = get_the_category_list( __( ', ', 'gridster-lite' ) );
				$categories_list = get_the_content();
				if ( $categories_list && gridster_categorized_blog() ) :
				printf( __( '%1$s', 'gridster-lite' ), $categories_list );
				endif; // End if categories 
				?>
			<?php endif; // End if 'post' == get_post_type() ?>
             <?php /*
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <p class="lead">
                        $21.000</p>
                </div>
                <div class="col-xs-12 col-md-6">
                    <a class="btn btn-success" href="http://www.jquery2dotnet.com">Add to cart</a>
                </div>
            </div>
            */ ?>
        </div>
    </div>
</div>
<div id="#checkClear"></div>
<?php /*
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
				// translators: used between list items, there is a space after the comma 
				//$categories_list = get_the_category_list( __( ', ', 'gridster-lite' ) );
	$categories_list = get_the_content();
	if ( $categories_list && gridster_categorized_blog() ) :
?>
<?php printf( __( '%1$s', 'gridster-lite' ), $categories_list ); ?>
<?php endif; // End if categories ?>
<?php //endif; // End if 'post' == get_post_type() ?>
</p>
</div>
<!-- post -->
*/ ?>