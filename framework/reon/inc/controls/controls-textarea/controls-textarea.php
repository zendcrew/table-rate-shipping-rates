<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_TextArea')) {

    class Reon_Controls_TextArea {

        public static function init() {
            add_action('reon/render-control-textarea', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-textarea', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            );

            if (isset($field['placeholder'])) {
                $attributes['placeholder'] = $field['placeholder'];
            }

            if (isset($field['height'])) {
                $attributes['height'] = $field['height'];
            }

            if (isset($field['cols'])) {
                $attributes['cols'] = $field['cols'];
            }

            if (isset($field['rows'])) {
                $attributes['rows'] = $field['rows'];
            }

            $box_css = array('rn-field-wrapper');
            if ($field['center_head'] == true) {
                $box_css[] = 'rn-center';
            }
            if (isset($field['grey-parent']) && $field['grey-parent'] == true) {
                $box_css[] = 'rn-grey-parent';
            }
            include 'view.php';
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            return sanitize_textarea_field($raw_option);
        }

    }

    Reon_Controls_TextArea::init();
}