<div class="rn-repeater-section rn-repeater-pfx">
    <?php if (isset($template_args['section_type_id'])) {
        ?>
        <input type="hidden" data-repeater-name="<?php echo esc_attr($template_args['section_type_id']); ?>" value="<?php echo esc_attr($template['id']); ?>" />
    <?php }
    ?>
    <div class="rn-repeater-section-head">
        <?php if (isset($template_args['sortable']['enabled']) && $template_args['sortable']['enabled'] == true) {
            ?>
            <div class="rn-repeater-head-left-buttons">
                <div class="rn-repeater-head-buttons">
                    <span class="rn-repeater-head-sort"><i class="fa fa-bars"></i></span>
                </div>
            </div>
            <?php
        }
        $static_id = array();
        if (isset($template_args['static_template'])) {
            $static_id = $template_args['static_template'];
        }

        $no_segment_css = '';
        $no_buttons = false;
        $is_sortable = false;
        if (isset($template_args['sortable']['enabled'])) {
            $is_sortable = $template_args['sortable']['enabled'];
        }
        if (($template['delete_button'] == false && $template['clone_button'] == false && $template['empy_button'] == false) || self::is_static_template($template['id'], $static_id)) {
            $no_buttons = true;
        }
        if ($template['empy_button'] == true) {
            $no_buttons = false;
        }
        if ($no_buttons == true && $is_sortable == false)
            $no_segment_css = ' rn-repeater-no-segments';
        ?>

        <div class="rn-repeater-section-head-controls<?php echo esc_attr($no_segment_css); ?>">
            <?php
            $fields = array();
            if (isset($template['fields'])) {
                $fields = $template['fields'];
            }

            $args['id'] = $template['id'];
            $fields = apply_filters('roen/get-simple-repeater-template-' . $template_args['filter_id'] . '-' . $template['id'] . '-fields', $fields, $args);

            $switcher_id = self::get_dyn_switcher_field_id($fields);


            $heas_left = self::get_fields($fields, 'left');
            $heas_right = self::get_fields($fields, 'right');
            ?>
            <div class="rn-repeater-head-left" style="width:<?php echo esc_attr($template_args['left_width']); ?>;">
                <?php
                if (count($heas_left) > 0) {
                    ?>
                    <div class="rn-repeater-head-controls" style="width: 100%;">
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

                            if (isset($template_value[$lfield['id']])) {
                                $lfield['value'] = $template_value[$lfield['id']];
                            } else {
                                if (isset($lfield['default'])) {
                                    $lfield['value'] = $lfield['default'];
                                }
                            }

                            if ($in_template == true && isset($lfield['dyn_switcher_target'])) {
                                ob_start();
                                ?><div id="dyn_<?php echo esc_attr($lfield['dyn_switcher_target_value']); ?>"><?php
                                do_action('reon/render-control-' . $lfield['type'], $lfield);
                                ?></div><?php
                                ReonUI::set_dynamic_contents(ob_get_clean(), $lfield['dyn_switcher_target_value']);
                            } else if ($switcher_id == $lfield['id']) {
                                do_action('reon/render-control-' . $lfield['type'], $lfield);
                            } else if (isset($lfield['dyn_switcher_target_value']) && isset($template_value[$switcher_id])) {
                                if ($lfield['dyn_switcher_target_value'] == $template_value[$switcher_id]) {
                                    do_action('reon/render-control-' . $lfield['type'], $lfield);
                                }
                            } else {
                                do_action('reon/render-control-' . $lfield['type'], $lfield);
                            }
                        }
                        ?>
                    </div>
                <?php } ?>
            </div>

            <?php
            if (count($heas_right) > 0) {
                ?>
                <div class="rn-repeater-head-right" style="width:<?php echo esc_attr($template_args['right_width']); ?>;">
                    <div class="rn-repeater-head-controls" style="float:right;">
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

                            if ($in_template == true && isset($rfield['dyn_switcher_target'])) {
                                ob_start();
                                ?><div id="dyn_<?php echo esc_attr($rfield['dyn_switcher_target_value']); ?>" style="display:none; visibility: hidden;"><?php
                                do_action('reon/render-control-' . $rfield['type'], $rfield);
                                ?></div><?php
                                ReonUI::set_dynamic_contents(ob_get_clean(), $rfield['dyn_switcher_target_value']);
                            } else if ($switcher_id == $rfield['id']) {
                                do_action('reon/render-control-' . $rfield['type'], $rfield);
                            } else if (isset($rfield['dyn_switcher_target_value']) && isset($template_value[$switcher_id])) {
                                if ($rfield['dyn_switcher_target_value'] == $template_value[$switcher_id]) {
                                    do_action('reon/render-control-' . $rfield['type'], $rfield);
                                }
                            } else {
                                do_action('reon/render-control-' . $rfield['type'], $rfield);
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>  

        </div>
        <?php
        if (($template['delete_button'] == true || $template['delete_button'] == true) && !self::is_static_template($template['id'], $static_id)) {
            $btns_with = '';

            if (isset($template['buttons_box_width'])) {
                $btns_with = ' style="' . esc_attr('width:' . $template['buttons_box_width'] . ';') . '"';
            }
            ?>
            <div class="rn-repeater-head-right-buttons">                
                <div class="rn-repeater-head-buttons"<?php echo wp_kses_post($btns_with); ?>>
                    <?php
                    if ($template['buttons_sep'] == true) {
                        ?>
                        <span class="rn-repeater-head-sep"></span>
                        <?php
                    }
                    ?>
                    <?php
                    if ($template['clone_button'] == true) {
                        ?>
                        <span class="rn-repeater-head-duplicate"><i class="dashicons dashicons-admin-page"></i></span>
                        <?php
                    }
                    ?>
                    <?php
                    if ($template['delete_button'] == true) {
                        ?>
                        <span class="rn-repeater-head-delete"><i class="dashicons dashicons-no-alt"></i></span>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        } else if ($template['empy_button'] == true) {
            $btns_with = ' style="' . esc_attr('width:' . $template['buttons_box_width'] . ';') . '"';
            ?>
            <div class="rn-repeater-head-right-buttons">                
                <div class="rn-repeater-head-buttons"<?php echo wp_kses_post($btns_with); ?>>
                    <span class="rn-repeater-head-empty"></span>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="rn-clear"></div>
    </div>
</div>