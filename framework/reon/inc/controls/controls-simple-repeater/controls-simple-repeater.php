<?php

if (!class_exists('Reon')) {
    return;
}

if (!class_exists('Reon_Controls_SimpleRepeater')) {

    class Reon_Controls_SimpleRepeater {

        public static function init() {
            add_action('reon/render-control-simple-repeater', array(new self(), 'render'), 10, 1);
            add_filter('reon/field-classes', array(new self(), 'get_css_classes'), 20, 2);
            add_filter('reon/control-attributes', array(new self(), 'get_attributes'), 20, 2);
            add_filter('reon/get-simple-repeater-children-types', array(new self(), 'get_children_types'), 10, 3);
            add_filter('reon/sanitize-simple-repeater', array(new self(), 'sanitize'), 1, 4);
        }

        public static function render($field) {
            if (!isset($field['filter_id']) || $field['filter_id'] == '') {
                $field['filter_id'] = $field['id'];
            }
            $templates = array();
            if (isset($field['templates'])) {
                $templates = $field['templates'];
            }
            $args = ReonUtil::get_screen_args_from_field($field);

            $templates = apply_filters('reon/get-simple-repeater-field-' . $field['filter_id'] . '-templates', $templates, $args);

            $template_groups = apply_filters('reon/get-simple-repeater-field-' . $field['filter_id'] . '-template-groups', array(), $args);


            $attributes = array(
                'data-repeater-id' => $field['id'],
                'data-repeater-pfx' => '[' . $field['id'] . ']',
            );

            if (isset($field['max_sections'])) {
                $attributes['data-max_sections'] = $field['max_sections'];
            }

            if (isset($field['max_sections_msg'])) {
                $attributes['data-max_sections_msg'] = $field['max_sections_msg'];
            }



            if (!isset($field['css_class'])) {
                $field['css_class'] = array();
            }
            $field['css_class'][] = 'rn-repeater';
            $field['css_class'][] = 'rn-simple-repeater';
            $field['css_class'][] = 'rn-repeater-pfx';

            if (isset($field['white_repeater']) && $field['white_repeater'] == true) {
                $field['css_class'][] = 'rn-white-repeater';
            } else {
                $field['css_class'][] = 'rn-grey-repeater';
            }

            if (isset($field['repeater_size']) && $field['repeater_size'] == 'small') {
                $field['css_class'][] = 'rn-small-repeater';
            }

            if (isset($field['repeater_size']) && $field['repeater_size'] == 'smaller') {
                $field['css_class'][] = 'rn-smaller-repeater';
            }


            $con_classes = 'rn-repeater-sections';
            $connect_with = '';
            if (isset($field['sortable']['enabled']) && $field['sortable']['enabled'] == true) {
                $con_classes = $con_classes . ' rn-sortable-repeater';
            } else {
                $con_classes = $con_classes . ' rn-no-sortable';
            }
            if (isset($field['sortable']['connect_with']) && $field['sortable']['connect_with'] != '') {
                $con_classes = $con_classes . ' ' . $field['sortable']['connect_with'];
                $connect_with = ' data-sortable-connectwith="' . esc_attr($field['sortable']['connect_with']) . '"';
            }

            if (isset($field['head']['enable']) && $field['head']['enable'] == true) {

                $field['head']['left_width'] = $field['left_width'];
                $field['head']['right_width'] = $field['right_width'];
                $field['head']['white_spacer'] = '';
                $field['head']['sortable'] = false;
                if (isset($field['sortable']['enabled'])) {
                    $field['head']['sortable'] = $field['sortable']['enabled'];
                }
                if (isset($field['buttons_box_width'])) {
                    $field['head']['white_spacer'] = $field['buttons_box_width'];
                }
            }

            include 'view.php';
        }

        private static function render_section($template, $template_value, $template_args, $in_template = false) {
            $args = ReonUtil::get_screen_args_from_field($template_args);

            if (!isset($template['buttons_sep'])) {
                $template['buttons_sep'] = $template_args['buttons_sep'];
            }

            if (!isset($template['buttons_box_width'])) {
                if (isset($template_args['buttons_box_width'])) {
                    $template['buttons_box_width'] = $template_args['buttons_box_width'];
                }
            }

            if (!isset($template['delete_button'])) {
                $template['delete_button'] = $template_args['delete_button'];
            }

            if (!isset($template['clone_button'])) {
                $template['clone_button'] = $template_args['clone_button'];
            }

            if (!isset($template['empy_button'])) {
                $template['empy_button'] = $template_args['empy_button'];
            }



            include 'view-section.php';
        }

        private static function render_head($head) {
            $left_titles = self::get_header_titles($head['titles'], 'left');
            $right_titles = self::get_header_titles($head['titles'], 'right');

            $left_width = $head['left_width'];
            $right_width = $head['right_width'];

            $is_sortable = $head['sortable'];
            $white_spacer = $head['white_spacer'];

            include 'view-head.php';
        }

        private static function get_dyn_switcher_field_id($fields) {
            foreach ($fields as $field) {
                if (isset($field['dyn_switcher_id'])) {
                    return $field['id'];
                }
            }
            return '';
        }

        private static function get_templates_by_group_id($templates, $static_template_id = array(), $group_id = false) {
            $temps = array();
            if ($group_id == false) {
                foreach ($templates as $temp) {
                    if (self::is_static_template($temp['id'], $static_template_id)) {
                        continue;
                    }

                    if (!isset($temp['group_id'])) {
                        $temps[] = $temp;
                    }
                }
            } else {
                foreach ($templates as $temp) {
                    if (self::is_static_template($temp['id'], $static_template_id)) {
                        continue;
                    }

                    if (isset($temp['group_id']) && $temp['group_id'] == $group_id) {
                        $temps[] = $temp;
                    }
                }
            }

            return $temps;
        }

        private static function get_template_by_id($templates, $template_id) {
            foreach ($templates as $template) {
                if ($template['id'] == $template_id) {
                    return $template;
                }
            }
            if (count($templates) > 0) {
                return $templates[0];
            }
            return array();
        }

        private static function get_fields($fields, $pos) {
            $re_fields = array();
            foreach ($fields as $field) {
                if ($pos == 'right') {
                    if (isset($field['position']) && $field['position'] == 'right') {
                        $re_fields[] = $field;
                    }
                } else {
                    if (!isset($field['position']) || $field['position'] != 'right') {
                        $re_fields[] = $field;
                    }
                }
            }
            return $re_fields;
        }

        private static function get_header_titles($cols, $pos) {
            $re_cols = array();
            foreach ($cols as $col) {
                if ($pos == 'right') {
                    if (isset($col['position']) && $col['position'] == 'right') {
                        if (!isset($col['tooltip'])) {
                            $col['tooltip'] = '';
                        }
                        $re_cols[] = $col;
                    }
                } else {
                    if (!isset($col['position']) || $col['position'] != 'right') {
                        if (!isset($col['tooltip'])) {
                            $col['tooltip'] = '';
                        }
                        $re_cols[] = $col;
                    }
                }
            }
            return $re_cols;
        }

        private static function get_template_args($field) {
            $args = ReonUtil::get_screen_args_from_field($field);
            $args['repeater_id'] = $field['id'];
            $args['filter_id'] = $field['filter_id'];
            $args['buttons_sep'] = true;
            $args['delete_button'] = true;
            $args['empy_button'] = false;
            $args['clone_button'] = true;
            $args['white_repeater'] = false;

            $args['left_width'] = '100%';
            $args['right_width'] = '0%';
            if (isset($field['left_width'])) {
                $args['left_width'] = $field['left_width'];
            }
            if (isset($field['left_width'])) {
                $args['right_width'] = $field['right_width'];
            }


            if (isset($field['new_field_args'])) {
                $args['field_args'] = $field['new_field_args'];
            }

            if (isset($field['white_repeater']) && $field['white_repeater'] == true) {
                $args['white_repeater'] = true;
            }

            if (isset($field['buttons_box_width'])) {
                $args['buttons_box_width'] = $field['buttons_box_width'];
            }

            if (isset($field['buttons_sep'])) {
                $args['buttons_sep'] = $field['buttons_sep'];
            }

            if (isset($field['delete_button'])) {
                $args['delete_button'] = $field['delete_button'];
            }

            if (isset($field['empy_button'])) {
                $args['empy_button'] = $field['empy_button'];
            }

            if (isset($field['clone_button'])) {
                $args['clone_button'] = $field['clone_button'];
            }
            if (isset($field['static_template'])) {
                $args['static_template'] = $field['static_template'];
            }

            if (isset($field['sortable'])) {
                $args['sortable'] = $field['sortable'];
            }
            if (isset($field['section_type_id'])) {
                $args['section_type_id'] = $field['section_type_id'];
            }


            return $args;
        }

        private static function is_static_template($template_id, $static_template_ids) {
            $stic_ids = $static_template_ids;
            if (!is_array($stic_ids)) {
                $stic_ids = array($static_template_ids);
            }
            foreach ($stic_ids as $stic_id) {
                if ($stic_id == '') {
                    return false;
                }
                if ($stic_id == $template_id) {
                    return true;
                }
            }

            return false;
        }

        public static function get_css_classes($classes, $field) {
            $cls = $classes;
            if ($field['type'] == 'simple-repeater') {
                $cls[] = 'rn-repeater-ui';
            }
            return $cls;
        }

        public static function get_attributes($attributes, $field) {
            $attrs = array();
            if (isset($field['in_repeater']) && $field['in_repeater'] == true) {
                foreach ($attributes as $key => $value) {

                    if ($key == 'id' || $key == 'name') {
                        continue;
                    }
                    $attrs[$key] = $value;
                }
                $attrs['data-repeater-name'] = $field['id'];
                return $attrs;
            }
            return $attributes;
        }

        public static function get_children_types($field_types, $rep_field, $field_args) {

            $templates = array();
            if (isset($rep_field['templates'])) {
                $templates = $rep_field['templates'];
            }
            $rep_field['screen'] = $field_args['screen'];
            if (isset($field_args['option_name'])) {
                $rep_field['option_name'] = $field_args['option_name'];
            }
            if (isset($field_args['post_id'])) {
                $rep_field['post_id'] = $field_args['post_id'];
            }
            if (isset($field_args['metabox_id'])) {
                $rep_field['metabox_id'] = $field_args['metabox_id'];
            }
            if (!isset($rep_field['filter_id']) || $rep_field['filter_id'] == '') {
                $rep_field['filter_id'] = $rep_field['id'];
            }

            $args = ReonUtil::get_screen_args_from_field($rep_field);
            $template_args = self::get_template_args($rep_field);
            $args['filter_id'] = $rep_field['id'];

            if (isset($rep_field['filter_id']) && $rep_field['filter_id'] != '') {
                $args['filter_id'] = $rep_field['filter_id'];
            }

            $templates = apply_filters('reon/get-simple-repeater-field-' . $args['filter_id'] . '-templates', $templates, $args);

            $fields = array();




            foreach ($templates as $template) {



                if (isset($template['fields'])) {
                    foreach ($template['fields'] as $t_field) {
                        $fields[] = $t_field;
                    }
                }

                $args['id'] = $template['id'];
                $fields = apply_filters('roen/get-simple-repeater-template-' . $template_args['filter_id'] . '-' . $template['id'] . '-fields', $fields, $args);

                $sec_types = array(
                    'id' => $template['id'],
                    'type' => 'repeater-section',
                );

                if (isset($rep_field['section_type_id'])) {
                    $sec_types['section_type_id'] = $rep_field['section_type_id'];
                }
                foreach ($fields as $field) {

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

                            $sec_types['children'][] = $fld_tp;
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

                        $sec_types['children'][] = $fld_tp;
                    }
                }
                $field_types[] = $sec_types;
            }





            return $field_types;
        }

        public static function sanitize($sanitized_option, $raw_option, $field_id, $children) {
            $opt = array();

            if (is_array($raw_option)) {
                foreach ($raw_option as $r_option) {
                    foreach ($children as $sec_type) {
                        if (isset($sec_type['section_type_id'])) {
                            $sec_type_id = $sec_type['section_type_id'];
                            if (isset($r_option[$sec_type_id]) && $r_option[$sec_type_id] == $sec_type['id']) {
                                $option = array();
                                $option[$sec_type_id] = $sec_type['id'];
                                foreach ($sec_type['children'] as $field_type) {
                                    if (isset($r_option[$field_type['id']])) {
                                        $sanitize_type = $field_type['type'];
                                        if (isset($field_type['sanitize_type'])) {
                                            $sanitize_type = $field_type['sanitize_type'];
                                        }
                                        $option[$field_type['id']] = apply_filters('reon/sanitize-' . $sanitize_type, $r_option[$field_type['id']], $r_option[$field_type['id']], $field_type['id'], $field_type['children']);
                                    }
                                }
                                $opt[] = $option;
                            }
                        } else {

                            $option = array();
                            foreach ($sec_type['children'] as $field_type) {
                                if (isset($r_option[$field_type['id']])) {
                                    $sanitize_type = $field_type['type'];
                                    if (isset($field_type['sanitize_type'])) {
                                        $sanitize_type = $field_type['sanitize_type'];
                                    }
                                    $option[$field_type['id']] = apply_filters('reon/sanitize-' . $sanitize_type, $r_option[$field_type['id']], $r_option[$field_type['id']], $field_type['id'], $field_type['children']);
                                }
                            }
                            $opt[] = $option;
                        }
                    }
                }
            }


            return $opt;
        }

    }

    Reon_Controls_SimpleRepeater::init();
}