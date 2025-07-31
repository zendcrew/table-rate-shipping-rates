<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if (!class_exists('Reon_Controls_ButtonSet')) {

    class Reon_Controls_ButtonSet {

        public static function init() {
            add_action('reon/render-control-button-set', array(new self(), 'render'), 10, 1);
            add_filter('reon/control-classes', array(new self(), 'get_css_classes'), 20, 2);
            add_filter('reon/sanitize-button-set', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
           
            $options = array();

            if (isset($field['options'])) {
                $options = $field['options'];
            }
            if (isset($field['data']) && isset($field['data']['source'])) {
                $data_args = array(
                    'source' => $field['data']['source']
                );

                if (isset($field['data']['value_col'])) {
                    $data_args['value_col'] = $field['data']['value_col'];
                }
                if (isset($field['data']['show_value'])) {
                    $data_args['show_value'] = $field['data']['show_value'];
                }

                $source_option = ReonApi::get_data_list($data_args);
                foreach ($source_option as $key => $value) {
                    $options[$key] = $value;
                }
            }



            if (isset($field['multiple']) && $field['multiple'] == true) {
                $selected = array();
                if (isset($field['value'])) {
                    if (is_array($field['value'])) {
                        $selected = $field['value'];
                    }
                }


                $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-field', 'rn-btnset', 'rn-button-set'), $field)));
                $box_attributes['data-multiple'] = true;

                include 'multiple-view.php';
            } else {

                $attributes = array(
                    'type' => 'hidden',
                    'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
                );

                if (!isset($field['css_class'])) {
                    $field['css_class'] = array();
                }
                $field['css_class'][] = 'rn-opt-value';

                $inp_value = '';
                if (isset($field['off_value'])) {
                    $inp_value = $field['off_value'];
                }

                if (isset($field['value']) && !is_array($field['value'])) {
                    $inp_value = $field['value'];
                }
                $attributes['value'] = $inp_value;

                $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-field', 'rn-btnset', 'rn-button-set'), $field)));
                $box_attributes['data-multiple'] = false;

                include 'single-view.php';
            }
        }

        public static function get_css_classes($classes, $field) {
            $cls = $classes;
            if ($field['type'] == 'button-set' && isset($field['multiple']) && $field['multiple'] == true && isset($field['item_index'])) {
                $cls[] = 'rn-opt-value';
                $cls[] = 'rn-opt-multi';
            }

            return $cls;
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

    Reon_Controls_ButtonSet::init();
}