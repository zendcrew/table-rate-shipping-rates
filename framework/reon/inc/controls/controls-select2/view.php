<div <?php echo wp_kses_post(ReonUtil::array_to_attributes($wrapper_attr)); ?>>            
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-box-attributes', $box_attributes, $field))); ?>>
        <?php
        if (isset($field['label'])) {
            $label = $field['label'];
            $label['single'] = true;
            $label['no-space'] = true;
            do_action('reon/render-control-label', $label);
        }
        ?> 
        <span class="rn-field">                                                       
            <select  <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?>>
                <?php
                $options = array();
                $selected = array();
                $disabled_list = array();

                if (isset($field['options'])) {
                    $options = $field['options'];
                }


                if (isset($field['value'])) {
                    $selected = $field['value'];
                }

                if (isset($field['data']) && $field['data']['ajax'] != true) {
                    $data_args = array(
                        'source' => $field['data']['source']
                    );

                    if (isset($field['data']['value_col'])) {
                        $data_args['value_col'] = $field['data']['value_col'];
                    }

                    if (isset($field['data']['pagesize'])) {
                        $data_args['pagesize'] = $field['data']['pagesize'];
                    }

                    if (isset($field['data']['show_value'])) {
                        $data_args['show_value'] = $field['data']['show_value'];
                    }

                    foreach (ReonApi::get_data_list($data_args) as $key => $value) {
                        $options[$key] = $value;
                    }

                    if (isset($field['disabled_list_filter'])) {
                        $disabled_list = apply_filters($field['disabled_list_filter'], $disabled_list, $options);
                    }
                    ReonUtil::array_to_dropdown_list($options, $selected, $disabled_list);
                } else if (isset($field['data']) && $field['data']['ajax'] == true) {

                    $data_args = array(
                        'source' => $field['data']['source']
                    );

                    if (isset($field['data']['value_col'])) {
                        $data_args['value_col'] = $field['data']['value_col'];
                    }
                    if (isset($field['data']['value_col_pre'])) {
                        $data_args['value_col_pre'] = $field['data']['value_col_pre'];
                    }
                    if (isset($field['data']['show_value'])) {
                        $data_args['show_value'] = $field['data']['show_value'];
                    }
                    $ajax_selected = ReonApi::map_data_list($selected, $data_args);
                    ReonUtil::array_to_dropdown_list($ajax_selected, $selected);
                } else if (isset($field['tags']) && $field['tags'] == true) {
                    $tag_options = array();
                    foreach ($selected as $value) {
                        $tag_options[$value] = $value;
                    }
                    ReonUtil::array_to_dropdown_list($tag_options, $selected);
                } else {

                    if (isset($field['disabled_list_filter'])) {
                        $disabled_list = apply_filters($field['disabled_list_filter'], $disabled_list, $options);
                    }
                    ReonUtil::array_to_dropdown_list($options, $selected, $disabled_list);
                }
                ?>
            </select>
        </span>
    </div><?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
</div>