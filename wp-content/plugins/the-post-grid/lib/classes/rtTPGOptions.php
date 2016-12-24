<?php

if(!class_exists('rtTPGOptions')):

    class rtTPGOptions
    {

        function rtPostTypes(){
            $post_types =  get_post_types(
                array(
                    '_builtin' => true
                )
            );
            $exclude = array( 'attachment', 'revision', 'nav_menu_item' );
            foreach($exclude as $ex){
                unset($post_types[$ex]);
            }
            return $post_types;
        }

        function  rtPostOrders(){
            return array(
                "ASC" => "Ascending",
                "DESC" => "Descending",
            );
        }

        function rtTermOperators(){
            return array(
                'IN' => "IN — show posts which associate with one or more of selected terms",
                'NOT IN' => "NOT IN — show posts which do not associate with any of selected terms",
                'AND' => "AND — show posts which associate with all of selected terms",
            );
        }
        function rtTermRelations(){
            return array(
                'AND' => "AND — show posts which match all settings",
                'OR' => "OR — show posts which match one or more settings",
            );
        }

        function  rtPostOrderBy(){
            return array(
                "ID" => "ID",
                "title" => "Title",
                "date" => "Created date",
                "modified" => "Modified date",
                "menu_order" => "Menu Order"
            );
        }

        function rtTPGSettingFields(){
            global $rtTPG;
            $settings = get_option($rtTPG->options['settings']);
            return  array(
                'custom_css' => array(
                    'type' => 'custom_css',
                    'name' => 'custom_css',
                    'label' => 'Custom Css',
                    'id'    => 'custom-css',
                    'value' => isset($settings['custom_css']) ? trim($settings['custom_css']) : null,
                ),
            );
        }

        function rtTPGCommonFilterFields(){
            return array(
                'post__in' => array(
                    "name" => "post__in",
                    "label" => "Include only",
                    "type"  => "text",
                    "class" => "full",
                    "description" => __('List of post IDs to show (comma-separated values, for example: 1,2,3)', 'the-post-grid')
                ),
                'post__not_in' => array(
                    "name" => "post__not_in",
                    "label" => "Exclude",
                    "type"  => "text",
                    "class" => "full",
                    "description" => __('List of post IDs to hide (comma-separated values, for example: 1,2,3)', 'the-post-grid')
                ),
                'limit' => array(
                    "name" => "limit",
                    "label" => "Limit",
                    "type"  => "number",
                    "class" => "full",
                    "description" => __('The number of posts to show. Set empty to show all found posts.', 'the-post-grid')
                )
            );
        }

        function rtTPGPostType(){
            return array(
                "name" => "tpg_post_type",
                "label" => "Post Type",
                "type"  => "select",
                "id"    => "rc-sc-post-type",
                "class" => "rt-select2",
                "options"   => $this->rtPostTypes()
            );
        }

        function rtTPAdvanceFilters(){
            return array(
                'type' => "checkbox",
                'name' => "post_filter",
                'label' => "Advanced filters",
                'id' => "post_filter",
                "alignment" => "vertical",
                "multiple" => true,
                "options"   => array(
                    'tpg_taxonomy' => "Taxonomy",
                    'order' => "Order",
                    'author' => "Author",
                    'tpg_post_status' => "Status",
                    's' => "Search"
                ),
            );
        }

        function rtTPGPostStatus(){
            return array(
                'publish' => 'Publish',
                'pending' => 'Pending',
                'draft' => 'Draft',
                'auto-draft' => 'Auto draft',
                'future' => 'Future',
                'private' => 'Private',
                'inherit' => 'Inherit',
                'trash' => 'Trash',
            );
        }

        function rtTPGLayoutSettingFields(){
            global $rtTPG;
            return array(
                'layout' => array(
                    "type"  => "select",
                    "name" => "layout",
                    "label" => "Layout",
                    "id"    => "rt-tpg-sc-layout",
                    "class" => "rt-select2",
                    "options"   => $this->rtTPGLayouts()
                ),
                'isotope-filtering' => array(
                    "type"  => "select",
                    "name" => "isotope_filter",
                    "label" => "Isotope Filter",
                    'holderClass' => "sc-isotope-filter hidden",
                    "id"    => "rt-tpg-sc-isotope-filter",
                    "class" => "rt-select2",
                    "options"   => $rtTPG->rt_get_taxonomy_for_isotope_filter()
                ),
                'column' => array(
                    "type"  => "select",
                    "name" => "column",
                    "label" => "Column",
                    "id"    => "rt-column",
                    "class" => "rt-select2",
                    "default"  => 4,
                    "options"   => $this->rtTPGColumns()
                ),
                'pagination' => array(
                    "type"  => "checkbox",
                    "name" => "pagination",
                    "label" => "Pagination",
                    'holderClass' => "pagination",
                    "id"    => "rt-tpg-pagination",
                    "option"   => 'Enable'
                ),
                'posts_per_page' => array(
                    "type"  => "number",
                    "name" => "posts_per_page",
                    "label" => "Display per page",
                    'holderClass' => "posts-per-page hidden",
                    "id"    => "posts-per-page",
                    "default"   => 5,
                    "description" => __("If value of Limit setting is not blank (empty), this value should be smaller than Limit value.", 'the-post-grid')
                ),
                'featured_image_size' => array(
                    "type"  => "select",
                    "name" => "featured_image_size",
                    "label" => "Feature Image Size",
                    "id"    => "featured-image-size",
                    "class" => "rt-select2",
                    "options"   => $rtTPG->get_image_sizes()
                ),
                'media_source' => array(
                    "type"  => "radio",
                    "name" => "media_source",
                    "label" => "Media Source",
                    "id"    => "media-source",
                    "default" => 'feature_image',
                    "alignment" => "vertical",
                    "options"   => $this->rtMediaSource()
                ),
                'excerpt_limit' => array(
                    "type"  => "number",
                    "name" => "excerpt_limit",
                    "label" => "Excerpt limit",
                    "id"    => "excerpt-limit",
                    "description" => __("Excerpt limit only integer number is allowed, Leave it blank for full excerpt. Note: This will remove all html tag", 'the-post-grid')
                )
            );
        }

        function rtTPGStyleFields(){

            return array(
                'primary_color' => array(
                    "type"  => "text",
                    "name"  => "primary_color",
                    "label" => "Primary Color",
                    "id"    => "primary-color",
                    "class"    => "rt-color",
                    "default"   =>  "#0367bf"
                ),
                'button_bg_color' => array(
                    "type"  => "text",
                    "name"  => "button_bg_color",
                    "label" => "Button background color",
                    "id"    => "button-bg-color",
                    "class"    => "rt-color"
                ),
                'button_hover_bg_color' => array(
                    "type"  => "text",
                    "name"  => "button_hover_bg_color",
                    "label" => "Button hover background color",
                    "id"    => "button-hover-bg-color",
                    "class"    => "rt-color"
                ),
                'button_active_bg_color' => array(
                    "type"  => "text",
                    "name"  => "button_active_bg_color",
                    "label" => "Button active background color",
                    "id"    => "button-active-bg-color",
                    "class"    => "rt-color"
                ),
                'button_text_bg_color' => array(
                    "type"  => "text",
                    "name"  => "button_text_color",
                    "label" => "Button text color",
                    "id"    => "button-text-color",
                    "class"    => "rt-color"
                )
            );

        }

        function itemFields(){
            return array(
                "type" => "checkbox",
                "name" => "item_fields",
                "label" => "Field selection",
                "id"    => "item-fields",
                "multiple" => true,
                "alignment" => "vertical",
                "default" => array_keys($this->rtTPGItemFields()),
                "options" => $this->rtTPGItemFields()
            );
        }

        function rtMediaSource(){
            return array(
                "feature_image" => __("Feature Image", 'the-post-grid'),
                "first_image"   => __("First Image from content", 'the-post-grid')
            );
        }

        function rtTPGColumns(){
            return array(
                1 => "Column 1",
                2 => "Column 2",
                3 => "Column 3",
                4 => "Column 4"
            );
        }
        function rtTPGLayouts(){
            return array(
                'layout1' => "Layout 1",
                'layout2' => "Layout 2",
                'layout3' => "Layout 3",
                'isotope1' => "Isotope Layout"
            );
        }

        function rtTPGItemFields(){
            return array(
                'title' => __( "Title", 'the-post-grid'),
                'excerpt' => __( "Excerpt", 'the-post-grid'),
                'read_more' => __( "Read More", 'the-post-grid'),
                'post_date' => __( "Post Date", 'the-post-grid'),
                'author'    => __( "Author", 'the-post-grid'),
                'categories' => __( "Categories", 'the-post-grid'),
                'tags'  => __( "Tags", 'the-post-grid'),
                'comment_count' => __( "Comment Count", 'the-post-grid')
            );
        }

    }

endif;