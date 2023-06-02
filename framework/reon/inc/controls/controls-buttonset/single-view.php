<div class="rn-field-wrapper<?php echo esc_attr(($field['center_head'] == true) ? ' rn-center' : ''); ?>"<?php echo isset( $field[ 'width' ] ) ? ' style="width:' . esc_attr( $field[ 'width' ] ) . ';"' : ''; ?>>
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-box-attributes', $box_attributes, $field))); ?>>
        <?php
        if (isset($field['off_value'])) {
            $off_text = 'Off';
            if (isset($field['off_text'])) {
                $off_text = $field['off_text'];
            }
            ?>
            <label class="rn-opt-set opt-off<?php echo esc_attr(($inp_value == $field['off_value']) ? ' active' : ''); ?>" data-value="<?php echo esc_attr($field['off_value']); ?>"><span><?php echo esc_html($off_text); ?></span></label>
            <?php
        }
        foreach ($options as $key => $value) {
            ?>
            <label class="rn-opt-set opt-on<?php echo esc_attr(($key == $inp_value) ? ' active' : ''); ?>" data-value="<?php echo esc_attr($key); ?>"><span><?php echo esc_html($value); ?></span></label>
                    <?php
                }
                ?><input <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?> />
    </div><?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
</div>