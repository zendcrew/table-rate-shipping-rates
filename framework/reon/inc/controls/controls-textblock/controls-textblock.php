<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_TextBlock')) {

    class Reon_Controls_TextBlock {

        public static function init() {
            add_action('reon/render-control-textblock', array(new self(), 'render'), 10, 1);
        }

        public static function render($field) {
            $show_box = true;

            if (isset($field['show_box'])) {
                $show_box = $field['show_box'];
            }

            $css_classes = array();


            if (isset($field['css_class'])) {
                if (is_array($field['css_class'])) {
                    $css_classes = $field['css_class'];
                } else if ($field['css_class'] != '') {
                    $css_classes = explode(' ', $field['css_class']);
                }
            }

            
            if ($show_box == true) {
                $css_classes[] = 'rn-field';
                $css_classes[] = 'rn-textblock';
            }


            $box_attributes = array();

            if (count($css_classes) > 0) {
                $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', $css_classes, $field)));
            }


            $wrapper_class = array('rn-field-wrapper');
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
            $text = '';
            if (isset($field['text'])) {
                $text = $field['text'];
            }
            include 'view.php';
        }

    }

    Reon_Controls_TextBlock::init();
}