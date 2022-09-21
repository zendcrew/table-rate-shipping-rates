<?php
if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_Spinner')) {

    class Reon_Controls_Spinner {

        public static function init() {
            add_action('reon/render-control-spinner', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-spinner', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'type' => 'text',
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );

            if (isset($field['placeholder'])) {
                $attributes['placeholder'] = $field['placeholder'];
            }

            if (isset($field['max'])) {
                $attributes['data-max'] = $field['max'];
            }

            if (isset($field['min'])) {
                $attributes['data-min'] = $field['min'];
            }

            if (isset($field['step'])) {
                $attributes['data-step'] = $field['step'];
            }

            if (isset($field['allow_offset_value']) && $field['allow_offset_value'] != true) {
                $attributes['data-offset'] = false;
            } else {
                $attributes['data-offset'] = true;
            }



            if (isset($field['value'])) {
                $attributes['value'] = $field['value'];
            }
            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', array('rn-spinner'), $field)));
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

    Reon_Controls_Spinner::init();
}