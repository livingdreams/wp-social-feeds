<?php
/**
 * Admin View: options
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="wrap">

    <form id="wpsf-setting" method="post" action="">

        <h2 class="nav-tab-wrapper"> 
            <?php
            foreach ($this->tabs as $name) {
                echo '<a href="' . admin_url('admin.php?page=wpsf-settings&tab=' . $name) . '" class="nav-tab ' . ( $current == $name ? 'nav-tab-active' : '' ) . '">' . ucfirst($name) . '</a>';
            }
            //do_action('social_feeds_tabs');
            ?>
        </h2>
        <?php //settings_fields('social-feeds-settings'); ?>
        <?php //do_settings_sections('social-feeds-settings'); ?>

        <?php do_action('socialfeeds_options_' . $current); ?>

        <p><?php do_action('socialfeeds_' . $current.'_shortcode'); ?></p>
        
        <p><?php submit_button(__( 'Save Changes' )); ?></p>

    </form>

</div>