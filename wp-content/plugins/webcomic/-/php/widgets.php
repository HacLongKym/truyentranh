<?php
/**
 * Contains WebcomicWidget and related classes.
 * 
 * @package Webcomic
 */

/**
 * Handle widget-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicWidget extends Webcomic {
	/**
	 * Register action and filter hooks.
	 * 
	 * @uses WebcomicWidget::widgets_init()
	 * @uses WebcomicWidget::admin_enqueue_scripts()
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}
	
	/**
	 * Register widgets.
	 * 
	 * @uses Widget_WebcomicLink
	 * @uses Widget_WebcomicPrint
	 * @uses Widget_DynamicWebcomic
	 * @uses Widget_RecentWebcomics
	 * @uses Widget_WebcomicDonation
	 * @uses Widget_ScheduledWebcomics
	 * @uses Widget_WebcomicStorylines
	 * @uses Widget_WebcomicCharacters
	 * @uses Widget_WebcomicCollections
	 * @uses Widget_PurchaseWebcomicLink
	 * @uses Widget_WebcomicStorylineLink
	 * @uses Widget_WebcomicCharacterLink
	 * @uses Widget_WebcomicCollectionLink
	 * @uses Widget_WebcomicTranscriptsLink
	 * @hook widgets_init
	 */
	public function widgets_init() {
		register_widget( 'Widget_WebcomicLink' );
		register_widget( 'Widget_WebcomicPrint' );
		register_widget( 'Widget_DynamicWebcomic' );
		register_widget( 'Widget_RecentWebcomics' );
		register_widget( 'Widget_WebcomicDonation' );
		register_widget( 'Widget_ScheduledWebcomics' );
		register_widget( 'Widget_WebcomicStorylines' );
		register_widget( 'Widget_WebcomicCharacters' );
		register_widget( 'Widget_WebcomicCollections' );
		register_widget( 'Widget_PurchaseWebcomicLink' );
		register_widget( 'Widget_WebcomicStorylineLink' );
		register_widget( 'Widget_WebcomicCharacterLink' );
		register_widget( 'Widget_WebcomicCollectionLink' );
		register_widget( 'Widget_WebcomicTranscriptsLink' );
	}
	
	/**
	 * Register scripts for widgets.
	 * 
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$config
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ('widgets' === $screen->id or 'customize' === $screen->id) {
			wp_enqueue_script('webcomic-widgets', self::$url . '-/js/admin-widgets.js', array('jquery'));
			
			wp_enqueue_media();
		}
	}
	
	/**
	 * Provides access to the plugin directory path.
	 * 
	 * @uses Webcomic::$dir
	 * @return string
	 */
	public static function dir() {
		return self::$dir;
	}
}

/** Webcomic Link widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicLink extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Link', 'webcomic' ), array( 'description' => __( 'Link to the previous, next, first, last, or a random webcomic', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$relative = $cache ? "{$relative}-nocache" : $relative;
		$collection = isset( $collection ) ? $collection : WebcomicTag::get_webcomic_collection();
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$link = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $link ) . '"', $image );
		}
		
		if ( $output = WebcomicTag::relative_webcomic_link( '%link', $link, $relative, ( boolean ) $in_same_term, false, $in_same_term, $collection ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'image' ] ) {
			if ( $old[ 'image' ] and $old[ 'image' ] !== $new[ 'image' ] ) {
				delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-link' );
			}
			
			update_post_meta( $new[ 'image' ], '_wp_attachment_context', 'widget-webcomic-link' );
		} else {
			delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-link' );
		}
		
		$old[ 'title' ]        = strip_tags( $new[ 'title' ] );
		$old[ 'link' ]         = $new[ 'link' ];
		$old[ 'image' ]        = $new[ 'image' ];
		$old[ 'relative' ]     = $new[ 'relative' ];
		$old[ 'collection' ]   = $new[ 'collection' ];
		$old[ 'in_same_term' ] = $new[ 'in_same_term' ];
		$old[ 'cache' ] = (bool) $new[ 'cache' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Target', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'relative' ); ?>">
					<option value="previous"<?php empty( $relative ) ? '' : selected( 'previous', $relative ); ?>><?php _e( 'Previous', 'webcomic' ); ?></option>
					<option value="next"<?php empty( $relative ) ? '' : selected( 'next', $relative ); ?>><?php _e( 'Next', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $relative ) ? '' : selected( 'first', $relative ); ?>><?php _e( 'First', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $relative ) ? '' : selected( 'last', $relative ); ?>><?php _e( 'Last', 'webcomic' ); ?></option>
					<option value="random"<?php empty( $relative ) ? '' : selected( 'random', $relative ); ?>><?php _e( 'Random', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Limit', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'in_same_term' ); ?>">
					<option value=""><?php _e( '(none)', 'webcomic' ); ?></option>
					<option value="storyline"<?php empty( $in_same_term ) ? '' : selected( 'storyline', $in_same_term ); ?>><?php _e( 'Storylines', 'webcomic' ); ?></option>
					<option value="character"<?php empty( $in_same_term ) ? '' : selected( 'character', $in_same_term ); ?>><?php _e( 'Characters', 'webcomic' ); ?></option>
				</select><br>
				<span class="description"><?php _e( 'Limit navigation to the storylines or characters of the current webcomic.', 'webcomic' ); ?></span>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="<?php echo $this->get_field_name( 'cache' ); ?>" value="1"<?php empty($cache) ? '' : checked($cache); ?>> <?php _e('Parameterize URL'); ?><br>
				<span class="description"><?php _e('For first, last, and random webcomic links.', 'webcomic'); ?></span>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'image' ); ?>"><?php self::ajax_image( empty( $image ) ? 0 : $image, $this->get_field_name( 'image' ), $this->get_field_id( 'image' ) ); ?></span><br>
				<input type="text" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo empty( $link ) ? '' : esc_attr( $link ); ?>" class="widefat">
				<span class="description"><?php _e( 'The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%title</td>
					<td><?php _e( 'Webcomic Title', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%date</td>
					<td><?php _e( 'Publish Date', 'webcomic' ); ?></td>
				</tr>
				<?php
					$count = 1;
					$sizes = '<tr><td>%full</td></tr>';
					
					foreach ( get_intermediate_image_sizes() as $size ) {
						$count++;
						$sizes .= "<tr><td>%{$size}</td></tr>";
					}
					
					echo preg_replace( '/<\/td><\/tr>/', '</td><td rowspan="' . $count . '" style="border-left:thin solid #dfdfdf">' . __( 'Image Preview', 'webcomic' ) . '</td></tr>', $sizes, 1 );
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Handle webcomic link image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Link Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_WebcomicLink::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_WebcomicLink::ajax_image" data-name="', $name, '" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}

/** Webcomic Print widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicPrint extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Print', 'webcomic' ), array( 'description' => __( 'A purchase print form for individual webcomics', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_donation_form()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$label = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $label ) . '"', $image );
		}
		
		if ( $output = WebcomicTag::webcomic_print_form( $type, $label ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'image' ] ) {
			if ( $old[ 'image' ] and $old[ 'image' ] !== $new[ 'image' ] ) {
				delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-print' );
			}
			
			update_post_meta( $new[ 'image' ], '_wp_attachment_context', 'widget-webcomic-print' );
		} else {
			delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-print' );
		}
		
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'type' ]  = $new[ 'type' ];
		$old[ 'image' ] = $new[ 'image' ];
		$old[ 'label' ] = $new[ 'label' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * The Webcomic Donation widget requires that at least one
	 * collection have a business email. If no collection has a business
	 * email an error message will be displayed in place of the widget
	 * settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$commerce    = array();
		$collections = WebcomicTag::get_webcomic_collections( true );
		
		foreach( $collections as $k => $v ) {
			if ( $v[ 'commerce' ][ 'business' ] ) {
				$commerce[ $k ] = $v[ 'name' ];
			}
		}
		
		if ( $commerce ) { ?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Type', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'type' ); ?>">
					<option value="domestic"<?php selected( 'domestic', empty( $type ) ? '' : $type ); ?>><?php _e( 'Domestic', 'webcomic' ); ?></option>
					<option value="international"<?php selected( 'international', empty( $type ) ? '' : $type ); ?>><?php _e( 'International', 'webcomic' ); ?></option>
					<option value="original"<?php selected( 'original', empty( $type ) ? '' : $type ); ?>><?php _e( 'Original', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Label', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'image' ); ?>"><?php self::ajax_image( empty( $image ) ? 0 : $image, $this->get_field_name( 'image' ), $this->get_field_id( 'image' ) ); ?></span><br>
				<input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo empty( $label ) ? '' : esc_attr( $label ); ?>" class="widefat">
				<span class="description"><?php _e( 'The label text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%total</td>
					<td><?php _e( 'Total Cost', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%price</td>
					<td><?php _e( 'Print Cost', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%shipping</td>
					<td><?php _e( 'Shipping Cost', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%collection-total</td>
					<td><?php _e( 'Collection Total Cost', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%collection-price</td>
					<td><?php _e( 'Collection Print Cost', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%collection-shipping</td>
					<td><?php _e( 'Collection Shipping Cost', 'webcomic' ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php } else { ?>
		<p style="color:#bc0b0b"><b><?php _e( 'You must add a business email to one or more collections to use this widget.', 'webcomic' ); ?></b></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'type' ); ?>" value="<?php echo empty( $type ) ? '' : $type; ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo empty( $image ) ? '' : $image; ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo empty( $label ) ? '' : esc_attr( $label ); ?>">
		<?php	
		}
	}
	
	/**
	 * Handle webcomic donation image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Print Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_WebcomicPrint::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_WebcomicPrint::ajax_image" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}

/** Dynamic Webcomic widget.
 * 
 * @package Webcomic
 */
class Widget_DynamicWebcomic extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Dynamic', 'webcomic' ), array( 'description' => __( 'A dynamic browser for your webcomics', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicWidget::dir()
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function widget( $args, $instance ) {
		global $post; $temp_post = $post;
		
		if ( wp_script_is( 'webcomic-dynamic', 'queue' ) ) {
			extract( $args );
			extract( $instance );
			
			if ( !$collection ) {
				$collection = WebcomicTag::get_webcomic_collection();
			} elseif ( -1 === ( int ) $collection ) {
				$collection = WebcomicTag::get_webcomic_collections();
			}
			
			if ( $collection ) {
				$webcomic = new WP_Query( array( 'post_type' => $collection, 'posts_per_page' => 1, 'order' => $reverse ? 'ASC' : 'DESC' ) );
				
				if ( $webcomic->have_posts() ) {
					echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, '<div data-webcomic-container="', $widget_id, '"', empty( $gesture ) ?  '' : ' data-webcomic-gestures', '>';
					
					while ( $webcomic->have_posts() ) { $webcomic->the_post();
						if ( !locate_template( array( "webcomic/dynamic-{$collection}-{$widget_id}.php", "webcomic/dynamic-{$widget_id}.php", "webcomic/dynamic-{$collection}.php", 'webcomic/dynamic.php' ), true, false ) ) {
							require WebcomicWidget::dir() . '-/php/integrate/dynamic.php';
						}
					}
					
					echo  '</div>', $after_widget;
				}
			}
		}
		
		$post = $temp_post;
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'reverse' ]    = $new[ 'reverse' ];
		$old[ 'gestures' ]   = $new[ 'gestures' ];
		$old[ 'collection' ] = $new[ 'collection' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * The Dynamic Webcomic widget requires that the dynamic navigation
	 * option be enabled. If dynamic navigation is not enabled an error
	 * message will be displayed in place of the widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$config      = get_option( 'webcomic_options' );
		$collections = WebcomicTag::get_webcomic_collections( true );
		
		if ( empty( $config[ 'dynamic' ] ) ) :
		?>
		<p style="color:#bc0b0b"><b><?php _e( 'You must enable dynamic navigation on the Settings > Webcomic page to use this widget.', 'webcomic' ); ?></b></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'collection' ); ?>" value="<?php echo empty( $collection ) ? '' : $collection; ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'reverse' ); ?>" value="<?php echo empty( $reverse ) ? '' : $reverse; ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'gestures' ); ?>" value="<?php echo empty( $gestures ) ? '' : $gestures; ?>">
		<?php else : ?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<option value="-1"<?php echo empty( $collection ) ? '' : selected( -1, $collection, false ); ?>><?php _e( '(all collections)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'reverse' ); ?>" value="1"<?php checked( !empty( $reverse ) ); ?>> <?php _e( 'Start with first webcomic', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'gestures' ); ?>" value="1"<?php checked( ( !empty( $config[ 'gestures' ] ) and !empty( $gestures ) ) ); disabled( empty( $config[ 'gestures' ] ) ); ?>> <?php _e( 'Enable touch gestures', 'webcomic' ); ?></label>
			<?php echo empty( $config[ 'gestures' ] ) ? '<br><span class="description">' . __( 'Gestures must be enabled on the <b>Settings > Webcomic</b> page.', 'webcomic' ) . '</span>' : ''; ?>
		</p>
		<?php
		endif;
	}
}

/** Recent Webcomics widget.
 * 
 * @package Webcomic
 */
class Widget_RecentWebcomics extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Recent', 'webcomic' ), array( 'description' => __( 'The most recently published webcomics on your site', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::the_webcomic()
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function widget( $args, $instance ) {
		global $post; $temp_post = $post;
		
		extract( $args );
		extract( $instance );
		
		if ( !$collection ) {
			$collection = WebcomicTag::get_webcomic_collection();
		} elseif ( -1 === ( int ) $collection ) {
			$collection = WebcomicTag::get_webcomic_collections();
		}
		
		if ( $collection ) {
			$the_posts = new WP_Query( array( 'post_type' => $collection, 'posts_per_page' => $numberposts ) );
			
			if ( $the_posts->have_posts() ) {
				echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, '<ul class="recent-webcomics">';
				
				while ( $the_posts->have_posts() ) { $the_posts->the_post();
					echo '<li>', $image ? WebcomicTag::the_webcomic( $image, 'self' ) : '<a href="' . get_permalink() . '">' . get_the_title( '', '', false ) . '</a>', '</li>';
				}
				
				echo '</ul>', $after_widget;
			}
		}
		
		$post = $temp_post;
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]       = strip_tags( $new[ 'title' ] );
		$old[ 'image' ]       = $new[ 'image' ];
		$old[ 'collection' ]  = $new[ 'collection' ];
		$old[ 'numberposts' ] = $new[ 'numberposts' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<option value="-1"<?php echo empty( $collection ) ? '' : selected( -1, $collection, false ); ?>><?php _e( '(all collections)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Webcomics to show', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'numberposts' ); ?>" value="<?php echo empty( $numberposts ) ? 5 : $numberposts; ?>" size="3">
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'image' ); ?>" value="thumbnail"<?php checked( ( !empty( $image ) and 'thumbnail' === $image ), true ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Webcomic Donation widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicDonation extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Donation', 'webcomic' ), array( 'description' => __( 'A donation form for your webcomics', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_donation_form()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$label = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $label ) . '"', $image );
		}
		
		if ( $output = WebcomicTag::webcomic_donation_form( $label, $collection ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'image' ] ) {
			if ( $old[ 'image' ] and $old[ 'image' ] !== $new[ 'image' ] ) {
				delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-donation' );
			}
			
			update_post_meta( $new[ 'image' ], '_wp_attachment_context', 'widget-webcomic-donation' );
		} else {
			delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-donation' );
		}
		
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'image' ]      = $new[ 'image' ];
		$old[ 'label' ]      = $new[ 'label' ];
		$old[ 'collection' ] = $new[ 'collection' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * The Webcomic Donation widget requires that at least one
	 * collection have a business email. If no collection has a business
	 * email an error message will be displayed in place of the widget
	 * settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$commerce    = array();
		$collections = WebcomicTag::get_webcomic_collections( true );
		
		foreach( $collections as $k => $v ) {
			if ( $v[ 'commerce' ][ 'business' ] ) {
				$commerce[ $k ] = $v[ 'name' ];
			}
		}
		
		if ( $commerce ) { ?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $commerce as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Label', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'image' ); ?>"><?php self::ajax_image( empty( $image ) ? 0 : $image, $this->get_field_name( 'image' ), $this->get_field_id( 'image' ) ); ?></span><br>
				<input type="text" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo empty( $label ) ? '' : esc_attr( $label ); ?>" class="widefat">
				<span class="description"><?php _e( 'The label text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%amount</td>
					<td><?php _e( 'Donation Amount', 'webcomic' ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php } else { ?>
		<p style="color:#bc0b0b"><b><?php _e( 'You must add a business email to one or more collections to use this widget.', 'webcomic' ); ?></b></p>
		<input type="hidden" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo empty( $image ) ? '' : $image; ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'label' ); ?>" value="<?php echo empty( $label ) ? '' : esc_attr( $label ); ?>">
		<input type="hidden" name="<?php echo $this->get_field_name( 'collection' ); ?>" value="<?php echo empty( $collection ) ? '' : $collection; ?>">
		<?php	
		}
	}
	
	/**
	 * Handle webcomic donation image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Donation Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_WebcomicDonation::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_WebcomicDonation::ajax_image" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}

/** Scheduled Webcomics widget.
 * 
 * @package Webcomic
 */
class Widget_ScheduledWebcomics extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Scheduled', 'webcomic' ), array( 'description' => __( 'Share upcomming webcomics from your buffer.', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::the_webcomic()
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function widget( $args, $instance ) {
		global $post; $temp_post = $post;
		
		extract( $args );
		extract( $instance );
		
		if ( !$collection ) {
			$collection = WebcomicTag::get_webcomic_collection();
		} elseif ( -1 === ( int ) $collection ) {
			$collection = WebcomicTag::get_webcomic_collections();
		}
		
		if ( $collection ) {
			$the_posts = new WP_Query( array( 'post_type' => $collection, 'posts_per_page' => $numberposts, 'post_status' => 'future' ) );
			
			if ( $the_posts->have_posts() ) {
				echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, '<ul class="scheduled-webcomics">';
				
				while ( $the_posts->have_posts() ) { $the_posts->the_post();
					echo '<li><figure>', $image ? WebcomicTag::the_webcomic( $image ) : get_the_title( '', '', false ), $date ? '<figcaption>' . get_the_time( get_option( 'date_format' ) ) . '</figcaption>' : '', '</figure></li>';
				}
				
				echo '</ul>', $after_widget;
			}
		}
		
		$post = $temp_post;
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]       = strip_tags( $new[ 'title' ] );
		$old[ 'date' ]        = $new[ 'date' ];
		$old[ 'image' ]       = $new[ 'image' ];
		$old[ 'collection' ]  = $new[ 'collection' ];
		$old[ 'numberposts' ] = $new[ 'numberposts' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<option value="-1"<?php echo empty( $collection ) ? '' : selected( -1, $collection, false ); ?>><?php _e( '(all collections)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Webcomics to show', 'webcomic' ); ?>
				<input type="text" name="<?php echo $this->get_field_name( 'numberposts' ); ?>" value="<?php echo empty( $numberposts ) ? 5 : $numberposts; ?>" size="3">
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'image' ); ?>" value="thumbnail"<?php checked( ( !empty( $image ) and 'thumbnail' === $image ), true ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'date' ); ?>" value="1"<?php checked( !empty( $date ) ); ?>> <?php _e( 'Show webcomic publish date', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Webcomic Storylines widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicStorylines extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Storylines', 'webcomic' ), array( 'description' => __( 'A list, dropdown, or cloud of Webcomic storylines', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_list_terms()
	 * @uses WebcomicTag::webcomic_term_cloud()
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$a      = array_merge( $args, $instance );
		$object = get_queried_object();
		
		unset($a['name']);
		
		$a[ 'target' ]           = empty( $a[ 'target' ] ) ? 'archive' : $a[ 'target' ];
		$a[ 'taxonomy' ]         = $collection ? "{$collection}_storyline" : WebcomicTag::get_webcomic_collection() . '_storyline';
		$a[ 'show_option_none' ] = __( 'Select Storyline', 'webcomic' );
		
		if ( !empty( $object->taxonomy ) and $a[ 'taxonomy' ] === $object->taxonomy ) {
			$a[ 'selected' ] = $object->term_id;
		}
		
		if ( 'list' === $format ) {
			$output = WebcomicTag::webcomic_list_terms( $a );
		} elseif ( 'dropdown' === $format ) {
			$output = WebcomicTag::webcomic_dropdown_terms( $a );
		} else {
			$output = WebcomicTag::webcomic_term_cloud( $a );
		}
		
		if ( $output ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]          = strip_tags( $new[ 'title' ] );
		$old[ 'format' ]         = $new[ 'format' ];
		$old[ 'target' ]         = $new[ 'target' ];
		$old[ 'webcomics' ]      = $new[ 'webcomics' ];
		$old[ 'collection' ]     = $new[ 'collection' ];
		$old[ 'show_count' ]     = $new[ 'show_count' ];
		$old[ 'show_image' ]     = $new[ 'show_image' ];
		$old[ 'webcomic_image' ] = $new[ 'webcomic_image' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Format', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="list"<?php empty( $format ) ? '' : selected( 'list', $format ); ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="dropdown"<?php empty( $format ) ? '' : selected( 'dropdown', $format ); ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
					<option value="cloud"<?php empty( $format ) ? '' : selected( 'cloud', $format ); ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Target', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'target' ); ?>">
					<option value="archive"<?php empty( $format ) ? '' : selected( 'archive', $target ); ?>><?php _e( 'Archive', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $format ) ? '' : selected( 'first', $target ); ?>><?php _e( 'First Webcomic', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $format ) ? '' : selected( 'last', $target ); ?>><?php _e( 'Last Webcomic', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomics' ); ?>" value="1"<?php empty( $webcomics ) ? '' : checked( $webcomics ); ?>> <?php _e( 'Show webcomics', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="thumbnail"<?php empty( $show_image ) ? '' : checked( $show_image, 'thumbnail' ); ?>> <?php _e( 'Show storyline covers', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1"<?php empty( $show_count ) ? '' : checked( $show_count ); ?>> <?php _e( 'Show webcomic counts', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomic_image' ); ?>" value="thumbnail"<?php empty( $webcomic_image ) ? '' : checked( $webcomic_image, 'thumbnail' ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Webcomic Characters widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicCharacters extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Characters', 'webcomic' ), array( 'description' => __( 'A list, dropdown, or cloud of Webcomic characters', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_list_terms()
	 * @uses WebcomicTag::webcomic_term_cloud()
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$a      = array_merge( $args, $instance );
		$object = get_queried_object();
		
		unset($a['name']);
		
		$a[ 'target' ]           = empty( $a[ 'target' ] ) ? 'archive' : $a[ 'target' ];
		$a[ 'taxonomy' ]         = $collection ? "{$collection}_character" : WebcomicTag::get_webcomic_collection() . '_character';
		$a[ 'show_option_none' ] = __( 'Select Character', 'webcomic' );
		
		if ( !empty( $object->taxonomy ) and $a[ 'taxonomy' ] === $object->taxonomy ) {
			$a[ 'selected' ] = $object->term_id;
		}
		
		if ( 'list' === $format ) {
			$output = WebcomicTag::webcomic_list_terms( $a );
		} elseif ( 'dropdown' === $format ) {
			$output = WebcomicTag::webcomic_dropdown_terms( $a );
		} else {
			$output = WebcomicTag::webcomic_term_cloud( $a );
		}
		
		if ( $output ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]          = strip_tags( $new[ 'title' ] );
		$old[ 'format' ]         = $new[ 'format' ];
		$old[ 'target' ]         = $new[ 'target' ];
		$old[ 'webcomics' ]      = $new[ 'webcomics' ];
		$old[ 'collection' ]     = $new[ 'collection' ];
		$old[ 'show_count' ]     = $new[ 'show_count' ];
		$old[ 'show_image' ]     = $new[ 'show_image' ];
		$old[ 'webcomic_image' ] = $new[ 'webcomic_image' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Format', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="list"<?php empty( $format ) ? '' : selected( 'list', $format ); ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="dropdown"<?php empty( $format ) ? '' : selected( 'dropdown', $format ); ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
					<option value="cloud"<?php empty( $format ) ? '' : selected( 'cloud', $format ); ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Target', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'target' ); ?>">
					<option value="archive"<?php empty( $format ) ? '' : selected( 'archive', $target ); ?>><?php _e( 'Archive', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $format ) ? '' : selected( 'first', $target ); ?>><?php _e( 'First Webcomic', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $format ) ? '' : selected( 'last', $target ); ?>><?php _e( 'Last Webcomic', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomics' ); ?>" value="1"<?php empty( $webcomics ) ? '' : checked( $webcomics ); ?>> <?php _e( 'Show webcomics', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="thumbnail"<?php empty( $show_image ) ? '' : checked( $show_image, 'thumbnail' ); ?>> <?php _e( 'Show character avatars', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1"<?php empty( $show_count ) ? '' : checked( $show_count ); ?>> <?php _e( 'Show webcomic counts', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomic_image' ); ?>" value="thumbnail"<?php empty( $webcomic_image ) ? '' : checked( $webcomic_image, 'thumbnail' ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Webcomic Characters widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicCollections extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Collections', 'webcomic' ), array( 'description' => __( 'A list, dropdown, or cloud of Webcomic collections', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::webcomic_list_collections()
	 * @uses WebcomicTag::webcomic_collection_cloud()
	 * @uses WebcomicTag::webcomic_dropdown_collections()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$a      = array_merge( $args, $instance );
		$object = get_queried_object();
		
		unset($a['name']);
		
		$a[ 'target' ]           = empty( $a[ 'target' ] ) ? 'archive' : $a[ 'target' ];
		$a[ 'orderby' ]          = 'name';
		$a[ 'show_option_none' ] = __( 'Select Collection', 'webcomic' );
		
		if ( !empty( $object->query_var ) and preg_match( '/^webcomic\d+$/', $object->query_var ) ) {
			$a[ 'selected' ] = $object->query_var;
		}
		
		if ( 'list' === $format ) {
			$output = WebcomicTag::webcomic_list_collections( $a );
		} elseif ( 'dropdown' === $format ) {
			$output = WebcomicTag::webcomic_dropdown_collections( $a );
		} else {
			$output = WebcomicTag::webcomic_collection_cloud( $a );
		}
		
		if ( $output ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		$old[ 'title' ]          = strip_tags( $new[ 'title' ] );
		$old[ 'format' ]         = $new[ 'format' ];
		$old[ 'target' ]         = $new[ 'target' ];
		$old[ 'webcomics' ]      = $new[ 'webcomics' ];
		$old[ 'show_count' ]     = $new[ 'show_count' ];
		$old[ 'show_image' ]     = $new[ 'show_image' ];
		$old[ 'webcomic_image' ] = $new[ 'webcomic_image' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 */
	public function form( $instance ) {
		extract( $instance );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Format', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'format' ); ?>">
					<option value="list"<?php empty( $format ) ? '' : selected( 'list', $format ); ?>><?php _e( 'List', 'webcomic' ); ?></option>
					<option value="dropdown"<?php empty( $format ) ? '' : selected( 'dropdown', $format ); ?>><?php _e( 'Dropdown', 'webcomic' ); ?></option>
					<option value="cloud"<?php empty( $format ) ? '' : selected( 'cloud', $format ); ?>><?php _e( 'Cloud', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Target', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'target' ); ?>">
					<option value="archive"<?php empty( $format ) ? '' : selected( 'archive', $target ); ?>><?php _e( 'Archive', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $format ) ? '' : selected( 'first', $target ); ?>><?php _e( 'First Webcomic', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $format ) ? '' : selected( 'last', $target ); ?>><?php _e( 'Last Webcomic', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomics' ); ?>" value="1"<?php empty( $webcomics ) ? '' : checked( $webcomics, true ); ?>> <?php _e( 'Show webcomics', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="thumbnail"<?php empty( $show_image ) ? '' : checked( $show_image, 'thumbnail' ); ?>> <?php _e( 'Show collection posters', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1"<?php empty( $show_count ) ? '' : checked( $show_count, true ); ?>> <?php _e( 'Show webcomic counts', 'webcomic' ); ?></label><br>
			<label><input type="checkbox" name="<?php echo $this->get_field_name( 'webcomic_image' ); ?>" value="thumbnail"<?php empty( $webcomic_image ) ? '' : checked( $webcomic_image, 'thumbnail' ); ?>> <?php _e( 'Show webcomic previews', 'webcomic' ); ?></label>
		</p>
		<?php
	}
}

/** Purchase Webcomic Link widget.
 * 
 * @package Webcomic
 */
class Widget_PurchaseWebcomicLink extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Purchase Link', 'webcomic' ), array( 'description' => __( 'Link to the purchase prints page for a webcomic', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$link = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $link ) . '"', $image );
		}
		
		if ( $output = WebcomicTag::purchase_webcomic_link( '%link', $link, get_the_ID() ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'image' ] ) {
			if ( $old[ 'image' ] and $old[ 'image' ] !== $new[ 'image' ] ) {
				delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-purchase-webcomic-link' );
			}
			
			update_post_meta( $new[ 'image' ], '_wp_attachment_context', 'widget-purchase-webcomic-link' );
		} else {
			delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-purchase-webcomic-link' );
		}
		
		$old[ 'title' ]        = strip_tags( $new[ 'title' ] );
		$old[ 'link' ]         = $new[ 'link' ];
		$old[ 'image' ]        = $new[ 'image' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'image' ); ?>"><?php self::ajax_image( empty( $image ) ? 0 : $image, $this->get_field_name( 'image' ), $this->get_field_id( 'image' ) ); ?></span><br>
				<input type="text" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo empty( $link ) ? '' : esc_attr( $link ); ?>" class="widefat">
				<span class="description"><?php _e( 'The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%title</td>
					<td><?php _e( 'Webcomic Title', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%date</td>
					<td><?php _e( 'Publish Date', 'webcomic' ); ?></td>
				</tr>
				<?php
					$count = 1;
					$sizes = '<tr><td>%full</td></tr>';
					
					foreach ( get_intermediate_image_sizes() as $size ) {
						$count++;
						$sizes .= "<tr><td>%{$size}</td></tr>";
					}
					
					echo preg_replace( '/<\/td><\/tr>/', '</td><td rowspan="' . $count . '" style="border-left:thin solid #dfdfdf">' . __( 'Image Preview', 'webcomic' ) . '</td></tr>', $sizes, 1 );
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Handle webcomic link image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Purchase Link Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_PurchaseWebcomicLink::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_PurchaseWebcomicLink::ajax_image" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}

/** Webcomic Storyline Link widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicStorylineLink extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Storyline Link', 'webcomic' ), array( 'description' => __( 'Link to the previous, next, first, last, or a random webcomic storyline', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$collection = $collection ? $collection : WebcomicTag::get_webcomic_collection();
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$link = preg_replace( '/alt=".+?"/', 'alt="' . $link . '"', $image );
		}
		
		if ( $output = WebcomicTag::relative_webcomic_term_link( '%link', $link, $target, $relative, "{$collection}_storyline" ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'image' ] ) {
			if ( $old[ 'image' ] and $old[ 'image' ] !== $new[ 'image' ] ) {
				delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-storyline-link' );
			}
			
			update_post_meta( $new[ 'image' ], '_wp_attachment_context', 'widget-webcomic-storyline-link' );
		} else {
			delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-storyline-link' );
		}
		
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'link' ]       = $new[ 'link' ];
		$old[ 'image' ]      = $new[ 'image' ];
		$old[ 'target' ]     = $new[ 'target' ];
		$old[ 'relative' ]   = $new[ 'relative' ];
		$old[ 'collection' ] = $new[ 'collection' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Storyline', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'relative' ); ?>">
					<option value="previous"<?php empty( $relative ) ? '' : selected( 'previous', $relative ); ?>><?php _e( 'Previous', 'webcomic' ); ?></option>
					<option value="next"<?php empty( $relative ) ? '' : selected( 'next', $relative ); ?>><?php _e( 'Next', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $relative ) ? '' : selected( 'first', $relative ); ?>><?php _e( 'First', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $relative ) ? '' : selected( 'last', $relative ); ?>><?php _e( 'Last', 'webcomic' ); ?></option>
					<option value="random"<?php empty( $relative ) ? '' : selected( 'random', $relative ); ?>><?php _e( 'Random', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Target', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'target' ); ?>">
					<option value="archive"<?php empty( $target ) ? '' : selected( 'archive', $target ); ?>><?php _e( 'Archive', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $target ) ? '' : selected( 'first', $target ); ?>><?php _e( 'First Webcomic', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $target ) ? '' : selected( 'last', $target ); ?>><?php _e( 'Last Webcomic', 'webcomic' ); ?></option>
					<option value="random"<?php empty( $target ) ? '' : selected( 'random', $target ); ?>><?php _e( 'Random Webcomic', 'webcomic' ); ?></option>
				</select>
			</label><br>
			<span class="description"><?php _e( 'Where the storyline link will point to.', 'webcomic' ); ?></span>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'image' ); ?>"><?php self::ajax_image( empty( $image ) ? 0 : $image, $this->get_field_name( 'image' ), $this->get_field_id( 'image' ) ); ?></span><br>
				<input type="text" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo empty( $link ) ? '' : esc_attr( $link ); ?>" class="widefat">
				<span class="description"><?php _e( 'The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%title</td>
					<td><?php _e( 'Storyline Title', 'webcomic' ); ?></td>
				</tr>
				<?php
					$count = 1;
					$sizes = '<tr><td>%full</td></tr>';
					
					foreach ( get_intermediate_image_sizes() as $size ) {
						$count++;
						$sizes .= "<tr><td>%{$size}</td></tr>";
					}
					
					echo preg_replace( '/<\/td><\/tr>/', '</td><td rowspan="' . $count . '" style="border-left:thin solid #dfdfdf">' . __( 'Cover Image', 'webcomic' ) . '</td></tr>', $sizes, 1 );
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Handle webcomic link image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Storyline Link Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_WebcomicStorylineLink::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_WebcomicStorylineLink::ajax_image" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}

/** Webcomic Character Link widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicCharacterLink extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Character Link', 'webcomic' ), array( 'description' => __( 'Link to the previous, next, first, last, or a random webcomic character', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$collection = $collection ? $collection : WebcomicTag::get_webcomic_collection();
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$link = preg_replace( '/alt=".+?"/', 'alt="' . $link . '"', $image );
		}
		
		if ( $output = WebcomicTag::relative_webcomic_term_link( '%link', $link, $target, $relative, "{$collection}_character" ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'image' ] ) {
			if ( $old[ 'image' ] and $old[ 'image' ] !== $new[ 'image' ] ) {
				delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-character-link' );
			}
			
			update_post_meta( $new[ 'image' ], '_wp_attachment_context', 'widget-webcomic-character-link' );
		} else {
			delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-character-link' );
		}
		
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'link' ]       = $new[ 'link' ];
		$old[ 'image' ]      = $new[ 'image' ];
		$old[ 'target' ]     = $new[ 'target' ];
		$old[ 'relative' ]   = $new[ 'relative' ];
		$old[ 'collection' ] = $new[ 'collection' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Character', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'relative' ); ?>">
					<option value="previous"<?php empty( $relative ) ? '' : selected( 'previous', $relative ); ?>><?php _e( 'Previous', 'webcomic' ); ?></option>
					<option value="next"<?php empty( $relative ) ? '' : selected( 'next', $relative ); ?>><?php _e( 'Next', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $relative ) ? '' : selected( 'first', $relative ); ?>><?php _e( 'First', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $relative ) ? '' : selected( 'last', $relative ); ?>><?php _e( 'Last', 'webcomic' ); ?></option>
					<option value="random"<?php empty( $relative ) ? '' : selected( 'random', $relative ); ?>><?php _e( 'Random', 'webcomic' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Target', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'target' ); ?>">
					<option value="archive"<?php empty( $target ) ? '' : selected( 'archive', $target ); ?>><?php _e( 'Archive', 'webcomic' ); ?></option>
					<option value="first"<?php empty( $target ) ? '' : selected( 'first', $target ); ?>><?php _e( 'First Webcomic', 'webcomic' ); ?></option>
					<option value="last"<?php empty( $target ) ? '' : selected( 'last', $target ); ?>><?php _e( 'Last Webcomic', 'webcomic' ); ?></option>
					<option value="random"<?php empty( $target ) ? '' : selected( 'random', $target ); ?>><?php _e( 'Random Webcomic', 'webcomic' ); ?></option>
				</select>
			</label><br>
			<span class="description"><?php _e( 'Where the character link will point to.', 'webcomic' ); ?></span>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'image' ); ?>"><?php self::ajax_image( empty( $image ) ? 0 : $image, $this->get_field_name( 'image' ), $this->get_field_id( 'image' ) ); ?></span><br>
				<input type="text" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo empty( $link ) ? '' : esc_attr( $link ); ?>" class="widefat">
				<span class="description"><?php _e( 'The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%title</td>
					<td><?php _e( 'Character Title', 'webcomic' ); ?></td>
				</tr>
				<?php
					$count = 1;
					$sizes = '<tr><td>%full</td></tr>';
					
					foreach ( get_intermediate_image_sizes() as $size ) {
						$count++;
						$sizes .= "<tr><td>%{$size}</td></tr>";
					}
					
					echo preg_replace( '/<\/td><\/tr>/', '</td><td rowspan="' . $count . '" style="border-left:thin solid #dfdfdf">' . __( 'Avatar Image', 'webcomic' ) . '</td></tr>', $sizes, 1 );
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Handle webcomic link image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Character Link Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_WebcomicCharacterLink::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_WebcomicCharacterLink::ajax_image" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}

/** Webcomic Character Link widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicCollectionLink extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Collection Link', 'webcomic' ), array( 'description' => __( 'Link to a Webcomic collection archive', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		$collection = $collection ? $collection : WebcomicTag::get_webcomic_collection();
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$link = preg_replace( '/alt=".+?"/', 'alt="' . $link . '"', $image );
		}
		
		if ( $output = WebcomicTag::webcomic_collection_link( '%link', $link, $collection ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'image' ] ) {
			if ( $old[ 'image' ] and $old[ 'image' ] !== $new[ 'image' ] ) {
				delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-collection-link' );
			}
			
			update_post_meta( $new[ 'image' ], '_wp_attachment_context', 'widget-webcomic-collection-link' );
		} else {
			delete_post_meta( $old[ 'image' ], '_wp_attachment_context', 'widget-webcomic-collection-link' );
		}
		
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'link' ]       = $new[ 'link' ];
		$old[ 'image' ]      = $new[ 'image' ];
		$old[ 'collection' ] = $new[ 'collection' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label><?php _e( 'Collection', 'webcomic' ); ?><br>
				<select name="<?php echo $this->get_field_name( 'collection' ); ?>">
					<option value=""><?php _e( '(current collection)', 'webcomic' ); ?></option>
					<?php
						foreach ( $collections as $k => $v ) {
							echo '<option value="', $k, '"', empty( $collection ) ? '' : selected( $k, $collection, false ), '>', esc_html( $v[ 'name' ] ), '</option>';
						}
					?>
				</select>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'image' ); ?>"><?php self::ajax_image( empty( $image ) ? 0 : $image, $this->get_field_name( 'image' ), $this->get_field_id( 'image' ) ); ?></span><br>
				<input type="text" name="<?php echo $this->get_field_name( 'link' ); ?>" value="<?php echo empty( $link ) ? '' : esc_attr( $link ); ?>" class="widefat">
				<span class="description"><?php _e( 'The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%title</td>
					<td><?php _e( 'Collection Title', 'webcomic' ); ?></td>
				</tr>
				<?php
					$count = 1;
					$sizes = '<tr><td>%full</td></tr>';
					
					foreach ( get_intermediate_image_sizes() as $size ) {
						$count++;
						$sizes .= "<tr><td>%{$size}</td></tr>";
					}
					
					echo preg_replace( '/<\/td><\/tr>/', '</td><td rowspan="' . $count . '" style="border-left:thin solid #dfdfdf">' . __( 'Poster Image', 'webcomic' ) . '</td></tr>', $sizes, 1 );
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Handle webcomic collection link image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Collection Link Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_WebcomicCollectionLink::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_WebcomicCollectionLink::ajax_image" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}

/** Webcomic Transcripts Link widget.
 * 
 * @package Webcomic
 */
class Widget_WebcomicTranscriptsLink extends WP_Widget {
	/**
	 * Initialize the widget. */
	public function __construct() {
		parent::__construct( false, __( '[Webcomic] Transcripts Link', 'webcomic' ), array( 'description' => __( 'Link to the transcripts for a webcomic', 'webcomic' ) ) );
	}
	
	/**
	 * Render the widget.
	 * 
	 * @param array $args General widget arguments.
	 * @param array $instance Specific instance arguments.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		
		if ( !empty( $image ) and $image = wp_get_attachment_image( $image, 'full' ) ) {
			$link = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $link ) . '"', $image );
		}
		
		if ( $output = WebcomicTag::webcomic_transcripts_link( '%link', $none, $some, $off, $language ) ) {
			echo $before_widget, empty( $title ) ? '' : $before_title . $title . $after_title, $output, $after_widget;
		}
		
	}
	
	/**
	 * Update an instance of the widget.
	 * 
	 * @param array $new New instance settings.
	 * @param array $old Old instance settings.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( $new[ 'off_image' ] ) {
			if ( $old[ 'off_image' ] and $old[ 'off_image' ] !== $new[ 'off_mage' ] ) {
				delete_post_meta( $old[ 'off_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
			}
			
			update_post_meta( $new[ 'off_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
		} else {
			delete_post_meta( $old[ 'off_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
		}
		
		if ( $new[ 'none_image' ] ) {
			if ( $old[ 'none_image' ] and $old[ 'none_image' ] !== $new[ 'none_image' ] ) {
				delete_post_meta( $old[ 'none_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
			}
			
			update_post_meta( $new[ 'none_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
		} else {
			delete_post_meta( $old[ 'none_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
		}
		
		if ( $new[ 'some_image' ] ) {
			if ( $old[ 'some_image' ] and $old[ 'some_image' ] !== $new[ 'some_image' ] ) {
				delete_post_meta( $old[ 'some_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
			}
			
			update_post_meta( $new[ 'some_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
		} else {
			delete_post_meta( $old[ 'some_image' ], '_wp_attachment_context', 'widget-webcomic-transcripts-link' );
		}
		
		$old[ 'title' ]      = strip_tags( $new[ 'title' ] );
		$old[ 'off' ]        = $new[ 'off' ];
		$old[ 'none' ]       = $new[ 'none' ];
		$old[ 'some' ]       = $new[ 'some' ];
		$old[ 'off_image' ]  = $new[ 'image' ];
		$old[ 'none_image' ] = $new[ 'image' ];
		$old[ 'some_image' ] = $new[ 'image' ];
		$old[ 'language' ]   = $new[ 'language' ];
		
		return $old;
	}
	
	/**
	 * Render widget settings.
	 * 
	 * @param array $instance Specific instance settings.
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	public function form( $instance ) {
		extract( $instance );
		
		$collections = WebcomicTag::get_webcomic_collections( true );
		?>
		<p>
			<label>
				<?php _e( 'Title', 'webcomic' ); ?>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo empty( $title ) ? '' : esc_attr( $title ); ?>" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'None Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'none_image' ); ?>"><?php self::ajax_image( empty( $none_image ) ? 0 : $none_image, $this->get_field_name( 'none_image' ), $this->get_field_id( 'none_image' ) ); ?></span><br>
				<input type="text" id="<?php echo $this->get_field_name( 'none' ); ?>" name="<?php echo $this->get_field_name( 'none' ); ?>" value="<?php echo empty( $none ) ? '' : esc_attr( $none ); ?>" class="widefat">
				<span class="description"><?php _e( 'Displayed when transcription is on but no transcripts are available. The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Some Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'some_image' ); ?>"><?php self::ajax_image( empty( $some_image ) ? 0 : $some_image, $this->get_field_name( 'some_image' ), $this->get_field_id( 'some_image' ) ); ?></span><br>
				<input type="text" id="<?php echo $this->get_field_name( 'some' ); ?>" name="<?php echo $this->get_field_name( 'some' ); ?>" value="<?php echo empty( $some ) ? '' : esc_attr( $some ); ?>" class="widefat">
				<span class="description"><?php _e( 'Displayed when one or more transcripts are available. The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<p>
			<label>
				<?php _e( 'Off Link', 'webcomic' ); ?><br>
				<span id="<?php echo $this->get_field_id( 'off_image' ); ?>"><?php self::ajax_image( empty( $off_image ) ? 0 : $off_image, $this->get_field_name( 'off_image' ), $this->get_field_id( 'off_image' ) ); ?></span><br>
				<input type="text" id="<?php echo $this->get_field_name( 'off' ); ?>" name="<?php echo $this->get_field_name( 'off' ); ?>" value="<?php echo empty( $off ) ? '' : esc_attr( $off ); ?>" class="widefat">
				<span class="description"><?php _e( 'Displayed when transcription is turned off. The link text will be used as alternate text if you select an image.', 'webcomic' ); ?></span>
			</label>
		</p>
		<table class="widefat">
			<thead>
				<tr>
					<th><?php _e( 'Token', 'webcomic' ); ?></th>
					<th><?php _e( 'Replacement', 'webcomic' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>%title</td>
					<td><?php _e( 'Webcomic Title', 'webcomic' ); ?></td>
				</tr>
				<tr>
					<td>%date</td>
					<td><?php _e( 'Publish Date', 'webcomic' ); ?></td>
				</tr>
				<?php
					$count = 1;
					$sizes = '<tr><td>%full</td></tr>';
					
					foreach ( get_intermediate_image_sizes() as $size ) {
						$count++;
						$sizes .= "<tr><td>%{$size}</td></tr>";
					}
					
					echo preg_replace( '/<\/td><\/tr>/', '</td><td rowspan="' . $count . '" style="border-left:thin solid #dfdfdf">' . __( 'Image Preview', 'webcomic' ) . '</td></tr>', $sizes, 1 );
				?>
			</tbody>
		</table>
		<?php
	}
	
	/**
	 * Handle webcomic transcript link image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $name The widget field name.
	 * @param string $target The widget field ID.
	 */
	public static function ajax_image( $id, $name, $target ) {
		$target = str_replace( '#', '', $target );
		
		if ( $id ) {
			echo '<a href="', esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ), '">', wp_get_attachment_image( $id ), '</a><br>';
		}
		
		echo '<input type="hidden" name="', $name, '" value="', $id, '"><a class="button webcomic-image" data-title="', __( 'Select a Transcript Link Image', 'webcomic' ), '" data-update="', __( 'Update', 'webcomic' ), '" data-callback="Widget_WebcomicTranscriptsLink::ajax_image" data-name="', $name, '" data-target="#', $target, '" data-webcomic-admin-url="', admin_url(), '">', $id ? __( 'Change', 'webcomic' ) : __( 'Select Image', 'webcomic' ), '</a>';
		
		if ( $id ) {
			echo ' <a class="button webcomic-image-x" data-callback="Widget_WebcomicTranscriptsLink::ajax_image" data-target="#', $target, '">', __( 'Remove', 'webcomic' ), '</a>';
		}
	}
}