<article class="archive-article col-md-6" itemscope itemtype="http://schema.org/BlogPosting"> 
	<div <?php post_class(); ?>>                            
		<?php if ( has_post_thumbnail() ) : ?>                               
			<div class="featured-thumbnail"><?php the_post_thumbnail( 'alpha-store-single', array( 'itemprop' => 'image' ) ); ?></div>                                                                                 
		<?php endif; ?>
		<header>
			<h2 class="page-header" itemprop="headline">                                
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
					<?php the_title(); ?>
				</a>                            
			</h2>
			<?php get_template_part( 'template-parts/template-part', 'postmeta' ); ?>
		</header>  
		<div class="home-header text-center">                                                      
			<div class="entry-summary" itemprop="text">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->                                                                                                                       
			<div class="clear"></div>                                  
			<p>                                      
				<a class="btn btn-primary btn-md outline" href="<?php the_permalink(); ?>" itemprop="interactionCount">
					<?php esc_html_e( 'Read more', 'alpha-store' ); ?> 
				</a>                                  
			</p>                            
		</div>                      
	</div>
	<div class="clear"></div>
</article>