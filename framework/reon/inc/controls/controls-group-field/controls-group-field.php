<?php

if (!class_exists('Reon_Controls_Group')) {

    class Reon_Controls_Group {

        public static function init() {
            //Remove this in the future
            add_action('reon/render-control-group-field', array(new self(), 'render'), 10, 1);
            add_filter('reon/get-group-field-children-types', array(new self(), 'get_children_types'), 10, 3);
            add_filter('reon/sanitize-group-field', array(new self(), 'sanitize'), 1, 4);
            
            //New type
            add_action('reon/render-control-group', array(new self(), 'render'), 10, 1);
            add_filter('reon/get-group-children-types', array(new self(), 'get_children_types'), 10, 3);
            add_filter('reon/sanitize-group', array(new self(), 'sanitize'), 1, 4);
        }
        
        public static function render($g_field) {
            $group_with = '';
            if (isset($g_field['width'])) {
                $group_with = ' style="width:' . esc_attr($g_field['width']) . ';"';
            }


            $is_grey_parent = false;
            if (isset($g_field['grey-parent'])) {
                $is_grey_parent = true;
            }
            $con_classes = array('rn-group');

            if ( isset( $g_field[ 'fluid-group' ] ) && $g_field[ 'fluid-group' ] == true ) {
                $con_classes[] = 'rn-fluid-group';
            }

            if (isset($g_field['css_class'])) {
                if (is_array($g_field['css_class'])) {
                    foreach ($g_field['css_class'] as $cs_class) {
                        $con_classes[] = $cs_class;
                    }
                } else {
                    $con_classes[] = $g_field['css_class'];
                }
            }

            $attr = array(
                'class' => ReonUtil::array_to_classes(apply_filters('reon/control-parent-classes', $con_classes, $g_field)),
            );

            $dyn_attr = array();
            if (isset($g_field['dyn_switcher_target'])) {
                $dyn_attr['data-dyn_switcher_target'] = $g_field['dyn_switcher_target'];
            }
            if (isset($g_field['dyn_switcher_target_value'])) {
                $dyn_attr['data-dyn_switcher_target_value'] = $g_field['dyn_switcher_target_value'];
            }
            include 'view.php';
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

    Reon_Controls_Group::init();
}