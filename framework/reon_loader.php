<?php

if (!defined('ABSPATH')) {
    exit;
}


if (!class_exists('WTARS_Shipped_Framework_Loader')) {

    class WTARS_Shipped_Framework_Loader {

        public static function init() {
            add_filter('load_reon', array(new self(), 'get_framework'));
            add_action('plugins_loaded', array(new self(), 'load_reon_framework'));
        }

        //---------------------------
        //These codes can be editted
        //---------------------------
        public static function get_framework($package) {
            $reon_version = 1.0; //this is editable
            $reon_file = dirname(__FILE__) . '/reon/framework.php';

            if (!isset($package['reon_version']) || $package['reon_version'] < $reon_version) {
                return array(
                    'reon_version' => $reon_version,
                    'reon_file' => $reon_file
                );
            }

            return $package;
        }

        //--------------------------------
        //These codes SHOULD NOT be edited
        //--------------------------------

        function load_reon_framework() {
            if (defined('REON_VERSION')) {
                return;
            }
            $reon_package = apply_filters('load_reon', array());
            if (isset($reon_package['reon_version']) && isset($reon_package['reon_file'])) {
                $reon_version = $reon_package['reon_version'];
                require_once $reon_package['reon_file'];
            }
        }

    }

    WTARS_Shipped_Framework_Loader::init();
}
