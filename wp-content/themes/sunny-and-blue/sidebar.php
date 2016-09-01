<?php
/**
 * The template for the sidebar containing the main widget area
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */
?>
<!-- sidebar -->
<aside class="col-md-4 col-sm-8 col-xs-8">
    <div class="sidebar">

        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar()) : ?>
        <?php endif; ?>

    </div>
</aside>
<!-- end of sidebar -->