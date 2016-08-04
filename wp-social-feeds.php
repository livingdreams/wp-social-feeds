<?php
/*
  Plugin Name: WP Social Feeds
  Plugin URI: http://livingdreams.lk/wp-social-feeds
  Description: Manage Facebook, Instagram, Twitter and Pinterest social feeds all together.
  Version: 1.0.0
  Author: Living Dreams
  Author URI: http://livingdreams.lk
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//deafine plugins directory
defined('ROOT_DIR') || define('ROOT_DIR', plugin_dir_path(__FILE__));

$upload_dir = wp_upload_dir();
//deafine uploads directory
defined('UPLOADS_DIR') || define('UPLOADS_DIR', $upload_dir['basedir'] . '/socialfeeds');
//deafine uploads url
defined('UPLOADS_URL') || define('UPLOADS_URL', $upload_dir['baseurl'] . '/socialfeeds');
//deafine feeds data saving table name
defined('FEEDS_TABLE') || define('FEEDS_TABLE', 'social_feeds');


if (!class_exists('WP_SocialFeeds')):

    /**
     * Main WP Social Feeds Class
     *
     * @class WP_SocialFeeds
     * @version	1.0.0
     */
    class WP_SocialFeeds {

        /**
         * Plugin version, used for cache-busting of style and script file references.
         * @var string
         */
        const VERSION = '1.0.0';

        /**
         * WP_SocialFeeds The single instance of the class
         * @var object
         */
        protected static $_instance = null;

        /**
         * Suported social media list.
         * @var array 
         */
        protected $feeds = array('facebook', 'twitter', 'instagram', 'pinterest');

        /**
         * Main WP_SocialFeeds Instance to ensures only one instance of WP_SocialFeeds is loaded or can be loaded.
         *
         * @static
         * @return WP_SocialFeeds - Main instance
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * WP_SocialFeeds constructor
         */
        public function __construct() {
            $this->init();
            add_filter('cron_schedules', array(__CLASS__, 'add_scheduled_interval'));
            require_once( ROOT_DIR . 'feeds/wp-sf-feed.php');
            add_action('social_feed_schedule', array(__CLASS__, 'get_social_feed'));
            if (is_admin()) {
                require_once( ROOT_DIR . 'admin/wp-sf-admin.php');
                $admin = new WPSF_Admin($this->feeds);
            }
        }

        /**
         * WP_SocialFeeds initiate hooks
         */
        public function init() {
            register_activation_hook(__FILE__, array(__CLASS__, 'install'));
            add_shortcode('socialfeeds', array(__CLASS__, 'social_feeds'));
            register_deactivation_hook(__FILE__, array(__CLASS__, 'deactivate'));
            add_action('vc_before_init', array(__CLASS__, 'ldcf_vc_map'));
        }

        /** add once a minute interval to wp schedules
         * @param type $schedules
         * @return string
         */
        public function add_scheduled_interval($schedules) {
            $schedules['minutely'] = array('interval' => 60, 'display' => 'Once a minute');
            return $schedules;
        }

        public function get_social_feed() {
            $file = "text.txt";
            $current = file_get_contents($file) . " \n " . date('Y-m-d H:i:s');
            file_put_contents($file, $current);

            foreach ($this->feeds as $feed) {
                $current .= "  " . $feed;

                new WPSF_Feed($feed);
            }
            file_put_contents($file, $current);
            fclose($file);
        }

        /**
         * Get options to given feed
         * @param string $feed
         */
        public static function getOption($feed = null) {
            if ($options = get_option('socialfeeds')) {
                return $feed == null ? $options : $options[$feed];
            }
            return false;
        }

        /**
         * Show flash messages
         * @param string $class
         * @param string $message
         */
        public static function flash($class, $message) {
            ?>
            <div class="<?= $class ?> notice notice is-dismissible">
                <p><?php _e($message, 'socialfeeds'); ?></p>
            </div>
            <?php
        }

        /**
         * Display socialfeed as given short code attributes
         * @global object $wpdb
         * @param array $atts
         */
        public function social_feeds($atts) {
            ob_start();
            wp_enqueue_style('social-feeds', plugins_url('assests/css/styles.css', __FILE__));
            //extract shortcode attributes to variables
            extract(shortcode_atts(array('feed' => 'facebook', 'limit' => 5), $atts));
            //extract feed options to variables
            if ($options = self::getOption($feed)) {
                extract($options);
                //wptexturize($options);
                // var_dump($options);
            } else {
                echo "Could not find $feed";
            }
            //getting data from table
            global $wpdb;
            $table_name = $wpdb->prefix . FEEDS_TABLE;
            //$charset_collate = $wpdb->get_charset_collate();
            $feed_data = $wpdb->get_results("SELECT * FROM $table_name WHERE social_feed='$feed' ORDER BY id");
            require_once('simple-image.php');
            //displaying feed view


            if (!is_admin()) {
                include "feeds/{$feed}/view-feed.php";
                // echo "<div> $feed</div>";
                $content = ob_get_clean();
                return $content;
            }
        }

        /**
         * Install Plugin
         */
        public static function install() {
            //Create tabel
            global $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table_name = $wpdb->prefix . FEEDS_TABLE;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    social_feed varchar(32),
    postid varchar(255) NOT NULL,
    profile_url varchar(255),
    page_name varchar(255),
    status varchar(255),
    type varchar(255),
    created_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    message varchar(255),
    shared_photo_link varchar(255),
    shared_link_img varchar(255),
    shared_link varchar(255),
    shared_link_name varchar(255),
    shared_link_desc varchar(255),
    likes int(16),
    UNIQUE KEY id (id)
  ) $charset_collate;";
            dbDelta($sql);

            //Register a scheduled event
            if (!wp_next_scheduled('get_social_feed')) {
                wp_schedule_event(time(), 'minutely', 'social_feed_schedule');
            }
        }

        /**
         * Deactivate Plugin
         */
        public static function deactivate() {
            global $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $tablename = $wpdb->prefix . FEEDS_TABLE;
            $wpdb->query("DROP TABLE IF EXISTS $tablename");
            wp_clear_scheduled_hook('social_feed_schedule');
        }

        //add vc support to shortcode
        public function ldcf_vc_map() {


            vc_map(array(
                "name" => "WP Social Feeds",
                "base" => "socialfeeds",
                "class" => "",
                "icon" => "",
                "category" => "Social",
                "allowed_container_element" => 'vc_row',
                "params" => array(
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "group" => "Form",
                        "heading" => __("Social Feed", "social-feeds"),
                        "param_name" => "feed",
                        "value" => array(
                            "Facebook" => "facebook",
                            "Twitter" => "twitter",
                            "Instagram" => "instagram",
                            "Pinterest" => "pinterest"),
                        "description" => "Select feed here.",
                    ),
                )
            ));
        }

    }

    WP_SocialFeeds::instance();

endif;

