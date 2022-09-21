<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ReonUI')) {

    class ReonUI {

        private static $dyn_contents = array();

        public static function init() {
            add_action('admin_enqueue_scripts', array(new self(), 'enqueue_admin_assets'), 30);
        }

        public static function enqueue_admin_assets() {
            self::register_styles();
            self::register_scripts();
            self::enqueue_styles();
            self::enqueue_scripts();
        }

        public static function set_dynamic_contents($contents, $key) {
            if (!isset(self::$dyn_contents[$key])) {
                self::$dyn_contents[$key] = $contents;
            }
        }

        public static function get_dynamic_contents() {
            $contents = '';
            foreach (self::$dyn_contents as $key => $dyn_content) {
                $contents .= $dyn_content;
            }
            return $contents;
        }

        private static function register_styles() {
            $rn_style_path = REON_ASSETS . '/css/';
            $rn_script_path = REON_ASSETS . '/js/';
            wp_register_style('fontawsome', $rn_style_path . 'fontawesome/css/all.min.css');
            wp_register_style('spectrum', $rn_style_path . 'spectrum.css');
            wp_register_style('nouislider', $rn_style_path . 'nouislider.min.css');
            wp_register_style('select2', $rn_script_path . 'select2/css/select2.min.css');
            wp_register_style('rn-jquery-ui', $rn_style_path . 'jquery-ui.min.css');
            wp_register_style('timepicker_addon', $rn_script_path . 'timepicker-addon/jquery-ui-timepicker-addon.css');


            wp_register_style('rn-admin', $rn_style_path . 'admin.css');
            do_action('reon/register-styles');
        }

        private static function register_scripts() {
            $rn_scripts_path = REON_ASSETS . '/js/';

            if (!isset($GLOBALS['wp_scripts']->registered['jquery-serializeform'])) {
                wp_register_script('jquery-serializeform', $rn_scripts_path . 'jquery-serializeForm.min.js', array('jquery'), '1.2.1', true);
            }

            wp_register_script('rn-ui-option-page', $rn_scripts_path . 'rn-ui-option-page.js', array('jquery'), '1.0', true);
            wp_register_script('rn-ui-meta-box', $rn_scripts_path . 'rn-ui-meta-box.js', array('jquery'), '1.0', true);
            wp_register_script('spectrum', $rn_scripts_path . 'spectrum.js', array('jquery'), '1.0', true);
            wp_register_script('wnumb', $rn_scripts_path . 'wNumb.js', array('jquery'), '1.1', true);
            wp_register_script('nouislider', $rn_scripts_path . 'nouislider.min.js', array('jquery'), '8.5.1', true);
            wp_register_script('noui', $rn_scripts_path . 'rn-noui.js', array('jquery'), '1.1', true);

            if (!isset($GLOBALS['wp_scripts']->registered['select2'])) {
                wp_register_script('select2', $rn_scripts_path . 'select2/js/select2.min.js', array('jquery'), '4.0.3', true);
            }

            if (!isset($GLOBALS['wp_scripts']->registered['jquery-tiptip'])) {
                wp_register_script('jquery-tiptip', $rn_scripts_path . 'jquery.tiptip.min.js', array('jquery'), '1.3', true);
            }

            wp_register_script('buttonset', $rn_scripts_path . 'buttonset.js', array('jquery'), '1.0', true);
            wp_register_script('repeater', $rn_scripts_path . 'repeater.js', array('jquery', 'jquery-ui-sortable'), '1.0', true);
            wp_register_script('foldjs', $rn_scripts_path . 'foldjs.js', array('jquery'), '1.0', true);
            wp_register_script('timepicker-addon', $rn_scripts_path . 'timepicker-addon/jquery-ui-timepicker-addon.js', array('jquery'), '1.0', true);
            wp_register_script('rn-datetime', $rn_scripts_path . 'rn-datetime.js', array('jquery'), '1.0', true);
            wp_register_script('rn-autoid', $rn_scripts_path . 'rn-autoid.js', array('jquery'), '1.0', true);

            do_action('reon/register-scripts');
        }

        private static function enqueue_styles() {
            wp_enqueue_style('fontawsome');
            wp_enqueue_style('spectrum');
            wp_enqueue_style('select2');
            wp_enqueue_style('rn-jquery-ui');
            wp_enqueue_style('timepicker_addon');
            wp_enqueue_style('nouislider');

            wp_enqueue_style('rn-admin');

            $toplevel_styles = '';

            foreach (ReonOptionPage::get_pages() as $option_page) {

                if (isset($option_page['menu']['slug'])) {
                    $toplevel_styles = $toplevel_styles . '#toplevel_page_' . $option_page['menu']['slug'] . ' .wp-first-item{display:none;}';
                }
            }

            wp_add_inline_style('rn-admin', $toplevel_styles);

            do_action('reon/enqueue-styles');
        }

        private static function enqueue_scripts() {
            $deps = array(
                'jquery-ui-spinner',
                'jquery-ui-slider',
                'jquery-serializeform',
                'rn-ui-option-page',
                'rn-ui-meta-box',
                'spectrum',
                'wnumb',
                'nouislider',
                'noui',
                'select2',
                'buttonset',
                'jquery-ui-datepicker',
                'repeater',
                'timepicker-addon',
                'jquery-tiptip',
                'rn-datetime',
                'rn-autoid',
                'foldjs',
            );


            $rn_scripts_path = REON_ASSETS . '/js/';
            wp_enqueue_script('rn-ui', $rn_scripts_path . 'rn-ui.js', $deps, true, true);


            $client_variables = array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'culture' => apply_filters('roen/get-culture', array())
            );

            $output = json_encode(apply_filters('roen/client-variables', $client_variables), JSON_UNESCAPED_SLASHES);
            $rn_uiv = 'var rn_uiv=' . $output . ';';

            wp_add_inline_script('jquery', $rn_uiv, 'before');

            do_action('reon/enqueue-scripts');
        }

    }

}