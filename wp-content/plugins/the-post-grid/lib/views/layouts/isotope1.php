<?php

$html = null;

$html .= "<div class='{$grid} {$class} {$isoFilter}'>";
    $html .= '<div class="rt-holder">';
        $html .= '<div class="rt-img-holder">';
            $html .= '<div class="overlay">';
                $html .= "<a class='view-details' href='{$pLink}'>
                            <i class='fa fa-info'></i>
                        </a>";
            $html .= '</div>'; 
            $html .= "<a href='{$pLink}'><img class='img-responsive' src='{$imgSrc}' alt='{$title}'></a>";
        $html .= '</div> ';

        $html .= '<div class="rt-detail">';
            if(in_array('title', $items)){
                $html .= "<h2 class='entry-title'><a href='{$pLink}'>{$title}</a></h2>";
            }
            $postMetaTop = $postMetaMid = null;

            if(in_array('author', $items)){
                $postMetaTop .= "<span class='author'><i class='fa fa-user'></i>{$author}</span>";
            }
            if(in_array('post_date', $items) && $date){
                $postMetaTop .= "<span class='date'><i class='fa fa-calendar'></i>{$date}</span>";
            }
            if(in_array('comment_count', $items) && $comment){
                $postMetaTop .= "<span class='comment-link'><i class='fa fa-comments-o'></i>{$comment}</span>";
            }

            if(in_array('categories', $items) && $categories){
                $postMetaTop .= "<span class='categories-links'><i class='fa fa-folder-open-o'></i>{$categories}</span>";
            }
            if(in_array('tags', $items) && $tags){
                $postMetaMid .= "<span class='post-tags-links'><i class='fa fa-tags'></i>{$tags}</span>";
            }

            if(!empty($postMetaTop)){
                $html .= "<div class='post-meta-user'>{$postMetaTop}</div>";
            }
            if(!empty($postMetaMid)){
                $html .= "<div class='post-meta-tags'>{$postMetaMid}</div>";
            }
            
            
        $html .= '</div>'; 
    $html .= '</div>';
$html .='</div>';

echo $html;