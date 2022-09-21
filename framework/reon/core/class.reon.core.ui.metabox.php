<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ReonMetaBox')) {

    class ReonMetaBox {

        private static $post_metaboxes = array();

        public static function init() {
            add_action('add_meta_boxes', array(new self(), 'init_metaboxes'));
            add_action('save_post', array(new self(), 'save_post_metabox'), 10, 3);
        }

        public static function init_metaboxes() {

            foreach (self::get_metaboxes() as $metabox) {
                add_meta_box($metabox['id'], $metabox['title'], array(new self(), 'render_metabox'), $metabox['screen'], $metabox['context'], $metabox['priority']);
            }
        }

        public static function render_metabox($post, $args) {

            $post_id = $post->ID;
            $metabox = self::get_metabox($args['id']);
            $metabox['fields'] = ReonCore::process_default_post_metabox_fields(apply_filters('reon/get-metabox-' . $metabox['id'] . '-fields', $metabox['fields'], $metabox['id'], $post_id));
            $metabox['nonce_path'] = basename(__FILE__);

            do_action('reon/render-metabox', $metabox, $post_id);
        }

        public static function save_post_metabox($post_id, $post, $update) {

            foreach (self::get_metaboxes() as $metabox) {

                $is_autosave = wp_is_post_autosave($post_id);
                $is_revision = wp_is_post_revision($post_id);
                $is_valid_nonce = ( isset($_POST['reon_post_meta' . $metabox['id']]) && wp_verify_nonce(wp_unslash($_POST['reon_post_meta' . $metabox['id']]), basename(__FILE__)) ) ? true : false;// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

                if ($is_autosave || $is_revision || !$is_valid_nonce) {
                    continue;
                }
                $metabox['fields'] = ReonCore::process_default_post_metabox_fields(apply_filters('reon/get-metabox-' . $metabox['id'] . '-fields', $metabox['fields'], $metabox['id'], $post_id, $update));


                $post_metas = array();

                $valuas_id = 'reon_metabox_' . $metabox['id'] . '_value';

                if (isset($_POST[$valuas_id])) {
                    $valuas_array = json_decode(wp_unslash($_POST[$valuas_id]), true);// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    if (is_array($valuas_array) && isset($valuas_array[$metabox['id']])) {
                        $post_metas = apply_filters('reon/save_all_' . $metabox['id'] . '_post_metas', self::sanitize_meta_option($metabox['id'], $post_id, $valuas_array[$metabox['id']]), $post_id);
                    }
                }

                if ($metabox['save_metabox'] == true) {
                    $field_ids = ReonUtil::get_controls_field_ids($metabox['fields']);
                    foreach ($field_ids as $field_id) {
                        if (isset($post_metas[$field_id])) {
                            update_post_meta($post_id, $field_id, $post_metas[$field_id]);
                        } else {
                            delete_post_meta($post_id, $field_id);
                        }
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
                    $mt = apply_filters('reon/get-metabox', $metabox);
                    return $mt;
                }
            }
            return array();
        }

        public static function get_metaboxes() {
            return self::$post_metaboxes;
        }

        public static function set_metabox($args = array()) {
            $metabox = self::default_metabox();
            foreach ($args as $key => $value) {
                $metabox[$key] = $value;
            }

            self::$post_metaboxes[] = apply_filters('reon/set-metabox', $metabox);
        }

        private static function default_metabox() {

            $args = array(
                'id' => 'reon',
                'title' => '',
                'screen' => array('page', 'post'),
                'context' => 'advanced',
                'priority' => 'default',
                'sanitize_mode' => 'default', // default, recursive, filter
                'save_metabox' => true,
                'fields' => array()
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
                'screen' => 'metabox',
                'metabox_id' => $metabox_id,
                'post_id' => $post_id
            );

            $field_types = array();

            $metabox = self::get_metabox($metabox_id);
            $metabox['fields'] = apply_filters('reon/get-metabox-' . $metabox['id'] . '-fields', ReonCore::process_default_post_metabox_fields($metabox['fields']), $metabox['id'], $post_id);


            foreach ($metabox['fields'] as $field) {

                if (isset($field['merge_fields']) && $field['merge_fields'] == false && isset($field['fields'])) {
                    foreach (ReonUtil::get_controls_field_types($field['fields'], $field_args) as $field_type) {
                        $field_types[] = array(
                            'id' => $field_type['id'],
                            'type' => $field_type['type'],
                            'screen' => $field_type['screen'],
                            'sanitize_type' => $field_type['sanitize_type'],
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