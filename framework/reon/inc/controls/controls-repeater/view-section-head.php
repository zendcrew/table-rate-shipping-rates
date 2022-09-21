<div class="rn-repeater-section-head">

    <div class="rn-repeater-head-left">
        <?php
        if (isset($template_args['sortable']['enabled']) && $template_args['sortable']['enabled'] == true) {
            ?>
            <div class="rn-repeater-head-buttons">
                <span class="rn-repeater-head-sort"><i class="fa fa-bars"></i></span>
            </div>
            <?php
        }
        if (isset($template['head']['title'])) {
            $title = $template['head']['title'];
            if (isset($template['head']['subtitle'])) {
                $title = wp_kses_post($title . '<span>' . $template['head']['subtitle'] . '</span>');
            }
            $default_title = '';
            if (isset($template['head']['defaut_title'])) {
                $default_title = ' data-repeater-default-title="' . esc_attr($template['head']['defaut_title']) . '"';
            }

            $default_subtitle = '';

            if (isset($template['head']['defaut_subtitle'])) {
                $default_subtitle = ' data-repeater-default-subtitle="' . esc_attr($template['head']['defaut_subtitle']) . '"';
            }

            $tooltip = '';
            if (isset($template['head']['tooltip'])) {
                $tooltip = '<span class="rn-tips" title="' . esc_attr($template['head']['tooltip']) . '"></span>';
            }
            ?>
            <div class="rn-repeater-head-title"<?php echo wp_kses_post($default_title . $default_subtitle); ?>><?php echo wp_kses_post($title . $tooltip); ?></div>
            <?php
        }

        $head_fields = array();
        if (isset($template['head']['fields'])) {
            $head_fields = $template['head']['fields'];
        }

        $args['id'] = $template['id'];
        $head_fields = apply_filters('roen/get-repeater-template-' . $template_args['filter_id'] . '-' . $template['id'] . '-head-fields', $head_fields, $args);

        $heas_left = self::get_section_head_fields($head_fields, 'left');
        if (count($heas_left) > 0) {
            ?>
            <div class="rn-repeater-head-controls">
                <?php
                foreach ($heas_left as $lfld) {
                    $lfield = ReonCore::process_default_field($lfld);
                    $lfield['in_repeater'] = true;


                    $lfield = ReonUtil::get_screen_args_from_field($template_args, $lfield);

                    if ($template_args['white_repeater'] == false) {
                        $lfield['grey-parent'] = true;
                    }

                    $lfield['in_prefix'] = '';
                    $lfield['in_postfix'] = '';

                    if (isset($lfield['fields']) && isset($lfield['merge_fields']) && $lfield['merge_fields'] === false) {
                        $field_ids = ReonUtil::get_controls_field_ids($lfield['fields']);
                        foreach ($field_ids as $field_id) {
                            if (isset($template_value[$field_id])) {
                                $lfield['value'][$field_id] = $template_value[$field_id];
                            }
                        }
                    } else if (isset($template_value[$lfield['id']])) {
                        $lfield['value'] = $template_value[$lfield['id']];
                    } else {
                        if (isset($lfield['default'])) {
                            $lfield['value'] = $lfield['default'];
                        }
                    }

                    do_action('reon/render-control-' . $lfield['type'], $lfield);
                }
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="rn-repeater-head-right">
        <?php
        $heas_right = self::get_section_head_fields($head_fields, 'right');
        if (count($heas_right) > 0) {
            ?>
            <div class="rn-repeater-head-controls">
                <?php
                foreach ($heas_right as $rfld) {
                    $rfield = ReonCore::process_default_field($rfld);
                    $rfield['in_repeater'] = true;


                    $rfield = ReonUtil::get_screen_args_from_field($template_args, $rfield);


                    if ($template_args['white_repeater'] == false) {
                        $rfield['grey-parent'] = true;
                    }

                    $rfield['in_prefix'] = '';
                    $rfield['in_postfix'] = '';



                    if (isset($rfield['fields']) && isset($rfield['merge_fields']) && $rfield['merge_fields'] === false) {
                        $field_ids = ReonUtil::get_controls_field_ids($rfield['fields']);
                        foreach ($field_ids as $field_id) {
                            if (isset($template_value[$field_id])) {
                                $rfield['value'][$field_id] = $template_value[$field_id];
                            }
                        }
                    } else if (isset($template_value[$rfield['id']])) {
                        $rfield['value'] = $template_value[$rfield['id']];
                    } else {
                        if (isset($rfield['default'])) {
                            $rfield['value'] = $rfield['default'];
                        }
                    }

                    do_action('reon/render-control-' . $rfield['type'], $rfield);
                }
                ?>
            </div>
            <?php
        }
        ?>                    
        <?php
        $static_id = array();

        if (isset($template_args['static_template'])) {
            $static_id = $template_args['static_template'];
        }
        if (($template['head']['delete_button'] == true || $template['head']['delete_button'] == true) && !self::is_static_template($template['id'], $static_id)) {
            ?>
            <div class="rn-repeater-head-buttons">
                <?php
                if ($template['head']['buttons_sep'] == true) {
                    ?>
                    <span class="rn-repeater-head-sep"></span>
                    <?php
                }
                ?>
                <?php
                if ($template['head']['clone_button'] == true) {
                    ?>
                    <span class="rn-repeater-head-duplicate"><i class="dashicons dashicons-admin-page"></i></span>
                    <?php
                }
                ?>
                <?php
                if ($template['head']['delete_button'] == true) {
                    ?>
                    <span class="rn-repeater-head-delete"><i class="dashicons dashicons-no-alt"></i></span>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="rn-clear"></div>
</div>