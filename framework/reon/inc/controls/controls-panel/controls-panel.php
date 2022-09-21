<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_panel')) {

    class Reon_Controls_panel {

        public static function init() {
            add_action('reon/render-control-panel', array(new self(), 'render'), 10, 1);
            add_filter('reon/field-classes', array(new self(), 'get_css_classes'), 10, 2);
            add_filter('reon/get-panel-children-types', array(new self(), 'get_children_types'), 10, 3);
            add_filter('reon/sanitize-panel', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($p_field) {
            $con_classes = array('rn-repeater', 'rn-repeater-pfx', 'rn-repeater-panel');
            if (isset($p_field['css_class'])) {
                if (is_array($p_field['css_class'])) {
                    foreach ($p_field['css_class'] as $css_class) {
                        $con_classes[] = $css_class;
                    }
                } else {
                    $con_classes[] = $p_field['css_class'];
                }
            }
            if (isset($p_field['panel_size']) && $p_field['panel_size'] == 'small') {
                $con_classes[] = 'rn-small-repeater';
            }

            if (isset($p_field['panel_size']) && $p_field['panel_size'] == 'smaller') {
                $con_classes[] = 'rn-smaller-repeater';
            }


            $is_white_panel = false;
            if (isset($p_field['white_panel']) && $p_field['white_panel'] == true) {
                $is_white_panel = true;
            }

            if ($is_white_panel == true) {
                $con_classes[] = 'rn-white-repeater';
            } else {
                $con_classes[] = 'rn-grey-repeater';
            }

            $attr = array(
                'class' => ReonUtil::array_to_classes($con_classes, $p_field),
            );

            if (!isset($p_field['merge_fields']) || $p_field['merge_fields'] == true) {
                $attr['data-repeater-pfx'] = '[' . $p_field['id'] . ']';
            }

            if (isset($p_field['width'])) {
                $attr['style'] = 'width:' . $p_field['width'] . ';';
            }
            include 'view.php';
        }

        public static function get_css_classes($classes, $field) {
            $cls = $classes;
            if ($field['type'] == 'panel') {
                $cls[] = 'rn-repeater-ui';
            }
            return $cls;
        }

        public static function get_children_types($field_types, $p_field, $field_args) {
            if (isset($p_field['fields'])) {
                foreach ($p_field['fields'] as $field) {

                    if (!isset($field['sanitize_type'])) {
                        $field['sanitize_type'] = $field['type'];
                    }

                    if (isset($field['merge_fields']) && $field['merge_fields'] == false && isset($field['fields'])) {
                        foreach (ReonUtil::get_controls_field_types($field['fields'], $field_args) as $field_type) {

                            $fld_tp = array(
                                'id' => $field_type['id'],
                                'type' => $field_type['type'],
                                'sanitize_type' => $field_type['sanitize_type'],
                                'screen' => $field_type['screen'],
                                'children' => $field_type['children']
                            );
                            if (isset($field_type['option_name'])) {
                                $fld_tp['option_name'] = $field_type['option_name'];
                            }

                            if (isset($field_type['post_id'])) {
                                $fld_tp['post_id'] = $field_type['post_id'];
                            }
                            if (isset($field_type['metabox_id'])) {
                                $fld_tp['metabox_id'] = $field_type['metabox_id'];
                            }
                            $field_types[] = $fld_tp;
                        }
                    } else {
                        $fld_tp = array(
                            'id' => $field['id'],
                            'type' => $field['type'],
                            'sanitize_type' => $field['sanitize_type'],
                            'screen' => $field_args['screen'],
                            'children' => apply_filters('reon/get-' . $field['type'] . '-children-types', array(), $field, $field_args)
                        );
                        if (isset($field_args['option_name'])) {
                            $fld_tp['option_name'] = $field_args['option_name'];
                        }

                        if (isset($field_args['post_id'])) {
                            $fld_tp['post_id'] = $field_args['post_id'];
                        }
                        if (isset($field_args['metabox_id'])) {
                            $fld_tp['metabox_id'] = $field_args['metabox_id'];
                        }

                        $field_types[] = $fld_tp;
                    }
                }
            }
            return $field_types;
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            $opt = array();

            foreach ($children as $field_type) {
                if (isset($raw_option[$field_type['id']])) {
                    $sanitize_type = $field_type['type'];
                    if (isset($field_type['sanitize_type'])) {
                        $sanitize_type = $field_type['sanitize_type'];
                    }
                    $opt[$field_type['id']] = apply_filters('reon/sanitize-' . $sanitize_type, $raw_option[$field_type['id']], $raw_option[$field_type['id']], $field_type['id'], $field_type['children']);
                }
            }

            return $opt;
        }

    }

    Reon_Controls_panel::init();
}