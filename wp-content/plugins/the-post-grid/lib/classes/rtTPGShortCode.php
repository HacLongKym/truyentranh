<?php

if(!class_exists('rtTPGShortCode')):

    class rtTPGShortCode
    {

        private $scA = array();

        function __construct()
        {
            add_shortcode( 'the-post-grid', array( $this, 'the_post_grid_short_code' ) );
        }
        function register_sc_scripts(){
            global $rtTPG;
            $iso = false;

            foreach($this->scA as $sc){
                if(isset($sc) && is_array($sc)) {
                    if ($sc['isIsotope']) {
                        $iso = true;
                    }
                }
            }
            if(count($this->scA)){
                if($iso){
	                wp_enqueue_script('rt-isotope-js');
                }
	            wp_enqueue_style('rt-fontawsome');
	            wp_enqueue_script('rt-image-load-js');
	            wp_enqueue_script('rt-actual-height-js');
                wp_enqueue_script('rt-tpg-js');
                $nonce = wp_create_nonce( $rtTPG->nonceText() );
                wp_localize_script( 'rt-tpg-js', 'rttpg',
                    array(
                        'nonceID' => $rtTPG->nonceId(),
                        'nonce' => $nonce,
                        'ajaxurl' => admin_url( 'admin-ajax.php' )
                    ) );
            }
        }

        function the_post_grid_short_code($atts, $content = null){
            $rand = mt_rand();
            $layoutID = "rt-tpg-container-".$rand;
            global $rtTPG;
            $html = null;
            $arg= array();
            $atts = shortcode_atts( array(
                'id' => null
            ), $atts, 'the-post-grid' );
            $scID =  $atts['id'];
            if($scID && !is_null(get_post( $scID ))){
                $scMeta = get_post_meta($scID);

                $layout = (isset($scMeta['layout'][0]) ? $scMeta['layout'][0] : 'layout1');
                if(!in_array($layout, array_keys($rtTPG->rtTPGLayouts())) ){
                    $layout = 'layout1';
                }
                $col = (isset($scMeta['column'][0]) ? intval($scMeta['column'][0]) : 4);
                if (!in_array($col, array_keys($rtTPG->rtTPGColumns())) ) {
                    $col = 4;
                }
                $fImgSize = (isset($scMeta['featured_image_size'][0]) ? $scMeta['featured_image_size'][0] : "medium");
                $mediaSource = (isset($scMeta['media_source'][0]) ? $scMeta['media_source'][0] : "feature_image");
                $excerpt_limit = (isset($scMeta['excerpt_limit'][0]) ? absint($scMeta['excerpt_limit'][0]) : 0);


                $isIsotope = preg_match('/isotope/', $layout);

                /* Argument create */
                $args = array();
                $itemIdsArgs = array();


                $postType = (isset($scMeta['tpg_post_type'][0]) ? $scMeta['tpg_post_type'][0] : null);
                if($postType){
                    $args['post_type'] = $itemIdsArgs['post_type'] = $postType;
                }

                // Common filter
                /* post__in */
                $post__in = (isset($scMeta['post__in'][0]) ? $scMeta['post__in'][0] : null);
                if($post__in){
                    $post__in = explode(',', $post__in);
                    $args['post__in'] = $itemIdsArgs['post__in'] = $post__in;
                }
                /* post__not_in */
                $post__not_in = (isset($scMeta['post__not_in'][0]) ? $scMeta['post__not_in'][0] : null);
                if($post__not_in){
                    $post__not_in = explode(',', $post__not_in);
                    $args['post__not_in'] = $itemIdsArgs['post__not_in'] = $post__not_in;
                }

                /* LIMIT */
                $limit = (!empty($scMeta['limit'][0]) ? ($scMeta['limit'][0] == -1 ? 10000000 : (int)$scMeta['limit'][0]) : 10000000);
                $args['posts_per_page'] = $itemIdsArgs['posts_per_page'] = $limit;
                $pagination = false;
                if(!$isIsotope){
                    $pagination  = (isset($scMeta['pagination'][0]) ? $scMeta['pagination'][0] : false);
                    if($pagination) {

                        $posts_per_page  = (isset($scMeta['posts_per_page'][0]) ? intval($scMeta['posts_per_page'][0]) : $limit);
                        if ( $posts_per_page > $limit ) {
                            $posts_per_page = $limit;
                        }
                        // Set 'posts_per_page' parameter
                        $args[ 'posts_per_page' ] = $posts_per_page;

                        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

                        $offset = $posts_per_page * ( (int) $paged - 1 );
                        $args['paged'] = $paged;

                        // Update posts_per_page
                        if ( intval( $args[ 'posts_per_page' ] ) > $limit - $offset ) {
                            $args[ 'posts_per_page' ] = $limit - $offset;
                        }

                    }
                }






                // Advance Filter
                $adv_filter = (isset($scMeta['post_filter']) ? $scMeta['post_filter'] : array());

                // Taxonomy
                $taxQ = array();
                if(in_array('tpg_taxonomy', $adv_filter) && isset($scMeta['tpg_taxonomy'])){

                    if(is_array($scMeta['tpg_taxonomy']) && !empty($scMeta['tpg_taxonomy'])){
                        foreach($scMeta['tpg_taxonomy'] as $taxonomy){
                            $terms = (isset($scMeta['term_'.$taxonomy]) ? $scMeta['term_'.$taxonomy] : array());
                            if(is_array($terms) && !empty($terms)){
                                $operator  = (isset($scMeta['term_operator_'.$taxonomy][0]) ? $scMeta['term_operator_'.$taxonomy][0] : "IN");
                                $taxQ[] = array(
                                    'taxonomy' => $taxonomy,
                                    'field' => 'term_id',
                                    'terms' => $terms,
                                    'operator' => $operator,
                                );
                            }
                        }
                    }
                    if(count($taxQ) >= 2){
                        $relation  = (isset($scMeta['taxonomy_relation'][0]) ? $scMeta['taxonomy_relation'][0] : "AND");
                        $taxQ['relation'] = $relation;
                    }
                }

                if(!empty($taxQ)){
                    $args['tax_query'] = $itemIdsArgs['tax_query']   = $taxQ;
                }

                // Order
                if(in_array('order', $adv_filter)){
                    $order_by = (isset($scMeta['order_by'][0]) ? $scMeta['order_by'][0] : null);
                    $order	= (isset($scMeta['order'][0]) ? $scMeta['order'][0] : null);
                    if($order){
                        $args['order']   = $itemIdsArgs['order'] = $order;
                    }if($order_by){
                        $args['orderby'] = $itemIdsArgs['orderby'] = $order_by;
                    }
                }
                // Status
                if(in_array('tpg_post_status', $adv_filter)){
                    $post_status = (isset($scMeta['tpg_post_status']) ? $scMeta['tpg_post_status'] : array());
                    if(!empty($post_status)){
                        $args['post_status'] = $itemIdsArgs['post_status'] = $post_status;
                    }else {
                        $args['post_status'] = $itemIdsArgs['post_status'] = 'publish';
                    }
                }
                // Author
                $author = (isset($scMeta['author']) ? $scMeta['author'] : array());
                if(in_array('author', $adv_filter) && !empty($author)){
                    $args['author__in'] = $itemIdsArgs['author__in'] = $author;
                }
                // Search
                $s = (isset($scMeta['s'][0]) ? $scMeta['s'][0] : array());
                if(in_array('s', $adv_filter) && !empty($s)){
                    $args['s'] = $itemIdsArgs['s'] = $s;
                }

                // Validation
                if (!in_array($col, array_keys($rtTPG->rtTPGColumns())) ) {
                    $col = 4;
                }

                if(!in_array($layout, array_keys($rtTPG->rtTPGLayouts())) ){
                    $layout = 'layout1';
                }

                if(($layout == 'layout2')|| ($layout=='layout3')){
                    if($col==2){
                        $arg['image_area']="rt-col-lg-5 rt-col-md-5 rt-col-sm-6 rt-col-xs-12 ";
                        $arg['content_area']="rt-col-lg-7 rt-col-md-7 rt-col-sm-6 rt-col-xs-12 ";
                    }else{
                        $arg['image_area']="rt-col-lg-4 rt-col-md-4 rt-col-sm-6 rt-col-xs-12 ";
                        $arg['content_area']="rt-col-lg-8 rt-col-md-8 rt-col-sm-6 rt-col-xs-12 ";
                    }
                }
                $col = round(12/$col);
                if(($layout == 'layout2')|| ($layout=='layout3')){
                    $arg['grid'] = "rt-col-lg-{$col} rt-col-md-{$col} rt-col-sm-12 rt-col-xs-12";
                }else{
                    $arg['grid'] = "rt-col-lg-{$col} rt-col-md-{$col} rt-col-sm-6 rt-col-xs-12";
                }
                

                $arg['class'] = 'equal-height';
                if($isIsotope){
                    $arg['class'] .= ' isotope-item';
                }
                $arg['items'] = isset($scMeta['item_fields']) ? ($scMeta['item_fields'] ? $scMeta['item_fields'] : array()) : array();
                $postQuery = new WP_Query( $args );
                // Start layout
                $html .="<div class='container-fluid rt-tpg-container' id='{$layoutID}'>";
                $extClass = null;
                if($isIsotope){
                    $extClass = ' tpg-isotope';
                }
                    $html .="<div class='row {$layout}{$extClass}'>";
                        if ( $postQuery->have_posts() ) {
                            $html .= $this->layoutStyle($layoutID, $scMeta);

                            if($isIsotope) {
                                $isotope_filter = isset($scMeta['isotope_filter'][0]) ? $scMeta['isotope_filter'][0] : null;
                                $selectedTerms = (isset($scMeta['term_'.$isotope_filter]) ? $scMeta['term_'.$isotope_filter] : array());
                                $terms = get_terms( $isotope_filter, array(
                                    'orderby'           => 'name',
                                    'order'             => 'ASC',
                                    'hide_empty'        => false,
                                    'include'           => $selectedTerms
                                ));

                                $html .= '<div id="iso-button-'.$rand.'" class="rt-tpg-isotope-buttons button-group filter-button-group option-set">
											<button data-filter="*" class="selected">'.__('Show all', 'the-post-grid').'</button>';
                                if(! empty( $terms ) && ! is_wp_error( $terms )){
                                    foreach ( $terms as $term ) {
                                        $html .= "<button data-filter='.iso_{$term->term_id}'>" . $term->name . "</button>";
                                    }
                                }
                                $html .='</div>';

                                $html .= '<div class="rt-tpg-isotope" id="iso-tpg-'.$rand.'">';
                            }


                            while ($postQuery->have_posts()) : $postQuery->the_post();
                                $pID = get_the_ID();
                                $arg['pID'] = $pID;
                                $arg['title'] = get_the_title();
                                $arg['pLink'] = get_permalink();
                                $arg['author'] = '<a href="'.get_author_posts_url( get_the_author_meta( 'ID' ) ).'">'.get_the_author().'</a>';
                                $cc = wp_count_comments($pID);
                                $arg['date'] = get_the_date();
                                $excerpt = get_the_excerpt();
                                if($excerpt_limit){
                                    $arg['excerpt'] = $rtTPG->strip_tags_content($excerpt, $excerpt_limit);
                                }else{
                                    $arg['excerpt'] = $excerpt;
                                }
                                $arg['categories'] = get_the_term_list($pID, 'category', null, ', ');
                                $arg['tags'] = get_the_term_list($pID, 'post_tag', null, ', ');
                                if($isIsotope){
                                    $isotope_filter = isset($scMeta['isotope_filter'][0]) ? $scMeta['isotope_filter'][0] : null;
                                    $termAs = wp_get_post_terms($pID, $isotope_filter, array("fields" => "all"));
                                    $isoFilter = null;
                                    if(!empty($termAs)){
                                        foreach($termAs as $term){
                                            $isoFilter .= " iso_".$term->term_id;
                                        }
                                    }
                                    $arg['isoFilter'] = $isoFilter;
                                }
                                $deptClass = null;
                                if(!empty($deptAs)){
                                    foreach($deptAs as $dept){
                                        $deptClass .= " ".$dept->slug;
                                    }
                                }
                                if(comments_open()){
                                    $arg['comment'] = "<a href='".get_comments_link( $pID )."'>{$cc->total_comments} </a>";
                                }else{
                                    $arg['comment'] = "{$cc->total_comments}";
                                }
                                $imgSrc = null;

                                $arg['imgSrc'] = $rtTPG->getFeatureImageSrc($pID,$fImgSize, $mediaSource);

                                $html .= $rtTPG->render('layouts/'.$layout, $arg, true);

                            endwhile;

                            if($isIsotope){
                                $html .= '</div>'; // End isotope item holder
                            }

                        }else{
                            $html .= "<p>".__('No post found','the-post-grid')."</p>";
                        }
                    $html .="</div>"; // End row
                    if($pagination && !$isIsotope){
                        $found = 0;
                        if($postQuery->found_posts > $limit){
                            $found = $limit;
                        }else{
                            $found = $postQuery->found_posts;
                        }
                        $max_num_pages = ceil($found / $posts_per_page);
                        $html .= $rtTPG->rt_pagination($max_num_pages, $args[ 'posts_per_page' ]);
                    }
                $html .="</div>"; // container rt-tpg

                wp_reset_postdata();

                $scriptGenerator = array();
                $scriptGenerator['layout'] = $layoutID;
                $scriptGenerator['rand'] = $rand;
                $scriptGenerator['scMeta'] = $scMeta;
                $scriptGenerator['isIsotope'] = $isIsotope;
                $this->scA[] = $scriptGenerator;
                add_action( 'wp_footer', array($this, 'register_sc_scripts'));
            }else{
                $html .="<p>No shortCode found</p>";
            }
            return $html;
        }

        private function layoutStyle($layout, $scMeta)
        {
            $css = null;
            $css .= "<style type='text/css' media='all'>";
            // Variable
            $primaryColor = (isset($scMeta['primary_color'][0]) ? $scMeta['primary_color'][0] : null);
                if($primaryColor){
                    $css .= "#{$layout} .rt-detail i,#{$layout} .rt-detail .post-meta-user a,#{$layout} .rt-detail .post-meta-category a{";
                    $css .= "color:" . $primaryColor.";";
                    $css .="}";
                    $css .= "body .rt-tpg-container .rt-tpg-isotope-buttons .selected{";
                    $css .= "background-color:" . $primaryColor.";";
                    $css .="}";
                    $css .= "#{$layout} .rt-detail .read-more{";
                    $css .= "border: 1px solid " . $primaryColor.";";
                    $css .="}";
                }
            $button_bg_color = (isset($scMeta['button_bg_color'][0]) ? $scMeta['button_bg_color'][0] : null);
                if($button_bg_color){
                    $css .= "#{$layout} .pagination li a,#{$layout} .rt-tpg-isotope-buttons button,#{$layout} .rt-detail .read-more{";
                    $css .= "background-color:" . $button_bg_color.";";
                    $css .="}";
                }
            $button_hover_bg_color = (isset($scMeta['button_hover_bg_color'][0]) ? $scMeta['button_hover_bg_color'][0] : null);
                if($button_hover_bg_color){
                    $css .= "#{$layout} .pagination li a:hover,#{$layout} .rt-tpg-isotope-buttons button:hover,#{$layout} .rt-detail .read-more:hover{";
                    $css .= "background-color:" . $button_hover_bg_color.";";
                    $css .="}";
                }
            $button_active_bg_color = (isset($scMeta['button_active_bg_color'][0]) ? $scMeta['button_active_bg_color'][0] : null);
                if($button_active_bg_color){
                    $css .= "#{$layout} .pagination li.active span, #{$layout} .rt-tpg-isotope-buttons button.selected{";
                    $css .= "background-color:" . $button_active_bg_color.";";
                    $css .="}";
                }
            $button_text_color = (isset($scMeta['button_text_color'][0]) ? $scMeta['button_text_color'][0] : null);
                if($button_text_color){
                    $css .= "#{$layout} .pagination li a,#{$layout} .rt-tpg-isotope-buttons button,#{$layout} .rt-detail .read-more a{";
                    $css .= "color:" . $button_text_color.";";
                    $css .="}";
                }

            $css .= "</style>";
            return $css;
        }
    }
endif;
