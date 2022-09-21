<?php
add_action('reon/render-metabox', 'reon_metabox', 10, 2);
if (!function_exists('reon_metabox')) {

    function reon_metabox($post_metabox, $post_id) {
        ?>
        <div class="rn-container rn-metabox" data-repeater-pfx="<?php echo esc_attr($post_metabox['id']); ?>">
            <input type="hidden" name="<?php echo esc_attr($post_metabox['id']); ?>_pid" value="<?php echo esc_attr($post_id); ?>" />
            <input type="hidden" name="reon_metabox_id" value="<?php echo esc_attr($post_metabox['id']); ?>" />
            <?php wp_nonce_field($post_metabox['nonce_path'], 'reon_post_meta' . $post_metabox['id']); ?>
            <input type="hidden" class="reon_metabox_value" name="reon_metabox_<?php echo esc_attr($post_metabox['id']); ?>_value" value="" />        
            <table class="rn-metabox-inner">
                <tbody class="rn-ui-app">
                    <?php
                    $fields = $post_metabox['fields'];
                    if (isset($post_metabox['fields_head_width'])) {
                        for ($i = 0; $i < count($fields); $i++) {
                            $fields[$i]['head_width'] = $post_metabox['fields_head_width'];
                        }
                    }
                    do_action('reon/render-metabox-fields', $fields, $post_metabox['id'], $post_id);
                    ?>
                </tbody>
            </table>
        </div>
        <div class="rn-dynamic-contents" style="display:none; visibility: hidden;">
            <?php echo ReonUI::get_dynamic_contents();// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <?php
    }

}