<div class="rn-field-wrapper<?php echo esc_attr(($field['center_head'] == true) ? ' rn-center' : ''); ?>"<?php echo (isset($field['dyn_field_id'])) ? ' data-dyn_field_id="' . esc_attr($field['dyn_field_id']) . '"' : ''; ?>>
    <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-box-attributes', $box_attributes, $field))); ?>>         
        <?php
        if (isset($field['label'])) {
            $label = $field['label'];
            $label['single'] = true;
            do_action('reon/render-control-label', $label);
        }
        ?>
        <label class="rn-field rn-spectrum rn-no-border">            
            <input <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?> />
        </label>  
        <?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
    </div>        
</div>