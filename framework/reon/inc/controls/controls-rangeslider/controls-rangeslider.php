<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_RangeSlider')) {

    class Reon_Controls_RangeSlider {

        public static function init() {
            add_action('reon/render-control-range-slider', array(new self(), 'render'), 10, 1);
            add_filter('reon/control-classes', array(new self(), 'get_css_classes'), 11, 2);
            add_filter('reon/sanitize-range-slider', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            if (!isset($field['value_ids'])) {
                $field['value_ids'] = array(0, 1);
            }

            $attributes = array(
                'type' => 'text',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );
            if (!isset($field['css_class'])) {
                $field['css_class'] = array();
            }
            $inp_placeholder = array('', '');
            if (isset($field['placeholder'])) {
                $inp_placeholder = array();
                if (is_array($field['placeholder'])) {
                    foreach ($field['placeholder'] as $key => $value) {
                        $inp_placeholder[] = $value;
                    }
                } else {
                    $inp_placeholder[] = $field['placeholder'];
                }
            }
            if (count($inp_placeholder) < 2) {
                $inp_placeholder[] = $field['placeholder'];
            }

            $inp_values = array('', '');

            if (isset($field['value']) && is_array($field['value'])) {
                $inp_values = array();
                foreach ($field['value'] as $key => $value) {
                    $inp_values[] = $value;
                }
            }

            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-slider', 'rn-range-slider'), $field)));

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
            if ($field['type'] == 'range-slider') {
                if (isset($field['item_index']) && $field['item_index'] == $field['value_ids'][1]) {
                    $classes[] = 'rn-noui-right';
                    if (!isset($field['labels'])) {
                        $classes[] = 'no-right-label';
                    }
                } else {
                    $classes[] = 'rn-noui-left';
                }
            }
            return $classes;
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            if (is_array($raw_option)) {
                $r_option = array();
                foreach ($raw_option as $option) {
                    $r_option[] = sanitize_text_field($option);
                }
                return $r_option;
            } else {
                return sanitize_text_field($raw_option);
            }
        }

    }

    Reon_Controls_RangeSlider::init();
}