<?php

/**
 * WPSF_Feed Class.
 *
 * @author   Amal Ranganath
 * @category Feed
 * @package  WPSocialFeeds/Feed
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WPSF_Feed')) :

    /**
     * WP Social Feeds Admin class
     */
    class WPSF_Feed {

        protected $feed = '';

        public function __construct($feed) {
            $this->feed = $feed;
            include_once "$feed/get-feed.php";
        }

        /**
         * Curl request
         * @param string $url
         * @return JSON
         */
        
        
        public static function request($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }

        /**
         * Reset feed data
         * @global object $wpdb
         */
        public function reset() {
            if (is_dir(UPLOADS_DIR) === false) {
                mkdir(UPLOADS_DIR);
            }
            //delete existing data
            global $wpdb;
            $table_name = $wpdb->prefix . FEEDS_TABLE;
            //$result = $wpdb->get_results('SELECT * FROM wp_social_feed WHERE social_feed ="' . $this->id . '"');
            $wpdb->delete($table_name, array('social_feed' => $this->feed));
            $wpdb->show_errors();
        }

        /**
         * Save feed data
         * @global object $wpdb
         */
        public function save($data) {
            global $wpdb;
            $table_name = $wpdb->prefix . FEEDS_TABLE;

            $wpdb->insert($table_name, $data);
            $wpdb->show_errors();
        }

    }

    

    //end class
    endif;