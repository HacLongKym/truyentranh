<?php

if(!class_exists('rtTPGHelper')):

    class rtTPGHelper
    {
        function verifyNonce(){
            global $rtTPG;
            $nonce      = isset($_REQUEST[$this->nonceId()]) ? $_REQUEST[$this->nonceId()] : null;
            $nonceText  = $rtTPG->nonceText();
            if( !wp_verify_nonce( $nonce, $nonceText ) ) return false;
            return true;
        }

        function nonceText(){
            return "rttpg_nonce_secret";
        }

        function nonceId(){
            return "rttpg_nonce";
        }

        function rtAllOptionFields(){
            global $rtTPG;
            $fields = array();
            $fieldsA = array_merge(
                $rtTPG->rtTPGCommonFilterFields(),
                $rtTPG->rtTPGLayoutSettingFields(),
                $rtTPG->rtTPGStyleFields()
            );
            foreach($fieldsA as $field){
                $fields[] = $field;
            }
            array_push($fields, $rtTPG->rtTPGPostType());
            array_push($fields, $rtTPG->rtTPAdvanceFilters());
            array_push($fields, $rtTPG->itemFields());
            return $fields;
        }

        function rt_get_all_term_by_taxonomy($taxonomy = null){
            $terms = array();
            if($taxonomy){
                $tList = get_terms( array($taxonomy), array('hide_empty' => 0));
                if(is_array($tList) && !empty($tList) && empty($tList['errors'])){
                    foreach($tList as $term){
                        $terms[$term->term_id] =$term->name;
                    }
                }
            }

            return $terms;
        }

        function rt_get_taxonomy_for_isotope_filter($post_type = null){
            if(!$post_type){
                $post_type = get_post_meta(get_the_ID(), 'tpg_post_type', true);
            }
            if(!$post_type){
                $post_type = 'post';
            }
            return $this->rt_get_all_taxonomy_by_post_type($post_type);
        }

        function rt_get_all_taxonomy_by_post_type($post_type = null){
            $taxonomies = array();
            if($post_type && post_type_exists($post_type)){
                $taxObj = get_object_taxonomies( $post_type, 'objects' );
                if(is_array($taxObj) && !empty($taxObj)){
                    foreach($taxObj as $tKey => $taxonomy){
                        $taxonomies[$tKey] = $taxonomy->label;
                    }
                }
            }
            if($post_type == 'post'){
                unset($taxonomies['post_format']);
            }

            return $taxonomies;
        }

        function rt_get_users(){
            $users = array();
            $u = get_users();
            if(!empty($u)){
                foreach ($u as $user)
                {
                    $users[$user->ID] = $user->display_name;
                }
            }

            return $users;
        }

        function rtFieldGenerator($fields = array(), $multi = false){
            $html = null;
            if(is_array($fields) && !empty($fields)) {
                $rtField = new rtTPGField();
                if ($multi) {
                    foreach ($fields as $field) {
                        $html .= $rtField->Field($field);
                    }
                } else {
                    $html .= $rtField->Field($fields);
                }
            }

            return $html;
        }

        function rtFieldGeneratorBackup($fields = array(), $multi = false){
            $html = null;
            if(is_array($fields) && !empty($fields)) {
                $rtField = new rtTPGField();
                if ($multi) {
                        $i = 0;
                        $trigger = 0;
                        foreach ($fields as $field) {
                            $html .= ($trigger == 0 ? "<div class='rt-row'>" : null);
                            $html .= $rtField->Field($field);
                            $i++;
                            $trigger++;
                            if ($trigger == 2 || count($fields) == $i) {
                                $html .= "</div>";
                                $trigger = 0;
                            }
                        }
                } else {
                    $html .= "<div class='rt-row'>";
                        $html .= $rtField->Field($fields);
                    $html .= "</div>";
                }
            }

            return $html;
        }

        function get_image_sizes() {
            global $_wp_additional_image_sizes;

            $sizes = array();

            foreach ( get_intermediate_image_sizes() as $_size ) {
                if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
                    $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
                    $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
                    $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
                } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                    $sizes[ $_size ] = array(
                        'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
                        'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                        'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
                    );
                }
            }

            $imgSize = array();
            foreach($sizes as $key => $img){
                $imgSize[$key] = ucfirst($key)." ({$img['width']}*{$img['height']})";
            }

            return $imgSize;
        }

        function getFeatureImageSrc($post_id = null, $fImgSize = 'medium', $mediaSource ='feature_image'){
            $imgSrc = null;
            if($mediaSource == 'feature_image'){
                if ( $aID = get_post_thumbnail_id( $post_id )){
                    $image = wp_get_attachment_image_src( $aID , $fImgSize);
                    $imgSrc = $image[0];
                }
            }else if($mediaSource == 'first_image'){
                if ($img = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_the_content($post_id), $matches))
                    $imgSrc = $matches[1][0];
            }

            return $imgSrc;
        }
        function strip_tags_content($text, $limit = 0, $tags = '', $invert = FALSE) {

            /*preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
            $tags = array_unique($tags[1]);

            if(is_array($tags) AND count($tags) > 0) {
                if($invert == FALSE) {
                    $text = preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
                }
                else {
                    $text = preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
                }
            }elseif($invert == FALSE) {
                $text = preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
            }*/
            $text = strip_tags($text);
            if($limit > 0 && strlen($text) > $limit){
                $text = substr($text, 0, $limit);
            }
            return $text;
        }



        function rt_pagination($pages = '', $range = 4) {

            $html = null;
            $showitems = ($range * 2) + 1;
            global $paged;
            if(empty($paged)) $paged = 1;
            if($pages == ''){
                global $wp_query;
                $pages = $wp_query->max_num_pages;
                if(!$pages)
                {
                    $pages = 1;
                }
            }

            if(1 != $pages){

                $html .= '<div class="rt-pagination">';
                $html .= '<ul class="pagination">';

                if($paged > 1 && $showitems < $pages) $html .= "<li><a href='".get_pagenum_link($paged - 1)."' aria-label='Previous'><i class='fa fa-chevron-left' aria-hidden='true'></i></li>";

                for ($i=1; $i <= $pages; $i++){

                    if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
                    {
                        $html .= ($paged == $i)? "<li class=\"active\"><span>".$i."</span>

    </li>":"<li><a href='".get_pagenum_link($i)."'>".$i."</a></li>";

                    }

                }

                if ($paged < $pages && $showitems < $pages) $html .= "<li><a href=\"".get_pagenum_link($paged + 1)."\"  aria-label='Next'><i class='fa fa-chevron-right' aria-hidden='true'></i></a></li>";

                $html .= "</ul>";
                $html .= "</div>";
            }

            return $html;

        }

    }

endif;