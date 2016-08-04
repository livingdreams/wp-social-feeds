<?php

/**
 * Social Feeds Facebook Settings
 *
 * @author      Amal Ranganath
 * @category    Admin
 * @package     WPSocialFeeds/WPSF_Facebook
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WPSF_Facebook')) :

    /**
     * WP Social Feeds Facebook class
     */
    class WPSF_Facebook extends WPSF_Admin {

        public function __construct() {
            $this->id = 'facebook';
            
            add_action('socialfeeds_options_' . $this->id, array($this, 'render'));
            // action for save feed
            
            add_action('socialfeeds_' . $this->id . '_shortcode', array($this, 'shortcode'));
        }

        
        public function get_options() {
            $options = array(
                array('type' => 'section', 'title' => __('Facebook Options', 'socialfeeds'), 'desc' => 'Facebook', 'id' => 'general_options'),
                array(
                    'type' => 'text',
                    'id' => 'page_id',
                    'title' => __('Facebook Page ID', 'socialfeeds'),
                    'desc' => __('Facebook Page ID.', 'socialfeeds'),
                    'default' => 'Amal',
                ),
                array(
                    'type' => 'text',
                    'id' => 'token',
                    'title' => __('Access Token', 'socialfeeds'),
                    'desc' => __('Facebook Access Token.', 'socialfeeds'),
                    'default' => '@aasd',
                    'htmloptions' => array('class' => "form-control"),
                ),
                array(
                    'type' => 'select',
                    'id' => 'limit',
                    'title' => __('Number of posts', 'socialfeeds'),
                    'desc' => __('Number of posts to display.', 'socialfeeds'),
                    'default' => '10',
                    'options' => array(5 => "5", 8 => "8", 10 => "10"),
                ),
                array(
                    'type' => 'anchor',
                    'id' => 'save_btn',
                    'title' => __('Save Feed', 'socialfeeds'),
                    'desc' => __('', 'socialfeeds'),
                    'htmloptions' => array('class' => 'button-primary')
                ),
                array('type' => 'sectionend', 'id' => 'general_options'),
                array('type' => 'section', 'title' => __('Style Options', 'socialfeeds'), 'desc' => 'Style', 'id' => 'general_options'),
                array(
                    'type' => 'select',
                    'id' => 'title_tag',
                    'title' => __('Title Tag', 'socialfeeds'),
                    'desc' => __('Title Tag.', 'socialfeeds'),
                    'default' => 'h2',
                    'options' => array('h1' => "h1", 'h2' => "h2", 'h3' => "h3", 'h4' => "h4", 'h5' => "h5"),
                ),
                array(
                    'type' => 'text',
                    'id' => 'margin',
                    'title' => __('Wraper margin', 'socialfeeds'),
                    'desc' => __('Wraper margin.', 'socialfeeds'),
                    'default' => '0px',
                ),
                array(
                    'type' => 'text',
                    'id' => 'padding',
                    'title' => __('Wraper padding', 'socialfeeds'),
                    'desc' => __('Wraper padding.', 'socialfeeds'),
                    'default' => '0px',
                ),
                array(
                    'type' => 'text',
                    'id' => 'border_size',
                    'title' => __('Border Size', 'socialfeeds'),
                    'desc' => __('Border Size.', 'socialfeeds'),
                    'default' => '0px',
                ),
                array(
                    'type' => 'color',
                    'id' => 'border_color',
                    'title' => __('Border Color ', 'socialfeeds'),
                    'default' => '',
                ),
                array(
                    'type' => 'text',
                    'id' => 'border_radius',
                    'title' => __('Border Radius', 'socialfeeds'),
                    'desc' => __('Border Radius.', 'socialfeeds'),
                    'default' => '0px',
                ),
                array('type' => 'sectionend', 'id' => 'general_options'),
                array('type' => 'section', 'id' => 'general_options'),
                array(
                    'type' => 'checkbox',
                    'id' => 'image_size',
                    'title' => __('Use Custom Sizes', 'socialfeeds'),
                    'desc' => __('Use Custom Sizes', 'socialfeeds'),
                ),
                array(
                    'type' => 'text',
                    'id' => 'image_width',
                    'title' => __('Image Width', 'socialfeeds'),
                    'desc' => __('Select Image Width', 'socialfeeds'),
                    'default' => '600px',
                ),
                array(
                    'type' => 'text',
                    'id' => 'image_height',
                    'title' => __('Image Height', 'socialfeeds'),
                    'desc' => __('Select Image Height', 'socialfeeds'),
                    'default' => '338px',
                ),
                array('type' => 'sectionend', 'id' => 'general_options'));

            $this->options = apply_filters('socialfeeds_get_options_' . $this->id, $options);
        }

    }

    endif;
return new WPSF_Facebook();
