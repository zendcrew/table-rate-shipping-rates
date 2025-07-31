<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?><div <?php echo wp_kses_post(ReonUtil::array_to_attributes($wrapper_attr)); ?>>
    <?php
    if ($show_box == true) {
        ?>
        <div <?php echo wp_kses_post(ReonUtil::array_to_attributes(apply_filters('reon/control-box-attributes', $box_attributes, $field))); ?>>
            <?php echo wp_kses($text, ReonUtil::get_allow_html()); ?>
        </div>
        <?php
    } else {
        echo wp_kses($text, ReonUtil::get_allow_html());
    }
    ?>
    <?php do_action('reon/render-control-tooltip', $field['tooltip']); ?>
</div>