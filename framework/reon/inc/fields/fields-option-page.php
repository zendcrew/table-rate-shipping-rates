<?php

add_action('reon/render-option-page-fields', 'reon_fields_option_page', 10, 3);
if (!function_exists('reon_fields_option_page')) {

    function reon_fields_option_page($fields, $option_name, $instance_id) {
        $options = ReonCore::load_options(ReonOptionPage::get_page_option_args($option_name, $instance_id));
        $index = 0;
        foreach ($fields as $fld) {
            $field = $fld;
            $field['screen'] = 'option-page';
            $field['option_name'] = $option_name;

            if ($index == 0) {
                $field['first'] = true;
            }

            if (!isset($fields[$index + 1]) || $fields[$index + 1]['type'] == 'heading') {
                $field['last'] = true;
            }
            $field['in_prefix'] = $option_name . '[';
            $field['in_postfix'] = ']';


            if (isset($field['fields']) && isset($field['merge_fields']) && $field['merge_fields'] === false) {
                $field_ids = ReonUtil::get_controls_field_ids($field['fields']);
                foreach ($field_ids as $field_id) {
                    if (isset($options[$field_id])) {
                        $field['value'][$field_id] = $options[$field_id];
                    }
                }
            } else if (isset($options[$field['id']])) {
                $field['value'] = $options[$field['id']];
            } else if (isset($field['default'])) {
                $field['value'] = $field['default'];
            }


            if ($field['is_hidden'] == true) {
                do_action('reon/render-hidden-field', $field);
            } else if ($field['type'] == 'heading') {
                do_action('reon/render-heading-field', $field);
            } else if ($field['type'] == 'paneltitle') {
                do_action('reon/render-panel_title', $field);
            } else if ($field['full_width'] == true) {
                do_action('reon/render-fullwidth-field', $field);
            } else if ($field['title'] == '' && $field['desc'] == '') {
                do_action('reon/render-fullwidth-field', $field);
            } else {
                do_action('reon/render-normal-field', $field);
            }
            $index++;
        }
    }

}