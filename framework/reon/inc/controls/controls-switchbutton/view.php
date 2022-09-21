<div class="rn-field-wrapper<?php echo esc_attr(($field['center_head'] == true) ? ' rn-center' : ''); ?>"<?php echo wp_kses_post($btnset_with); ?><?php echo (isset($field['dyn_field_id'])) ? ' data-dyn_field_id="' . esc_attr($field['dyn_field_id']) . '"' : ''; ?>>
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-box-attributes', $box_attributes, $field))); ?>>
        <label class="rn-opt-set opt-off<?php echo ($is_on == false) ? ' active' : ''; ?>" data-value="<?php echo esc_attr($field['off_value']); ?>"><span><?php echo esc_html($field['off_text']); ?></span></label>
        <label class="rn-opt-set opt-on<?php echo ($is_on == true) ? ' active' : ''; ?>" data-value="<?php echo esc_attr($field['on_value']); ?>"><span><?php echo esc_html($field['on_text']); ?></span></label>
        <input <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?> />
    </div><?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
</div>
