<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_Select2')) {

    class Reon_Controls_Select2 {

        public static function init() {
            add_action('reon/render-control-select2', array(new self(), 'render'), 10, 1);
            add_filter('reon/sanitize-select2', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            $attributes = array(
                'name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
                'data-minimum-input-length' => 0,
                'data-minimum-results-forsearch' => 10,
            );

            if (isset($field['tags'])) {
                $attributes['data-tags'] = $field['tags'];
            }

            if (isset($field['minimum_input_length'])) {
                $attributes['data-minimum-input-length'] = $field['minimum_input_length'];
            }

            if (isset($field['minimum_results_forsearch'])) {
                $attributes['data-minimum-results-forsearch'] = $field['minimum_results_forsearch'];
            }

            if (isset($field['multiple']) && $field['multiple'] == true) {
                $attributes['multiple'] = 'multiple';
            }

            if (isset($field['placeholder'])) {
                $attributes['data-placeholder'] = $field['placeholder'];
            }

            if (isset($field['allow_clear'])) {
                $attributes['data-allow-clear'] = $field['allow_clear'];
            }
            if (isset($field['data']) && $field['data']['ajax'] == true) {
                $attributes['data-ajaxsource'] = $field['data']['source'];

                if (isset($field['data']['pagesize'])) {
                    $attributes['data-ajaxsource_pagesize'] = $field['data']['pagesize'];
                }

                if (isset($field['data']['value_col'])) {
                    $attributes['data-ajaxsource_value_col'] = $field['data']['value_col'];
                }

                if (isset($field['data']['value_col_pre'])) {
                    $attributes['data-ajaxsource_value_col_pre'] = $field['data']['value_col_pre'];
                }


                if (isset($field['data']['show_value'])) {
                    $attributes['data-ajaxsource_show_value'] = $field['data']['show_value'];
                }

                if (isset($field['disabled_list_filter'])) {
                    $attributes['data-ajaxsource_disabled_filter'] = $field['disabled_list_filter'];
                }
            }
            if (isset($field['dyn_switcher_id'])) {
                $attributes['data-dyn_switcher_id'] = $field['dyn_switcher_id'];
            }

            if (isset($field['dyn_switcher_width'])) {
                $attributes['data-dyn_switcher_width'] = $field['dyn_switcher_width'];
            }

            if (isset($field['dyn_switcher_exclude'])) {
                $attributes['data-dyn_switcher_exclude'] = $field['dyn_switcher_exclude'];
            }

            $box_classes = array('rn-select2');
            if (isset($field['dyn_switcher_id'])) {
                $box_classes[] = 'rn-dyn-switcher';
            }
            $box_attributes = array('class' => ReonUtil::array_to_classes(apply_filters('reon/control-box-classes', $box_classes, $field)));

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
            if (is_array($raw_option)) {
                $r_option = array();
                foreach ($raw_option as $option) {
                    $r_option[] = sanitize_text_field($option);
                }
                return $r_option;
            } else {
                if ($raw_option == '<=' || $raw_option == '<' || $raw_option == '>=' || $raw_option == '>=' || $raw_option == '==' || $raw_option == '!=') {
                    return $raw_option;
                }

                return sanitize_text_field($raw_option);
            }
        }

    }

    Reon_Controls_Select2::init();
}