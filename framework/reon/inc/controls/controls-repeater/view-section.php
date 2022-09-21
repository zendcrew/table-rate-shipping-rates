<div class="rn-repeater-section rn-repeater-pfx">
    <?php if (isset($template_args['section_type_id'])) {
        ?>
        <input type="hidden" data-repeater-name="<?php echo esc_attr($template_args['section_type_id']); ?>" value="<?php echo esc_attr($template['id']); ?>" />
        <?php
    }
    include 'view-section-head.php';
    include 'view-section-body.php';
    ?>
</div>