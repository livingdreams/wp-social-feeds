<?php

/**
 * Social Feeds  General Settings
 *
 * @author      Amal Ranganath
 * @category    Admin
 * @package     WPSocialFeeds/Admin
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WPSF_GeneralOptions')) :

    /**
     * WP Social Feeds General Options class
     */
    class WPSF_GeneralOptions extends WPSF_Admin {

        public function __construct() {
            $this->id = 'general';
            add_action('socialfeeds_options_' . $this->id, array($this, 'render'));
        }

        public function get_options() {
            $options = array(
                array('type' => 'section', 'title' => __('General Options', 'socialfeeds'), 'desc' => 'general', 'id' => 'general_options'),
                array(
                    'type' => 'text',
                    'id' => 'name',
                    'title' => __('Name', 'socialfeeds'),
                    'desc' => __('Just a name.', 'socialfeeds'),
                    'default' => 'Amal',
                ),
                array(
                    'type' => 'email',
                    'id' => 'email',
                    'title' => __('Email', 'socialfeeds'),
                    'desc' => __('Just an email.', 'socialfeeds'),
                    'default' => '@aasd',
                    'htmloptions' => array('class' => "form-control"),
                ),
                array('type' => 'sectionend', 'id' => 'general_options'),
            );

            $this->options = apply_filters('socialfeeds_get_options_' . $this->id, $options);
        }

    }

    endif;
return new WPSF_GeneralOptions();
