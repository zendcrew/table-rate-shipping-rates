<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Reon')) {


    class Reon {

        public static function init() {
            global $reon_version;
            define('REON_VERSION', $reon_version);
            define('REON_ASSETS', plugins_url('assets/', __FILE__));

                require_once dirname(__FILE__) . '/core/class.reon.core.php';
                require_once dirname(__FILE__) . '/inc/controls/controls.php';
                require_once dirname(__FILE__) . '/inc/fields/fields.php';

                require_once dirname(__FILE__) . '/inc/optionpage/option-page.php';
                require_once dirname(__FILE__) . '/inc/metabox/metaboxes.php';

                ReonCore::init();

        }

        public static function set_option_page($args = array()) {
            ReonOptionPage::set_page($args);
        }

        public static function set_option_section($option_name = '', $args = array()) {
            ReonOptionPage::set_section($option_name, $args);
        }

        public static function set_wc_option_page($args = array()) {
            ReonOptionPageWC::set_page($args);
        }

        public static function set_wc_option_section($option_name = '', $args = array()) {
            ReonOptionPageWC::set_section($option_name, $args);
        }

        public static function set_meta_box($args = array()) {
            ReonMetaBox::set_metabox($args);
        }

        public static function set_wc_product_meta_box($args = array()) {
            ReonWCMetaBox::set_metabox($args);
        }

        public static function set_wc_product_type_option($args = array()) {
            ReonWCMetaBox::set_product_type_option($args);
        }

    }

    Reon::init();
}