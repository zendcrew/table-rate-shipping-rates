<div class="rn-field-wrapper<?php echo esc_attr(($field['center_head'] == true) ? ' rn-center' : ''); ?>"<?php echo isset( $field[ 'width' ] ) ? ' style="width:' . esc_attr( $field[ 'width' ] ) . ';"' : ''; ?><?php echo (isset($field['dyn_field_id'])) ? ' data-dyn_field_id="' . esc_attr($field['dyn_field_id']) . '"' : ''; ?>>
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-box-attributes', $box_attributes, $field))); ?>>                           

        <?php
        if (isset($field['off_value'])) {
            $off_text = 'Off';
            if (isset($field['off_text'])) {
                $off_text = $field['off_text'];
            }
            ?>
            <label class="rn-opt-set opt-off<?php echo esc_attr((count($selected) <= 0) ? ' active' : ''); ?>"><span><?php echo esc_html($off_text); ?></span></label>
            <?php
        }


        $index = 0;
        foreach ($options as $key => $option) {
            if (!isset($field['css_class'])) {
                $field['css_class'] = array();
            }
            $attributes = array();
            $attributes['type'] = 'hidden';
            $attributes['data-optbtn'] = 'op' . $index;
            $attributes['data-opt-id'] = $field['id'] . $index;
            $attributes['data-opt-name'] = $field['in_prefix'] . $field['id'] . $field['in_postfix'] . '[' . $index . ']';
            $attributes['value'] = $key;
            $attributes['id'] = $field['id'] . $index;
            $attributes['name'] = $field['in_prefix'] . $field['id'] . $field['in_postfix'] . '[' . $index . ']';
            $is_selected = ReonUtil::s_c_r_d_check($key, $selected);

            $attributes['data-opt-active'] = $is_selected;
            if ($is_selected != true) {
                unset($attributes['value']);
                unset($attributes['id']);
                unset($attributes['name']);
            }
            $field['item_index'] = $index;
            ?>
            <label class="rn-opt-set opt-on <?php echo esc_attr(($is_selected == true) ? ' active' : ''); ?>" data-optbtn="<?php echo esc_attr('op' . $index); ?>" data-value="<?php echo esc_html($key); ?>"><span><?php echo esc_html($option); ?></span></label>
            <input <?php echo  wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?> /><?php
            $index++;
        }


        unset($field['item_index']);
        $attrs = array(
            'type' => 'hidden',
            'class' => 'rn-opt-empty rn-opt-multi',
            'data-opt-name' => $field['in_prefix'] . $field['id'] . $field['in_postfix'],
            'value' => '',
            'data-value' => '',
        );

        if (isset($field['off_value'])) {
            $attrs['data-value'] = $field['off_value'];
        }

        if (count($selected) <= 0) {
            $attrs['name'] = $field['in_prefix'] . $field['id'] . $field['in_postfix'];
            if (isset($field['off_value'])) {
                $attrs['value'] = $field['off_value'];
            }
        }
        ?>
        <input <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attrs, $field))); ?> />
    </div><?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
</div>
