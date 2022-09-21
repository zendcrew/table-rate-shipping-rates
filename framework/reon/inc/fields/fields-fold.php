<?php
add_filter('reon/control-columns-attributes', 'reon_process_field_fold_attributes', 10, 2);
add_filter('reon/field-attributes', 'reon_process_field_fold_attributes', 10, 2);
add_filter('reon/control-parent-attributes', 'reon_process_field_fold_attributes', 10, 2);
if (!function_exists('reon_process_field_fold_attributes')) {

    function reon_process_field_fold_attributes($attributes, $field) {
        if (isset($field['fold']['target'])) {
            $attributes['data-fold-target'] = $field['fold']['target'];
        }
        if (isset($field['fold']['attribute'])) {
            $attributes['data-fold-target-attr'] = $field['fold']['attribute'];
        }
        if (isset($field['fold']['value'])) {
            if (is_array($field['fold']['value'])) {
                $attributes['data-fold-value'] = preg_replace('/"/', "'", json_encode($field['fold']['value']));
            } else {
                $attributes['data-fold-value'] = $field['fold']['value'];
            }
        }

        if (isset($field['fold']['oparator'])) {
            $attributes['data-fold-comp'] = $field['fold']['oparator'];
        }

        return $attributes;
    }

}
add_filter('reon/control-columns-classes', 'reon_process_field_fold_classes', 10, 2);
add_filter('reon/control-parent-classes', 'reon_process_field_fold_classes', 10, 2);
add_filter('reon/field-classes', 'reon_process_field_fold_classes', 10, 2);
if (!function_exists('reon_process_field_fold_classes')) {

    function reon_process_field_fold_classes($classes, $field) {
        if (isset($field['fold'])) {
            $classes[] = 'rn-fold';
            $classes[] = 'rn-folded';
        }
        return $classes;
    }

}




add_filter('reon/control-box-attributes', 'reon_process_control_box_fold_attributes', 10, 2);
if (!function_exists('reon_process_control_box_fold_attributes')) {

    function reon_process_control_box_fold_attributes($attributes, $field) {
        if (isset($field['fold']['target'])) {
            $attributes['data-fold-target'] = $field['fold']['target'];
        }
        if (isset($field['fold']['attribute'])) {
            $attributes['data-fold-target-attr'] = $field['fold']['attribute'];
        }
        if (isset($field['fold']['value'])) {
            if (is_array($field['fold']['value'])) {
                $attributes['data-fold-value'] = preg_replace('/"/', "'", json_encode($field['fold']['value']));
            } else {
                $attributes['data-fold-value'] = $field['fold']['value'];
            }
        }

        if (isset($field['fold']['oparator'])) {
            $attributes['data-fold-comp'] = $field['fold']['oparator'];
        }

        return $attributes;
    }

}




add_filter('reon/control-box-classes', 'reon_process_control_box_fold_classes', 10, 2);
if (!function_exists('reon_process_control_box_fold_classes')) {

    function reon_process_control_box_fold_classes($classes, $field) {
        if (isset($field['fold'])) {
            $classes[] = 'rn-fold';
        }
        return $classes;
    }

}








add_filter('reon/control-attributes', 'reon_process_control_fold_attributes', 10, 2);
if (!function_exists('reon_process_control_fold_attributes')) {

    function reon_process_control_fold_attributes($attributes, $field) {
        if (isset($field['fold_id'])) {
            if (isset($field['item_index'])) {
                $attributes['data-fold-id'] = $field['fold_id'] . $field['item_index'];
            } else {
                $attributes['data-fold-id'] = $field['fold_id'];
            }
            
        }


        if (isset($field['fold']['target'])) {
            $attributes['data-fold-target'] = $field['fold']['target'];
        }
        if (isset($field['fold']['attribute'])) {
            $attributes['data-fold-target-attr'] = $field['fold']['attribute'];
        }
        if (isset($field['fold']['value'])) {
            if (is_array($field['fold']['value'])) {
                $attributes['data-fold-value'] = preg_replace('/"/', "'", json_encode($field['fold']['value']));
            } else {
                $attributes['data-fold-value'] = $field['fold']['value'];
            }
        }

        if (isset($field['fold']['oparator'])) {
            $attributes['data-fold-comp'] = $field['fold']['oparator'];
        }
        if (isset($field['fold']['clear'])) {
            $attributes['data-fold-clear'] = $field['fold']['clear'];
        }
        if (isset($field['fold']['empty'])) {
            $attributes['data-fold-empty'] = $field['fold']['empty'];
        }

        return $attributes;
    }

}

add_filter('reon/control-classes', 'reon_process_control_fold_classes', 10, 2);
if (!function_exists('reon_process_control_fold_classes')) {

    function reon_process_control_fold_classes($classes, $field) {
        if (isset($field['fold'])) {
            $classes[] = 'rn-fold';
        }
        return $classes;
    }

}
