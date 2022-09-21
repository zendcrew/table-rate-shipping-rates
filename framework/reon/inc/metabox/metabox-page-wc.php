<?php
add_action('reon/render-metabox-wc', 'reon_metabox_wc', 10, 2);
if (!function_exists('reon_metabox_wc')) {

    function reon_metabox_wc($product_metabox, $product_id) {
        ?>
        <div id="<?php echo 'rn_' . esc_attr($product_metabox['id']); ?>" class="rn-container rn-metabox-wc panel woocommerce_options_panel" data-repeater-pfx="<?php echo esc_attr($product_metabox['id']); ?>">
            <input type="hidden" name="<?php echo esc_attr($product_metabox['id']); ?>_pid" value="<?php echo esc_attr($product_id); ?>" />
            <input type="hidden" name="reon_metabox_wc_id" value="<?php echo esc_attr($product_metabox['id']); ?>" />
            <?php wp_nonce_field($product_metabox['nonce_path'], 'reon_product_meta' . $product_metabox['id']); ?>
            <input type="hidden" class="reon_metabox_value" name="reon_metabox_<?php echo esc_attr($product_metabox['id']); ?>_value" value="" />
            <table class="rn-metabox-wc-inner">  
                <tbody class="rn-ui-app">
                    <?php
                    $fields = $product_metabox['fields'];
                    if (isset($product_metabox['fields_head_width'])) {
                        for ($i = 0; $i < count($fields); $i++) {
                            $fields[$i]['head_width'] = $product_metabox['fields_head_width'];
                        }
                    }
                    do_action('reon/render-metabox-wc-fields', $fields, $product_metabox['id'], $product_id);
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

