<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_Time')) {

    class Reon_Controls_Time {

        public static function init() {
            add_action('reon/render-control-time', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-time', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {

            $attributes = array(
                'type' => 'text',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );
            if (isset($field['width'])) {
                if (!isset($field['style'])) {
                    $field['style'] = array();
                }
                $field['style']['min-width'] = $field['width'];
            }

            if (!isset($field['css_class'])) {
                $field['css_class'] = array();
            }
            $field['css_class'][] = 'rn-datetimepicker';
            $field['css_class'][] = 'rn-timeonly';

            if (isset($field['placeholder'])) {
                $attributes['placeholder'] = $field['placeholder'];
            }

            if (isset($field['time_format'])) {
                $attributes['data-time-format'] = $field['time_format'];
            }

            if (isset($field['one_line'])) {
                $attributes['data-one-line'] = $field['one_line'];
            }

            if (isset($field['step_hour'])) {
                $attributes['data-step-hour'] = $field['step_hour'];
            }

            if (isset($field['step_minute'])) {
                $attributes['data-step-minute'] = $field['step_minute'];
            }

            if (isset($field['step_second'])) {
                $attributes['data-step-second'] = $field['step_second'];
            }

            if (isset($field['time_locale'])) {
                $attributes['data-time-locale'] = $field['time_locale'];
            }

            if (isset($field['value'])) {
                $attributes['value'] = $field['value'];
            }
            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-field', 'rn-textbox'), $field)));

            $wrapper_class = array('rn-field-wrapper');
            if ($field['center_head'] == true) {
                $wrapper_class[] = 'rn-center';
            }

            if (isset($field['box_width']) && $field['box_width'] != '') {
                $wrapper_class[] = 'rn-fluid-width';
            }

            if (isset($field['grey-parent']) && $field['grey-parent'] == true) {
                $wrapper_class[] = 'rn-grey-parent';
            }

            $wrapper_attr = array(
                'class' => ReonUtil::array_to_classes($wrapper_class)
            );
            if (isset($field['dyn_field_id'])) {
                $wrapper_attr['data-dyn_field_id'] = $field['dyn_field_id'];
            }

            if (isset($field['box_width']) && $field['box_width'] != '') {
                $wrapper_attr['style'] = 'width:' . $field['box_width'] . ';';
            }
            include 'view.php';
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            return sanitize_text_field($raw_option);
        }

    }

    Reon_Controls_Time::init();
}