<div class="rn-field-wrapper<?php echo esc_attr(($p_field['center_head'] == true) ? ' rn-center' : ''); ?>" style="width:100%;">
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes($attr)); ?>>
        <div class="rn-repeater-sections rn-sortable-repeater">
            <div class="rn-repeater-section rn-active-section">
                <div class="rn-repeater-section-body rn-repeater-panel-body">
                    <table class="rn-repeater-section-inner">
                        <tbody class="rn-ui-subapp">
                            <?php
                            $fields = array();
                            if (isset($p_field['fields'])) {
                                $fields = $p_field['fields'];
                            }
                            $filter_id = $p_field['id'];
                            if (isset($p_field['filter_id']) && $p_field['filter_id'] != '') {
                                $filter_id = $p_field['filter_id'];
                            }
                            $args = ReonUtil::get_screen_args_from_field($p_field);
                            $fields = apply_filters('roen/get-panel-' . $filter_id . '-fields', $fields, $args);

                            $index = 0;

                            foreach ($fields as $fld) {
                                $field = ReonCore::process_default_field($fld);

                                $field = ReonUtil::get_screen_args_from_field($args, $field);



                                if ($is_white_panel == false) {
                                    $field['grey-parent'] = true;
                                }



                                if ($index == 0) {
                                    $field['first'] = true;
                                }

                                if (!isset($fields[$index + 1]) || $fields[$index + 1]['type'] == 'heading') {
                                    $field['last'] = true;
                                }

                                if (!isset($p_field['merge_fields']) || $p_field['merge_fields'] === true) {
                                    $field['in_prefix'] = $p_field['in_prefix'] . $p_field['id'] . $p_field['in_postfix'] . '[';
                                } else {
                                    $field['in_prefix'] = $p_field['in_prefix'];
                                }
                                $field['in_postfix'] = ']';



                                if (isset($field['fields']) && isset($field['merge_fields']) && $field['merge_fields'] === false) {
                                    $field_ids = ReonUtil::get_controls_field_ids($field['fields']);
                                    foreach ($field_ids as $field_id) {
                                        if (isset($p_field['value'][$field_id])) {
                                            $field['value'][$field_id] = $p_field['value'][$field_id];
                                        }
                                    }
                                } else if (isset($p_field['value'][$field['id']])) {
                                    $field['value'] = $p_field['value'][$field['id']];
                                } else {
                                    if (isset($field['default'])) {
                                        $field['value'] = $field['default'];
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
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>                
    </div>
</div>