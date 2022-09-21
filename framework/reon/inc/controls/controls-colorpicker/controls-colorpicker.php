<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_ColorPicker')) {

    class Reon_Controls_ColorPicker {

        public static function init() {
            add_action('reon/render-control-colorpicker', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-colorpicker', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'type' => 'text',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );

            if (isset($field['buton_text'])) {
                $attributes['title'] = $field['buton_text'];
            }

            if (isset($field['value'])) {
                $attributes['value'] = $field['value'];
            }

            $box_css = array('rn-colorpicker');
            if (isset($field['grey-parent']) && $field['grey-parent'] == true) {
                $box_css[] = 'rn-grey-parent';
            }

            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', $box_css, $field)));
            include 'view.php';
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            return sanitize_text_field($raw_option);
        }

    }

    Reon_Controls_ColorPicker::init();
}