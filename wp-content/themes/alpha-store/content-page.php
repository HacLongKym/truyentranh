<!-- start content container -->
<div class="row rsrc-content">    
	<?php //left sidebar ?>    
	<?php get_sidebar( 'left' ); ?>    
	<article class="col-md-<?php alpha_store_main_content_width(); ?> rsrc-main" itemscope itemtype="http://schema.org/BlogPosting">        
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>         
				<?php alpha_store_breadcrumb(); ?>         
				<?php if ( has_post_thumbnail() ) : ?>                                
					<div class="single-thumbnail"><?php the_post_thumbnail( 'alpha-store-single', array( 'itemprop' => 'image' ) ); ?></div>                                     
					<div class="clear"></div>                            
				<?php endif; ?>          
				<div <?php post_class( 'rsrc-post-content' ); ?>>                            
					<header>                              
						<h1 class="entry-title page-header" itemprop="headline">
							<?php the_title(); ?>
						</h1>                                                        
					</header>                            
					<div class="entry-content" itemprop="articleBody">                              
						<?php the_content(); ?>                            
					</div>                               
					<?php wp_link_pages(); ?>                                                                                  
					<?php comments_template(); ?>                         
				</div>        
			<?php endwhile; ?>        
		<?php else : ?>            
			<?php get_404_template(); ?>        
		<?php endif; ?>    
	</article>    
	<?php //get the right sidebar ?>    
	<?php get_sidebar( 'right' ); ?>
</div>
<!-- end content container -->