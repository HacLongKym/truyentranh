<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Gridster
 */

get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
<?php if ( have_posts() ) : ?>
<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'gridster-lite' ), '<span>' . get_search_query() . '</span>' ); ?></h1>

<?php /* LuanDT change inteface */ ?>
<div class="well well-sm">
    <strong>Display</strong>
    <div class="btn-group">
        <a href="#" id="list" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th-list">
        </span>List</a> <a href="#" id="grid" class="btn btn-default btn-sm"><span
            class="glyphicon glyphicon-th"></span>Grid</a>
    </div>
</div>

<div id="products" class="row list-group">
	<?php /* Start the Loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>
	<?php get_template_part( 'content', 'search' ); ?>
	<?php endwhile; ?>
</div>
<div class="clear"></div>
<?php gridster_content_nav( 'nav-below' ); ?>
<?php else : ?>
<?php get_template_part( 'no-results', 'search' ); ?>
<?php endif; ?>
<?php get_footer(); ?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$('#list').click(function(event){event.preventDefault();$('#products .item').addClass('list-group-item');$('#products .item').removeClass('grid-group-item');$('#products #checkClear').addClass('clear');});
$('#grid').click(function(event){event.preventDefault();$('#products .item').removeClass('list-group-item');$('#products .item').addClass('grid-group-item');$('#products #checkClear').removeClass('clear');});
});
</script>
<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<style>
	#products {
		width: 100%;    
		margin: 0;
	}
	.glyphicon { margin-right:5px; }
	.thumbnail
	{
	    margin-bottom: 20px;
	    padding: 0px;
	    -webkit-border-radius: 0px;
	    -moz-border-radius: 0px;
	    border-radius: 0px;
	}

	.item.list-group-item
	{
	    float: none;
	    width: 100%;
	    background-color: #fff;
	    margin-bottom: 10px;
	}
	.item.list-group-item:nth-of-type(odd):hover,.item.list-group-item:hover
	{
	    background: #428bca;
	}

	.item.list-group-item .list-group-image
	{
	    margin-right: 10px;
	}
	.item.list-group-item .thumbnail
	{
	    margin-bottom: 0px;
	}
	.item.list-group-item .caption
	{
	    padding: 9px 9px 0px 9px;
	    float: left;
	}
	.item.list-group-item:nth-of-type(odd)
	{
	    background: #eeeeee;
	}

	.item.list-group-item:before, .item.list-group-item:after
	{
	    display: table;
	    content: " ";
	}

	.item.list-group-item a > img
	{
	    float: left;
	}
	.item.list-group-item:after
	{
	    clear: both;
	}
	.list-group-item-text
	{
	    margin: 0 0 11px;
	}
	/*** effect rotate-scale ***/

	.img-rotate-scale
	{
	transition:all 0.2s ease-in-out;
	-webkit-transition:all 0.2s ease-in-out;
	-moz-transition:all 0.2s ease-in-out;
	-ms-transition:all 0.2s ease-in-out;
	-o-transition:all 0.2s ease-in-out;
	}

	.img-rotate-scale:hover
	{
	transform:rotate(360deg) scale(1.5,1.5);
	-webkit-transform:rotate(360deg) scale(1.5,1.5);
	-moz-transform:rotate(360deg) scale(1.5,1.5);
	-ms-transform:rotate(360deg) scale(1.5,1.5);
	-o-transform:rotate(360deg) scale(1.5,1.5);
	}
</style>