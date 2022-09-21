<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_AutoId')) {

    class Reon_Controls_AutoId {

        public static function init() {
            add_action('reon/render-control-autoid', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-autoid', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'type' => 'hidden',
                'class' => ReonUtil::array_to_classes(array('rn-ui-autoid')),
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );

            if (isset($field['autoid']) && $field['autoid'] != '') {
                $attributes['data-autoid_id'] = $field['autoid'];
            }
            
            if (isset($field['prefix'])) {
                $attributes['data-autoid_prefix'] = $field['prefix'];
            }
            
            if (isset($field['suffix'])) {
                $attributes['data-autoid_suffix'] = $field['suffix'];
            }

            if (isset($field['value'])) {
                $attributes['value'] = $field['value'];
            }

            $wrapper_class = array('rn-field-wrapper', 'rn-ui-hidden');

            $wrapper_attr = array(
                'class' => ReonUtil::array_to_classes($wrapper_class)
            );

            include 'view.php';
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            return sanitize_text_field($raw_option);
        }

    }

    Reon_Controls_AutoId::init();
}