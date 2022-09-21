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
        <input <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-attributes', $attributes, $field))); ?> />
        <?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
    </div>

</div>