<div class="rn-field-wrapper<?php echo wp_kses_post(($field['center_head'] == true) ? ' rn-center' : ''); ?>" style="width:100%;">
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?>>
        <div class="<?php echo esc_attr($con_classes); ?>"<?php echo wp_kses_post($connect_with); ?>>
            <?php
            if (isset($field['value']) && is_array($field['value']) && count($field['value']) > 0) {
                $args = self::template_args($field);

                foreach ($field['value'] as $section_value) {
                    if (count($templates) > 0) {
                        $template_id = '';
                        if (isset($field['section_type_id']) && isset($section_value[$field['section_type_id']])) {
                            $template_id = $section_value[$field['section_type_id']];
                        }
                        $template = self::get_template_by_id($templates, $template_id);
                        self::render_section($template, $section_value, $args);
                    }
                }
            }
            ?>
        </div>
        <div class="rn-repeater-template-adder">
            <?php
            $template_adder = array('button_text' => 'Add Section');
            if (isset($field['template_adder'])) {
                $template_adder = $field['template_adder'];
            }
            $adder_position = '';
            if (isset($template_adder['position'])) {
                if ($template_adder['position'] == 'right') {
                    $adder_position = ' class="rn-repeater-adder-right"';
                }
                if ($template_adder['position'] == 'center') {
                    $adder_position = ' class="rn-repeater-adder-center"';
                }
            }
            ?>
            <div<?php echo wp_kses_post($adder_position); ?>>
                <div class="rn-field-wrapper">
                    <?php
                    if (isset($template_adder['show_list']) && $template_adder['show_list'] == true) {
                        $list_width = '200px';
                        if (isset($template_adder['list_width'])) {
                            $list_width = $template_adder['list_width'];
                        }
                        $grey_p = '';
                        if (isset($field['white_repeater']) && $field['white_repeater'] == true) {
                            $grey_p = ' rn-grey-parent';
                        }
                        ?>
                        <div class="rn-select2<?php echo esc_attr($grey_p); ?>">
                            <?php
                            if (isset($template_adder['list_label']) || isset($template_adder['list_icon'])) {
                                $list_label = '';
                                $list_icon = '';
                                if (isset($template_adder['list_label'])) {
                                    $list_label = $template_adder['list_label'];
                                }
                                if (isset($template_adder['list_icon'])) {
                                    $list_icon = '<i class="' . esc_attr($template_adder['list_icon']) . '"></i>';
                                }
                                ?>
                                <label class="rn-field rn-single rn-no-space">
                                    <span class="rn-label"><?php echo wp_kses_post($list_icon) . esc_html($list_label); ?></span>           
                                </label>
                                <?php
                            }
                            ?>
                            <div class="rn-field rn-repeater-template-list">                           
                                <select data-minimum-input-length="0" data-minimum-results-forsearch="10" style="width:<?php echo esc_attr($list_width); ?>;">
                                    <?php
                                    $static_template_id = array();
                                    if (isset($field['static_template'])) {
                                        $static_template_id = $field['static_template'];
                                    }

                                    foreach (self::get_templates_by_group_id($templates, $static_template_id, false) as $temp) {
                                        ?>
                                        <option value="rp_<?php echo esc_attr($temp['id']); ?>"><?php echo esc_html($temp['list_label']); ?></option>
                                        <?php
                                    }
                                    if ( count($template_groups) > 0) {
                                        foreach ($template_groups as $key => $template_group) {
                                            ?>
                                            <optgroup label="<?php echo esc_attr($template_group); ?>">
                                                <?php
                                                foreach (self::get_templates_by_group_id($templates, $static_template_id, $key) as $temp) {
                                                    ?>
                                                    <option value="rp_<?php echo esc_attr($temp['id']); ?>"><?php echo esc_html($temp['list_label']); ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </optgroup>
                                            <?php
                                        }
                                    }
                                    ?>  
                                </select>
                            </div>                                
                        </div>
                        <?php
                    }
                    ?>                            
                    <a href="#" class="rn-repeater-template-btn rn-btn rn-icon-btn rn-btn-primary"><i class="dashicons dashicons-plus-alt2"></i><?php echo esc_html($template_adder['button_text']); ?></a>
                </div>
            </div>
        </div>
        <div class="rn-repeater-templates">
            <?php
            $args = self::template_args($field);

            foreach ($templates as $template) {
                if (isset($field['static_template']) && self::is_static_template($template['id'], $field['static_template'])) {
                    continue;
                }
                $template_con_attr = '';
                if (isset($template['id'])) {
                    $template_con_attr = ' data-repeater-template-id="rp_' . esc_attr($template['id']) . '"';
                }
                ?>
                <div<?php echo wp_kses_post($template_con_attr); ?>>
                    <?php self::render_section($template, array(), $args); ?>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>