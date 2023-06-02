<?php
add_action('reon/render-option-page', 'reon_optionpage', 10, 1);
if (!function_exists('reon_optionpage')) {

    function reon_optionpage($page) {
        $ajax_save_error = '';
        $ajax_reset_error = '';
        if (isset($page['ajax'])) {
            $ajax_msg = $page['ajax'];
            $ajax_save_error = isset($ajax_msg['save_error_msg']) ? $ajax_msg['save_error_msg'] : '';
            $ajax_reset_error = isset($ajax_msg['reset_error_msg']) ? $ajax_msg['reset_error_msg'] : '';
        }
        $no_display = ' rn-no-display';
        if (isset($page['display']['enabled']) && $page['display']['enabled'] == true) {
            $no_display = '';
        }
        $is_multi_section = false;
        if (count($page['sections']) > 1 || $page['import_export']['enable'] == true) {
            $is_multi_section = true;
        }
        if (isset($page['page_title']) && $page['page_title'] != '') {
            ?>
            <h1 class="wp-heading-inline"><?php echo esc_html($page['page_title']); ?></h1>
            <?php
        }
        ?>
            <div<?php echo isset($page['page_id'])? ' id="'. esc_attr($page['page_id']).'"':''; ?> class="rn-container rn-option-page <?php echo (isset($page['is_wc']) && $page['is_wc'] == true) ? 'rn_is_wc' : ''; ?>" data-repeater-pfx="<?php echo esc_attr($page['option_name']); ?>" data-ajax-save-error="<?php echo esc_attr($ajax_save_error); ?>" data-ajax-reset-error="<?php echo esc_attr($ajax_reset_error); ?>" data-option-name="<?php echo esc_attr($page['option_name']); ?>" style="width:<?php echo esc_attr($page['width']); ?>">
            <?php
            if ($is_multi_section == true) {
                ?>
                <div class="rn-option-page-aside<?php echo esc_attr($no_display); ?>" <?php echo (isset($page['aside_width']) && $page['aside_width'] != '') ? ' style="width:' . esc_attr($page['aside_width']) . '"' : ''; ?>>
                    <?php
                    if (isset($page['display']['enabled']) && $page['display']['enabled'] == true) {
                        include 'option-page-aside-details.php';
                    }
                    ?>
                    <?php include 'option-page-aside-menu.php'; ?>
                </div><!-- .rn-option-page-aside -->
                <?php
            }
            ?>
            <div class="rn-option-page-body<?php echo ($is_multi_section != true) ? " rn-no-aside" : ''; ?>"<?php echo (isset($page['aside_width']) && $page['aside_width'] != '') ? ' style="margin-left:' . esc_attr($page['aside_width']) . '"' : ''; ?>>
                <?php
                if (count($page['page_links']) > 0 || count($page['header_buttons']) > 0) {
                    include 'option-page-content-header.php';
                }
                $instance_id = '';
                if (isset($page['instance_id'])) {
                    $instance_id = $page['instance_id'];
                }
                ?>
                <?php if (!isset($page['is_wc']) || $page['is_wc'] == false) { ?><form class="" action="" method="post"><?php } ?>                
                    <div class="rn-option-page-groups">                    
                        <?php
                        wp_nonce_field($page['nonce_path'], 'option_page_' . $page['option_name']);
                        ?>
                        <input type="hidden" name="reon_instance_id" class="reon_instance_id" value="<?php echo esc_attr($instance_id); ?>" />
                        <?php
                        foreach ($page['sections'] as $section) {
                            if (count($section['fields']) > 0) {
                                $section_styles = array();
                                if (isset($page['default_min_height'])) {
                                    $section_styles['min-height'] = $page['default_min_height'];
                                }
                                if (isset($section['min_height'])) {
                                    $section_styles['min-height'] = $section['min_height'];
                                }


                                if (isset($page['group']) && $page['group'] == $section['group']) {
                                    ?>
                                    <div id="<?php echo esc_attr($section['id']); ?>" class="rn-option-page-group"<?php echo (count($section_styles) > 0) ? ' style="' . ReonUtil::array_to_styles($section_styles) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
                                        <table class="rn-option-page-group-inner">
                                            <tbody class="rn-ui-app">
                                    <?php
                                    $fields = $section['fields'];
                                    if (isset($page['fields_head_width'])) {
                                        for ($i = 0; $i < count($fields); $i++) {
                                            $fields[$i]['head_width'] = $page['fields_head_width'];
                                        }
                                    }
                                    do_action('reon/render-option-page-fields', $fields, $page['option_name'], $instance_id);
                                    ?>
                                            </tbody>
                                        </table>
                                    </div><!-- .rn-option-page-group -->                                
                                                <?php
                                            }
                                        }
                                    }
                                    if ($page['import_export']['enable'] == true) {
                                        include 'option-page-import-export.php';
                                    }
                                    ?>                         
                    </div><!-- .rn-option-page-groups -->
                        <?php if (!isset($page['is_wc']) || $page['is_wc'] == false) { ?></form><?php } ?>    
                        <?php
                        if (count($page['social_links']) > 0 || count($page['footer_buttons']) > 0) {
                            include 'option-page-content-footer.php';
                        }
                        ?>
                <div class="rn-ajax-overlay"></div>
            </div><!-- .rn-option-page-body -->
            <div class="rn-clear"></div>
        </div><!-- .rn-option-page -->
        <div class="rn-dynamic-contents" style="display:none; visibility: hidden;">
        <?php echo ReonUI::get_dynamic_contents();// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <?php
    }

}
