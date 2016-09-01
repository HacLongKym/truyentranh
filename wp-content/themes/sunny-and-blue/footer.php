<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */
?>
</div> <!-- end of /.container -->
</main>
<footer class="footer">
    <div class="footer-above">
        <div class="container">
            <div class="row">
                <!-- footer share button -->
                <div id="footerSocial" class="col-md-6">
				<!-- social-share -->
				 
	<?php sunny_and_blue_social_media_icons(); ?> 
                   
                
                    <!-- /.social-share -->
                </div>
                <div class="col-md-6">
                    <?php if (dynamic_sidebar('footer-right-1')) : else : ?>
<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
	<div class="pre-widget">
		<h3><?php _e('Widgetized Footer Right Column', 'sunny-and-blue'); ?></h3>
		<p><?php _e('This panel is active and ready for you to add some widgets via the WP Admin', 'sunny-and-blue'); ?></p>
	
	</div>
	<?php else: the_widget( 'WP_Widget_Tag_Cloud' );?>
	<?php endif; ?>
	<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-below">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
				<?php _e('Theme by Sunny and Blue', 'sunny-and-blue'); ?>                    
                </div>
            </div>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>

</body>
</html>