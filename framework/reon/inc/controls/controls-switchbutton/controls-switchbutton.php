<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_SwitchButton')) {

    class Reon_Controls_SwitchButton {

        public static function init() {
            add_action('reon/render-control-switch-button', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-switch-button', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $btnset_with = '';
            if (isset($g_field['width'])) {
                $btnset_with = ' style="width:' . esc_attr($g_field['width']) . ';"';
            }

            $attributes = array(
                'type' => 'hidden',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );

            if (!isset($field['css_class'])) {
                $field['css_class'] = array();
            }
            $field['css_class'][] = 'rn-opt-value';

            $is_on = false;
            $attributes['value'] = $field['off_value'];

            if (isset($field['value']) && $field['value'] == $field['on_value']) {
                $attributes['value'] = $field['on_value'];
                $is_on = true;
            }

            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-field', 'rn-btnset', 'rn-button-set'), $field)));
            $box_attributes['data-multiple'] = false;
            include 'view.php';
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            return sanitize_text_field($raw_option);
        }

    }

    Reon_Controls_SwitchButton::init();
}