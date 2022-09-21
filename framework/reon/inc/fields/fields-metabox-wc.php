<?php

add_action('reon/render-metabox-wc-fields', 'reon_fields_metabox_wc', 10, 3);
if (!function_exists('reon_fields_metabox_wc')) {

    function reon_fields_metabox_wc($fields, $metabox_id, $product_id) {

        $index = 0;
        foreach ($fields as $fld) {
            $field = $fld;
            $field['screen'] = 'metabox-wc';
            $field['post_id'] = $product_id;
            $field['metabox_id'] = $metabox_id;

            if ($index == 0) {
                $field['first'] = true;
            }

            if (!isset($fields[$index + 1]) || $fields[$index + 1]['type'] == 'heading') {
                $field['last'] = true;
            }
            $field['in_prefix'] = $metabox_id . '[';
            $field['in_postfix'] = ']';


            if (isset($field['fields']) && isset($field['merge_fields']) && $field['merge_fields'] === false) {
                $field_ids = ReonUtil::get_controls_field_ids($field['fields']);

                $meta_arr = array();
                foreach ($field_ids as $field_id) {
                    $mt_ar = get_post_meta($product_id, $field_id, false);
                    if (count($mt_ar) == 1) {
                        $meta_arr[$field_id] = $mt_ar[0];
                    }
                }


                if (count($meta_arr) == 0 && isset($field['default'])) {
                    $field['value'] = $field['default'];
                } else if (count($meta_arr) > 0) {
                    $field['value'] = $meta_arr;
                }
            } else {

                $meta_ar = get_post_meta($product_id, $field['id'], false);
                if (count($meta_ar) == 0 && isset($field['default'])) {
                    $field['value'] = $field['default'];
                } else if (count($meta_ar) == 1) {
                    $field['value'] = $meta_ar[0];
                }
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