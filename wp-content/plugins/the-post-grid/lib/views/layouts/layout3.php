<?php

$html = null;

$html .= "<div class='{$grid} {$class}'>";
    $html .= '<div class="rt-holder">';
        $html .= '<div class="row">';
            $html .= "<div class='{$image_area}'>";
                $html .= '<div class="rt-img-holder">';
                    $html .= '<div class="overlay">';
                        $html .= "<div class='link-holder'>
                                        <a class='view-details' href='{$pLink}'><i class='fa fa-info'></i></a>
                                    </div>";
                    $html .= '</div>';
                    $html .= "<a href='{$pLink}'><img class='img-responsive rounded' src='{$imgSrc}' alt='{$title}'></a>";
                $html .= '</div>';
            $html .= '</div>';
            $html .= "<div class='{$content_area}'>";
                $html .= '<div class="rt-detail">';
                        $metaHtml = null;
                        if(in_array('post_date', $items) && $date){
                            $metaHtml .= "<span class='date-meta'><i class='fa fa-calendar'></i> {$date}</span>";
                        }
                        if(in_array('author', $items)){
                            $metaHtml .= "<span class='author'><i class='fa fa-user'></i>{$author}</span>";
                        }
                        if(in_array('categories', $items) && $categories){
                            $metaHtml .= "<span class='categories-links'><i class='fa fa-folder-open-o'></i>{$categories}</span>";
                        }
                        if(in_array('tags', $items) && $tags){
                            $metaHtml .= "<span class='post-tags-links'><i class='fa fa-tags'></i>{$tags}</span>";
                        }
                        if(in_array('comment_count', $items) && $comment){
                            $metaHtml .= "<span class='comment-link'><i class='fa fa-comments-o'></i>{$comment}</span>";
                        }
                        if(!empty($metaHtml)){
                            $html .="<div class='post-meta-user'>{$metaHtml}</div>";
                        }

                        if(in_array('excerpt', $items)){
                            $html .= "<div class='post-content'>{$excerpt}</div>";
                        }
                        if(in_array('read_more', $items)){
                            $html .= "<span class='read-more'><a href='{$pLink}'>".__("Read More ..","the-post-grid")."</a></span>";
                        }
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
    $html .= '</div>';
$html .='</div>';

echo $html;