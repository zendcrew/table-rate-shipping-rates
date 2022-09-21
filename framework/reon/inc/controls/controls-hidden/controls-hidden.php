<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_Hidden')) {

    class Reon_Controls_Hidden {

        public static function init() {
            add_action('reon/render-control-hidden', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-hidden', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'type' => 'hidden',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );


            if (isset($field['value'])) {
                $attributes['value'] = $field['value'];
            }

            $wrapper_class = array('rn-field-wrapper','rn-ui-hidden');
           
            $wrapper_attr = array(
                'class' => ReonUtil::array_to_classes($wrapper_class)
            );

            include 'view.php';
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            return sanitize_text_field($raw_option);
        }

    }

    Reon_Controls_Hidden::init();
}