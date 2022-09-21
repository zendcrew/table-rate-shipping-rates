<div class="rn-repeater-section-body">
    <table class="rn-repeater-section-inner">
        <tbody class="rn-ui-subapp">
            <?php
            $fields = array();
            if (isset($template['fields'])) {
                $fields = $template['fields'];
            }

            $args['id'] = $template['id'];
            $fields = apply_filters('roen/get-repeater-template-' . $template_args['filter_id'] . '-' . $template['id'] . '-fields', $fields, $args);

            $index = 0;
            foreach ($fields as $fld) {
                $field = ReonCore::process_default_field($fld);
                $field['in_repeater'] = true;
                $field['repeater_id'] = $field['id'];
                if (isset($template['head']['title_field'])) {
                    $field['title_field'] = $template['head']['title_field'];
                }
                if (isset($template['head']['subtitle_field'])) {
                    $field['subtitle_field'] = $template['head']['subtitle_field'];
                }

                $field = ReonUtil::get_screen_args_from_field($template_args, $field);

                if ($template_args['white_repeater'] == false) {
                    $field['grey-parent'] = true;
                }

                if ($index == 0) {
                    $field['first'] = true;
                }

                if (!isset($fields[$index + 1]) || $fields[$index + 1]['type'] == 'heading') {
                    $field['last'] = true;
                }
                $field['in_prefix'] = '';
                $field['in_postfix'] = '';





                if (isset($field['fields']) && isset($field['merge_fields']) && $field['merge_fields'] === false) {
                    $field_ids = ReonUtil::get_controls_field_ids($field['fields']);
                    foreach ($field_ids as $field_id) {
                        if (isset($template_value[$field_id])) {
                            $field['value'][$field_id] = $template_value[$field_id];
                        }
                    }
                } else if (isset($template_value[$field['id']])) {
                    $field['value'] = $template_value[$field['id']];
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