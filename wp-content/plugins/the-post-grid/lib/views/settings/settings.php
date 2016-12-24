<?php
global $rtTPG;
?>

<div class="wrap">
<div class="width50">
    <div id="upf-icon-edit-pages" class="icon32 icon32-posts-page"><br /></div>
    <h2><?php _e('The Post Grid Settings', 'the-post-grid'); ?></h2>
    <h3><?php _e('General settings', 'the-post-grid');?>
        <a style="margin-left: 15px; font-size: 15px;" href="http://demo.radiustheme.com/wordpress/plugins/the-post-grid/" target="_blank"><?php _e('Documentation',  'the-post-grid') ?></a>
    </h3>

    <div class="rt-setting-wrapper">
        <div class="rt-response"></div>
        <form id="rt-settings-form" onsubmit="rtTPGSettings(this); return false;">
            <div class="rt-setting-holder">
                <?php echo $rtTPG->rtFieldGenerator($rtTPG->rtTPGSettingFields(), true); ?>
            </div>

            <p class="submit"><input type="submit" name="submit" class="button button-primary rtSaveButton" value="Save Changes"></p>

            <?php wp_nonce_field( $rtTPG->nonceText(), $rtTPG->nonceId() ); ?>
        </form>

        <div class="rt-response"></div>
    </div>
</div>
<div class="width50">
    <div class="pro-features">
    <h3>PRO Version Features</h3>
    <ol>
        <li>Fully responsive and mobile friendly.</li>
        <li>48 Different Layouts</li>
        <li>Even and Masonry Grid.</li>
        <li>WooCommerce supported.</li>
        <li>Custom Post Type Supported</li>
        <li>Display posts by any Taxonomy like category(s), tag(s), author(s), keyword(s)</li>
        <li>Order by Id, Title, Created date, Modified date and Menu order.</li>
        <li>Display image size (thumbnail, medium, large, full)</li>
        <li>Isotope filter for any taxonomy ie. categories, tags...</li>
        <li>Query Post with Relation.</li>
        <li>Fields Selection.</li>
        <li>All Text and Color control.</li>
        <li>Enable/Disable Pagination.</li>
        <li>AJAX Pagination (Load more and Load on Scrolling)</li>
        <li> and many more .......</li>
        </ol>
        <p><a href="https://www.radiustheme.com/the-post-grid-pro-for-wordpress/" class="button-link" target="_blank">Get Pro Version</a></p>
    </div>
</div>
    <div class="rt-help">
        <p style="font-weight: bold"><?php _e('Short Code', 'the-post-grid' );?> :</p>
        <code>[the-post-grid id="581" title="Home page post List"]</code><br>
        <p><?php _e('id = short code id (1,2,3,4)', 'the-post-grid' );?></p>
        <p><?php _e('title = Shot code title (Not recommended)', 'the-post-grid' );?></p>
        <p class="rt-help-link"><a class="button-primary" href="http://demo.radiustheme.com/wordpress/plugins/the-post-grid/" target="_blank"><?php _e('Demo', 'the-post-grid' );?></a> <a class="button-primary" href="https://radiustheme.com/how-to-setup-configure-the-post-grid-free-version-for-wordpress/" target="_blank"><?php _e('Documentation', 'the-post-grid' );?></a> </p>
    </div>


</div>
