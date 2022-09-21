<div class="rn-field-wrapper<?php echo esc_attr(($g_field['center_head'] == true) ? ' rn-center' : ''); ?>"<?php echo wp_kses_post($group_with); ?><?php echo wp_kses_post((count($dyn_attr) > 0) ? ReonUtil::array_to_attributes($dyn_attr) : ''); ?>>
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-parent-attributes', $attr, $g_field))); ?>>
        <?php
        $grp_fields = array();
        if (isset($g_field['fields'])) {
            $grp_fields = $g_field['fields'];
        }

        $args = ReonUtil::get_screen_args_from_field($g_field);

        if (count($grp_fields) > 0) {

            foreach ($grp_fields as $fld) {
                $field = ReonCore::process_default_field($fld);
                $field['parent_id'] = $g_field['id'];
                $field['center_head'] = false;

                $field = ReonUtil::get_screen_args_from_field($args, $field);

                if ($is_grey_parent == true) {
                    $field['grey-parent'] = true;
                }

                if (!isset($g_field['merge_fields']) || $g_field['merge_fields'] === true) {
                    $field['in_prefix'] = $g_field['in_prefix'] . $g_field['id'] . $g_field['in_postfix'] . '[';
                } else {
                    $field['in_prefix'] = $g_field['in_prefix'];
                }


                $field['in_postfix'] = ']';


                if (isset($field['fields']) && isset($field['merge_fields']) && $field['merge_fields'] === false) {
                    $field_ids = ReonUtil::get_controls_field_ids($field['fields']);
                    foreach ($field_ids as $field_id) {
                        if (isset($g_field['value'][$field_id])) {
                            $field['value'][$field_id] = $g_field['value'][$field_id];
                        }
                    }
                } else if (isset($g_field['value'][$field['id']])) {
                    $field['value'] = $g_field['value'][$field['id']];
                } else {
                    if (isset($field['default'])) {
                        $field['value'] = $field['default'];
                    }
                }
                do_action('reon/render-control-' . $field['type'], $field);
            }
        }
        ?>

    </div>  
</div>
