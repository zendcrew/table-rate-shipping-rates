<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_TextBox')) {

    class Reon_Controls_TextBox {

        public static function init() {
            add_action('reon/render-control-textbox', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-textbox', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'type' => 'text',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );

            if (isset($field['placeholder'])) {
                $attributes['placeholder'] = $field['placeholder'];
            }

            if (isset($field['input_type'])) {
                $attributes['type'] = $field['input_type'];
            }

            if (isset($field['value'])) {
                $attributes['value'] = $field['value'];
            }
            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-field', 'rn-textbox'), $field)));


            $wrapper_class = array('rn-field-wrapper');
            if (isset($field['grey-parent']) && $field['grey-parent'] == true) {
                $wrapper_class[] = 'rn-grey-parent';
            }
            if ($field['center_head'] == true) {
                $wrapper_class[] = 'rn-center';
            }

            if (isset($field['box_width']) && $field['box_width'] != '') {
                $wrapper_class[] = 'rn-fluid-width';
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

    Reon_Controls_TextBox::init();
}