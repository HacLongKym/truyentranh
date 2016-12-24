<?php

if(!class_exists('rtTPGFrontEnd')):

    class rtTPGFrontEnd
    {
        function __construct()
        {
            add_action('wp_footer', array($this, 'tpg_custom_css'));
            add_action( 'wp_enqueue_scripts', array($this, 'rt_tpg_enqueue_styles' ));

        }

        function rt_tpg_enqueue_styles(){
            wp_enqueue_style('rt-tpg-css');
        }

        function tpg_custom_css(){
            $html = null;
            global $rtTPG;
            $settings = get_option($rtTPG->options['settings']);
            $css = isset($settings['custom_css']) ? trim($settings['custom_css']) : null;
            if($css){
                $html .= "<style type='text/css' media='all'>";
                    $html .= $css;
                $html .= "</style>";
            }

            echo $html;
        }
    }
endif;