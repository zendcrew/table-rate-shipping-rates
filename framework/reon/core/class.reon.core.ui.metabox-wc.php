<?php

if (!defined('ABSPATH')) {
    exit;
}


if (!class_exists('ReonWCMetaBox')) {

    class ReonWCMetaBox {

        private static $post_metaboxes;
        private static $metaboxes;
        private static $product_type_options = array();

        public static function init() {
            $metaboxes = array();
            $product_type_options = array();
            add_filter('product_type_options', array(new self(), 'product_type_options'));
            add_action('woocommerce_process_product_meta', array(new self(), 'save_product_type_options'));
        }

        public static function product_type_options($product_type_options) {
            foreach (self::get_product_type_options() as $product_t_opt) {
                $product_type_option = apply_filters('reon/get-product-type-option', $product_t_opt);

                $product_type_options[$product_type_option['id']] = array(
                    'id' => $product_type_option['meta_id'],
                    'wrapper_class' => (is_array($product_type_option['wrapper_class'])) ? implode(' ', $product_type_option['wrapper_class']) : $product_type_option['wrapper_class'],
                    'label' => $product_type_option['label'],
                    'description' => $product_type_option['desc'],
                    'default' => $product_type_option['default']
                );
            }
            return $product_type_options;
        }

        public static function save_product_type_options($product_id) {
            foreach (self::get_product_type_options() as $product_t_opt) {
                $product_type_option = apply_filters('reon/get-product-type-option', $product_t_opt);

                $meta_value = 'no';
                if (isset($_POST[$product_type_option['meta_id']])) {
                    $meta_value = 'yes';
                }
                $meta_value = apply_filters('reon/save-product-type-option-meta', $meta_value, $product_id, $product_type_option['meta_id']);
                update_post_meta($product_id, $product_type_option['meta_id'], $meta_value);
            }
        }

        public static function product_tab($metabox_id, $tabs) {
            $metabox = self::get_metabox($metabox_id);

            $tabs[$metabox_id] = array(
                'label' => $metabox['title'],
                'target' => 'rn_' . $metabox['id'],
                'class' => $metabox['class'],
                'priority' => $metabox['priority']
            );

            return $tabs;
        }

        public static function product_tab_contents($metabox_id, $product_id) {
            $metabox = self::get_metabox($metabox_id);
            $metabox['fields'] = ReonCore::process_default_post_metabox_wc_fields(apply_filters('reon/get-metabox-wc-' . $metabox['id'] . '-fields', $metabox['fields'], $metabox['id'], $product_id));
            $metabox['nonce_path'] = basename(__FILE__);

            do_action('reon/render-metabox-wc', $metabox, $product_id);
        }

        public static function save_product_metabox($metabox_id, $product_id) {

            $is_autosave = wp_is_post_autosave($product_id);
            $is_revision = wp_is_post_revision($product_id);

            $is_valid_nonce = ( isset($_POST['reon_product_meta' . $metabox_id]) && wp_verify_nonce(wp_unslash($_POST['reon_product_meta' . $metabox_id]), basename(__FILE__)) ) ? true : false;// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

            if ($is_autosave || $is_revision || !$is_valid_nonce) {
                return;
            }


            $metabox = self::get_metabox($metabox_id);
            $metabox['fields'] = ReonCore::process_default_post_metabox_wc_fields(apply_filters('reon/get-metabox-wc-' . $metabox['id'] . '-fields', $metabox['fields'], $metabox['id'], $product_id));

            $product_metas = array();



            $valuas_id = 'reon_metabox_' . $metabox_id . '_value';

            if (isset($_POST[$valuas_id])) {
                $valuas_array = json_decode(wp_unslash($_POST[$valuas_id]), true);// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                if (is_array($valuas_array) && isset($valuas_array[$metabox_id])) {
                    $product_metas = apply_filters('reon/save_all_' . $metabox_id . '_product_metas', self::sanitize_meta_option($metabox_id, $product_id, $valuas_array[$metabox_id]), $product_id);
                }
            }



            if ($metabox['save_metabox'] == true) {

                $field_ids = ReonUtil::get_controls_field_ids($metabox['fields']);

                foreach ($field_ids as $field_id) {
                    if (isset($product_metas[$field_id])) {
                        update_post_meta($product_id, $field_id, $product_metas[$field_id]);
                    } else {
                        delete_post_meta($product_id, $field_id);
                    }
                }
            }
        }

        public static function get_metabox($id = '') {
            if ($id == '') {
                return array();
            }
            foreach (self::get_metaboxes() as $metabox) {
                if ($metabox['id'] == $id) {
                    $mt = apply_filters('reon/get-metabox-wc', $metabox);
                    return $mt;
                }
            }
            return array();
        }

        public static function get_metaboxes() {
            return self::$post_metaboxes;
        }

        public static function get_product_type_options() {
            return self::$product_type_options;
        }

        public static function set_metabox($args = array()) {

            $metabox = self::default_metabox();
            foreach ($args as $key => $value) {
                $metabox[$key] = $value;
            }
            self::$post_metaboxes[] = apply_filters('reon/set-metabox-wc', $metabox);
            self::$metaboxes[] = new ReonWCMetaBoxRenderer($args['id']);
        }

        public static function set_product_type_option($args = array()) {
            $product_type_options = self::default_product_type_option();
            foreach ($args as $key => $value) {
                $product_type_options[$key] = $value;
            }
            self::$product_type_options[] = apply_filters('reon/set-product-type-option', $product_type_options);
        }

        private static function default_metabox() {
            $args = array(
                'id' => 'reon',
                'title' => '',
                'priority' => 99,
                'class' => array(),
                'sanitize_mode' => 'default', // default, recursive, filter
                'save_metabox' => true,
                'clean_meta' => true,
                'fields' => array()
            );
            return $args;
        }

        private static function default_product_type_option() {

            $args = array(
                'id' => 'reon-product-type-option',
                'label' => '',
                'wrapper_class' => array(),
                'desc' => '',
                'default' => 'no'
            );
            return $args;
        }

        private static function sanitize_meta_option($metabox_id, $post_id, $options = array()) {

            $sanitize_mode = self::get_sanitize_mode( $metabox_id );
            
            if ( 'recursive' == $sanitize_mode ) {
                
                return self::get_recursive_sanitised_options( 'reon/sanitize-' . $metabox_id, $options );
            }
            
            $sanitise_filter = 'reon/sanitize-' . $metabox_id . '-options';

            if ( has_filter( $sanitise_filter ) && 'filter' == $sanitize_mode ) {
                
                return apply_filters( $sanitise_filter, $options );
            }

            $field_types = self::get_field_types($metabox_id, $post_id);
            
            $opt = array();
            foreach ($field_types as $field_type) {
                if (isset($options[$field_type['id']])) {
                    $sanitize_type = $field_type['type'];
                    if (isset($field_type['sanitize_type'])) {
                        $sanitize_type = $field_type['sanitize_type'];
                    }
                    
                    $opt[$field_type['id']] = apply_filters('reon/sanitize-' . $sanitize_type, $options[$field_type['id']], $options[$field_type['id']], $field_type['id'], $field_type['children']);
                }
            }

            return $opt;
        }
        
        private static function get_recursive_sanitised_options( $in_key, $options ) {

            $opt = array();

            foreach ( $options as $option_key => $option ) {

                $filter_key = $in_key . '-' . $option_key;

                if ( is_numeric( $option_key ) ) {

                    $filter_key = $in_key;
                }

                if ( has_filter( $filter_key ) ) {

                    $opt[ $option_key ] = apply_filters( $filter_key, $option );

                    continue;
                }

                if ( is_array( $option ) ) {

                    $opt[ $option_key ] = self::get_recursive_sanitised_options( $filter_key, $option );

                    continue;
                }

                if ( $option == '<=' || $option == '<' || $option == '>=' || $option == '>=' || $option == '==' || $option == '!=' ) {

                    $opt[ $option_key ] = $option;

                    continue;
                }

                $opt[ $option_key ] = sanitize_text_field( $option );
            }

            return $opt;
        }

        private static function get_field_types($metabox_id, $post_id) {

            $field_args = array(
                'screen' => 'metabox-wc',
                'metabox_id' => $metabox_id,
                'post_id' => $post_id
            );

            $field_types = array();

            $metabox = self::get_metabox($metabox_id);

            $metabox['fields'] = apply_filters('reon/get-metabox-wc-' . $metabox['id'] . '-fields', ReonCore::process_default_post_metabox_fields($metabox['fields']), $metabox['id'], $post_id);



            foreach ($metabox['fields'] as $field) {

                if (isset($field['merge_fields']) && $field['merge_fields'] == false && isset($field['fields'])) {
                    foreach (ReonUtil::get_controls_field_types($field['fields'], $field_args) as $field_type) {
                        $field_types[] = array(
                            'id' => $field_type['id'],
                            'type' => $field_type['type'],
                            'sanitize_type' => $field_type['sanitize_type'],
                            'screen' => $field_type['screen'],
                            'metabox_id' => $field_type['metabox_id'],
                            'post_id' => $field_type['post_id'],
                            'children' => $field_type['children']
                        );
                    }
                } else {
                    $field_types[] = array(
                        'id' => $field['id'],
                        'type' => $field['type'],
                        'sanitize_type' => $field['sanitize_type'],
                        'screen' => 'metabox',
                        'metabox_id' => $metabox_id,
                        'post_id' => $post_id,
                        'children' => apply_filters('reon/get-' . $field['type'] . '-children-types', array(), $field, $field_args)
                    );
                }
            }
            return $field_types;
        }
        
        private static function get_sanitize_mode( $metabox_id ) {

            $metabox = self::get_metabox( $metabox_id );

            if ( isset( $metabox[ 'sanitize_mode' ] ) ) {

                return $metabox[ 'sanitize_mode' ];
            }

            return 'default';
        }

    }

}


if (!class_exists('ReonWCMetaBoxRenderer')) {

    class ReonWCMetaBoxRenderer {

        public $metaboxe_id;

        public function __construct($id = '') {
            $this->metaboxe_id = $id;
            add_filter('woocommerce_product_data_tabs', array($this, 'metabox_tab'));

            add_action('woocommerce_product_data_panels', array($this, 'metabox_tab_content'), 99);
            add_action('woocommerce_process_product_meta', array($this, 'save_product_metas'));
        }

        public function metabox_tab($tabs) {
            return ReonWCMetaBox::product_tab($this->metaboxe_id, $tabs);
        }

        public function metabox_tab_content() {
            global $post;
            $product_id = $post->ID;
            ReonWCMetaBox::product_tab_contents($this->metaboxe_id, $post->ID);
        }

        public function save_product_metas($product_id) {
            ReonWCMetaBox::save_product_metabox($this->metaboxe_id, $product_id);
        }

    }

}
