<?php
/**
 * WPSF_Admin Class.
 *
 * @author   Amal Ranganath
 * @category Admin
 * @package  WPSocialFeeds/Admin
 * @version  1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WPSF_Admin')) {

    /**
     * WP Social Feeds Admin class
     */
    class WPSF_Admin {

        /**
         * The children class ID
         * @var string 
         */
        protected $id = '';

        /**
         * Main plugin options
         * @var array $options
         */
        protected $options = array();

        /**
         * Main plugin options
         * @var array tabs
         */
        private $tabs = array('general');

        /**
         * Constructor
         */
        public function __construct($feeds = array()) {
            $this->tabs = array_merge($this->tabs, $feeds);
            add_action('admin_menu', array($this, 'admin_menu'), 9);
            add_action('wp_ajax_save_feed', array($this, 'save_feed'));
            //add_action('socialfeeds_options_' . $this->id, array($this, 'render'));
            //load child classes
        }

        /**
         * Add menu items
         */
        public function admin_menu() {

            add_menu_page(__('Social Feeds', 'socialfeeds'), __('WP Social Feeds', 'socialfeeds'), 'admin_dashboard', 'social-feeds', null, '', 30);

            add_submenu_page('social-feeds', __('Settings', 'socialfeeds'), __('Settings', 'socialfeeds'), 'administrator', 'wpsf-settings', array($this, 'socialfeeds_settings'));
            //add_submenu_page('edit.php?post_type=product', __('Attributes', 'socialfeeds'), __('Attributes', 'socialfeeds'), 'manage_product_terms', 'product_attributes', array($this, 'attributes_page'));
        }

        /*
         * 
         */
        public function socialfeeds_settings() {
            wp_enqueue_style('social-feeds', plugins_url('../assests/css/admin.css', __FILE__));
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('social-feeds', plugins_url('../assests/js/scripts.js', __FILE__), array('wp-color-picker'), false, true);

            foreach ($this->tabs as $feed) {
                include_once "options/$feed-options.php";
            }
            $this->init();
        }

        /*
         * 
         */
        public function save_feed() {

            $this->save($_POST['data']);
            $fd = new WPSF_Feed($this->id);
            echo json_encode(array('status' => true, 'meassage' => 'Blasd'));
            die(); // this is required to terminate immediately and return a proper response
        }

        /*
         * 
         */
        public function shortcode() {
            echo 'Place this shortcode to display <b>' . $this->id . '</b> feed.<br/> [socialfeeds feed=' . $this->id . ']';
        }

        public function init() {

            $current = empty($_GET['tab']) ? 'general' : sanitize_title($_GET['tab']);
            include_once 'views/options-page.php';
        }

        /**
         * 
         * @return void
         */
        public function get_options() {
            return apply_filters('socialfeeds_get_options_' . $this->id, array());
        }

        /**
         * Render output
         */
        public function render() {
            $this->get_options();
            if ($_POST)
                $this->save();
            $this->generate_fields();
        }

        /**
         * Save feed options
         */
        public function save($p = null) {

            $data = $p == null ? $_POST : parse_str($p);
            var_dump($data);
            $feed_options = array();
            foreach ($this->options as $option) {
                $feed_options[$option['id']] = $data[$option['id']] != '' ? $data[$option['id']] : $option['default'];
            }

            //update if option exists or else insert
            if ($options = WP_SocialFeeds::getOption()) {
                $options[$this->id] = $feed_options;
                update_option('socialfeeds', $options);
                WP_SocialFeeds::flash('updated', ucfirst($this->id) . ' options successfully updated.');
                //call if a method exists
                //new WPSF_Feed($this->id);
                //if (method_exists($this, 'get_feed')) $this->get_feed();
            } else {
                add_option('socialfeeds', array($this->id => $feed_options));
            }
        }

        /*
         * 
         */

        public function extract_attr($option) {
            if (!empty($option['htmloptions']) && is_array($option['htmloptions'])) {
                foreach ($option['htmloptions'] as $attribute => $attribute_value) {
                    $extra_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
                }
                return implode(' ', $extra_attributes);
            }
        }

        /**
         * Show fields by given feed options
         */
        public function generate_fields() {
            $options = WP_SocialFeeds::getOption($this->id);
            foreach ($this->options as $option):
                $option_value = isset($options[$option['id']]) ? $options[$option['id']] : $option['default'];
                $description = wp_kses_post($option['desc']);

                // Custom attribute handling
                $extra_attributes = array();
                if (!empty($option['htmloptions']) && is_array($option['htmloptions'])) {
                    foreach ($option['htmloptions'] as $attribute => $attribute_value) {
                        $extra_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
                    }
                }

                // Switch based on type
                switch ($option['type']) {

                    // Section Start & Titles
                    case 'section':
                        if (!empty($option['title'])) {
                            echo '<h3>' . esc_html($option['title']) . '</h3>';
                        }
                        if (!empty($option['desc'])) {
                            echo wpautop(wptexturize(wp_kses_post($option['desc'])));
                        }
                        echo '<table class="form-table">' . "\n\n";
                        break;

                    // Section Ends
                    case 'sectionend':
                        echo '</table>';
                        break;

                    // Standard text inputs and subtypes like 'number'
                    case 'text':
                    case 'email':
                    case 'number':
                    case 'color' :
                    case 'password' :

                        $type = $option['type'];

                        if ($option['type'] == 'color') {
                            $type = 'text';
                            $option['htmloptions']['class'] .= ' colorpick';
                        }
                        ?><tr valign="top">
                            <th scope="row" class="title">
                                <label for="<?= esc_attr($option['id']); ?>"><?= esc_html($option['title']); ?></label>
                                <?= $tooltip_html; ?>
                            </th>
                            <td class="wpsf-<?= sanitize_title($option['type']) ?>">
                                <input name="<?= esc_attr($option['id']); ?>" id="<?= esc_attr($option['id']); ?>" type="<?= esc_attr($type); ?>" value="<?= esc_attr($option_value); ?>" <?= $this->extract_attr($option); ?>/> 
                                <?= $description; ?>
                            </td>
                        </tr><?php
                        break;

                    // Textarea
                    case 'textarea':
                        ?><tr valign="top">
                            <th scope="row" class="title">
                                <label for="<?= esc_attr($option['id']); ?>"><?= esc_html($option['title']); ?></label>
                                <?= $tooltip_html; ?>
                            </th>
                            <td class="wpsf-<?= sanitize_title($option['type']) ?>">
                                <?= $description; ?>
                                <textarea name="<?= esc_attr($option['id']); ?>" id="<?= esc_attr($option['id']); ?>" <?= $this->extract_attr($option); ?> ><?= esc_textarea($option_value); ?></textarea>
                            </td>
                        </tr><?php
                        break;

                    // Select boxes
                    case 'select' :
                    case 'multiselect' :
                        ?><tr valign="top">
                            <th scope="row" class="title">
                                <label for="<?= esc_attr($option['id']); ?>"><?= esc_html($option['title']); ?></label>
                                <?= $tooltip_html; ?>
                            </th>
                            <td class="wpsf-<?= sanitize_title($option['type']) ?>">
                                <select name="<?= esc_attr($option['id']); ?><?php if ($option['type'] == 'multiselect') echo '[]'; ?>" id="<?= esc_attr($option['id']); ?>" <?= $this->extract_attr($option); ?> <?= ( 'multiselect' == $option['type'] ) ? 'multiple="multiple"' : ''; ?> >
                                    <?php foreach ($option['options'] as $key => $val) { ?>
                                        <option value="<?= esc_attr($key); ?>" <?php
                                        if (is_array($option_value)) {
                                            selected(in_array($key, $option_value), true);
                                        } else {
                                            selected($option_value, $key);
                                        }
                                        ?>><?= $val ?></option>
                                            <?php } ?>
                                </select>
                                <?= $description; ?>
                            </td>
                        </tr><?php
                        break;

                    // Radio inputs
                    case 'radio' :
                        ?><tr valign="top">
                            <th scope="row" class="title">
                                <label for="<?= esc_attr($option['id']); ?>"><?= esc_html($option['title']); ?></label>
                                <?= $tooltip_html; ?>
                            </th>
                            <td class="wpsf-<?= sanitize_title($option['type']) ?>">
                                <fieldset>
                                    <?= $description; ?>
                                    <ul>
                                        <?php foreach ($option['options'] as $key => $val) { ?>
                                            <li>
                                                <label><input name="<?= esc_attr($option['id']); ?>" value="<?= $key; ?>" type="radio" <?= $this->extract_attr($option); ?> <?php checked($key, $option_value); ?> /> <?= $val ?></label>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </fieldset>
                            </td>
                        </tr><?php
                        break;

                    // Checkbox input
                    case 'checkbox' :
                        ?>
                        <tr valign="top" class="">
                            <th scope="row" class="title"><?= esc_html($option['title']) ?></th>
                            <td class="">
                                <fieldset>
                                    <?php if (!empty($option['title'])) { ?>
                                        <legend class="screen-reader-text"><span><?= esc_html($option['title']) ?></span></legend>
                                    <?php } ?>
                                    <label for="<?= $option['id'] ?>">
                                        <input name="<?= esc_attr($option['id']); ?>" id="<?= esc_attr($option['id']); ?>" type="checkbox" value="1" <?php checked($option_value, 'yes'); ?> <?= $this->extract_attr($option); ?> /> 
                                        <?= $description ?>
                                    </label> <?= $tooltip_html; ?>
                                    <?php ?>
                                </fieldset>
                            </td>
                        </tr>
                        <?php
                        break;
                    case 'anchor' :
                        ?>

                        <tr valign="top">
                            <th scope="row" class="title">
                                <a href="<?= $option_value ?>" id="<?= esc_attr($option['id']); ?>" title="<?= esc_html($option['title']) ?>" <?= $this->extract_attr($option); ?>> <?= esc_html($option['title']) ?></a>
                            </th>
                        </tr>
                    <?php
                    // Default: run an action
                    default:
                        echo $description;
                        break;
                }
            endforeach;
        }

    }
    //end WPSF_Admin class

}