<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_Slider')) {

    class Reon_Controls_Slider {

        public static function init() {
            add_action('reon/render-control-slider', array(new self(), 'render'), 10, 1);
            add_filter('reon/control-classes', array(new self(), 'get_css_classes'), 11, 2);
            add_filter('reon/sanitize-slider', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'type' => 'text',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );

            if (isset($field['placeholder'])) {
                $attributes['placeholder'] = $field['placeholder'];
            }

            if (isset($field['value'])) {
                $attributes['value'] = $field['value'];
            }
            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-slider'), $field)));

            $wrp_class = array('rn-field-wrapper');
            if ($field['center_head'] == true) {
                $wrp_class[] = 'rn-center';
            }
            if (isset($field['grey-parent']) && $field['grey-parent'] == true) {
                $wrp_class[] = 'rn-grey-parent';
            }
            include 'view.php';
        }

        public static function get_css_classes($classes, $field) {
            if ($field['type'] == 'slider') {
                $classes[] = 'rn-noui-left';
            }
            return $classes;
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            return sanitize_text_field($raw_option);
        }

    }

    Reon_Controls_Slider::init();
}