<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_Date')) {

    class Reon_Controls_Date {

        public static function init() {
            add_action('reon/render-control-date', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-date', array(new self(), 'sanitize'), 1, 4);
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
            $field['css_class'][] = 'rn-dateonly';



            if (isset($field['placeholder'])) {
                $attributes['placeholder'] = $field['placeholder'];
            }

            if (isset($field['date_format'])) {
                $attributes['data-date-format'] = $field['date_format'];
            }

            if (isset($field['number_of_months'])) {
                $attributes['data-number-of-months'] = $field['number_of_months'];
            }

            if (isset($field['min_date'])) {
                $attributes['data-min-date'] = $field['min_date'];
            }

            if (isset($field['max_date'])) {
                $attributes['data-max-date'] = $field['max_date'];
            }

            if (isset($field['show_button_panel'])) {
                $attributes['data-show-button-panel'] = $field['show_button_panel'];
            }

            if (isset($field['change_month'])) {
                $attributes['data-change-month'] = $field['change_month'];
            }

            if (isset($field['change_year'])) {
                $attributes['data-change-year'] = $field['change_year'];
            }

            if (isset($field['show_week'])) {
                $attributes['data-show-week'] = $field['show_week'];
            }

            if (isset($field['first_day'])) {
                $attributes['data-first-day'] = $field['first_day'];
            }

            if (isset($field['show_other_months'])) {
                $attributes['data-show-other-months'] = $field['show_other_months'];
            }

            if (isset($field['select_other_months'])) {
                $attributes['data-select-other-months'] = $field['select_other_months'];
            }

            if (isset($field['date_locale'])) {
                $attributes['data-date-locale'] = $field['date_locale'];
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

    Reon_Controls_Date::init();
}