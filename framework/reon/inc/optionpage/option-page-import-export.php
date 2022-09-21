<?php
$import_export = $page['import_export'];
$section_styles = array();
if (isset($page['default_min_height'])) {
    $section_styles['min-height'] = $page['default_min_height'];
}
if (isset($import_export['min_height'])) {
    $section_styles['min-height'] = $import_export['min_height'];
}

if (!isset($page['group'])||$page['group'] == 0) {
    ?>
    <div id="rn_import_export" class="rn-option-page-group"<?php echo (count($section_styles) > 0) ? ' style="' . ReonUtil::array_to_styles($section_styles) . '"' : '';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
        <table class="rn-option-page-group-inner rn-ui-app">
            <tr class="rn-ui-section rn-ui-big-section">
                <td class="rn-ui-block" colspan="2">
                    <div class="rn-ui-head">
                        <h6 class="rn-ui-head-title"><?php echo esc_attr($import_export['import']['title']); ?></h6>
                        <p class="rn-ui-head-desc">
                            <?php echo wp_kses_post($import_export['import']['desc']); ?>
                        </p>
                    </div>
                    <div class="rn-field-wrapper rn-import-export-wrapper">
                        <a href="#" class="rn-import-from-data rn-btn"><?php echo esc_html($import_export['import']['data_button_text']); ?></a>
                        <a href="#" class="rn-import-from-url rn-btn"><?php echo esc_html($import_export['import']['url_button_text']); ?></a>                    
                    </div> 
                    <div class="rn-clear"></div>
                    <div class="rn-field-wrapper rn-import-export-wrapper rn-url-import-panel" style="width:100%; display: none">
                        <p class="rn-field-desc">
                            <?php echo wp_kses_post($import_export['import']['url_textbox_desc']); ?>
                        </p>
                        <textarea id="rn_import_field_url" placeholder="<?php echo esc_attr($import_export['import']['url_textbox_hint']); ?>" style="width:100%;height:50px;" cols="80" rows="5"></textarea>                                        
                    </div>
                    <div class="rn-field-wrapper rn-import-export-wrapper rn-data-import-panel" style="width:100%; display: none">
                        <p class="rn-field-desc">
                            <?php echo wp_kses_post($import_export['import']['data_textbox_desc']); ?>
                        </p>
                        <textarea id="rn_import_field_data" placeholder="<?php echo esc_attr($import_export['import']['data_textbox_hint']); ?>" style="width:100%;height:150px;" cols="80" rows="5"></textarea>                                        
                    </div>
                    <div class="rn-clear"></div>
                    <div class="rn-field-wrapper rn-data-importnow-panel" style="width:100%; display: none;">
                        <a href="#" class="rn-btn rn-btn-primary rn-import-btn" data-import-type="data"><?php echo esc_html($import_export['import']['import_button_text']); ?></a>
                        <p class="rn-import-export-warn"><?php echo wp_kses_post($import_export['import']['warn_text']); ?></p>
                    </div> 
                </td><!-- .rn-ui-block -->
            </tr><!-- .rn-ui-section -->
            <tr class="rn-ui-section rn-ui-big-section rn-last">
                <td class="rn-ui-block" colspan="2">
                    <div class="rn-ui-head">
                        <h6 class="rn-ui-head-title"><?php echo esc_html($import_export['export']['title']); ?></h6>
                        <p class="rn-ui-head-desc">
                            <?php echo esc_html($import_export['export']['desc']); ?>
                        </p>
                    </div>
                    <div class="rn-field-wrapper rn-import-export-wrapper">
                        <a href="<?php echo esc_url($page['export_url']); ?>" class="rn-export-download-data rn-btn rn-btn-primary"><?php echo esc_html($import_export['export']['download_button_text']); ?></a>
                        <a href="#" class="rn-export-url rn-btn"><?php echo esc_html($import_export['export']['url_button_text']); ?></a>
                        <a href="#" class="rn-export-data rn-btn"><?php echo esc_html($import_export['export']['data_button_text']); ?></a>
                    </div> 
                    <div class="rn-clear"></div>
                    <div class="rn-field-wrapper rn-import-export-wrapper rn-url-export-panel" style="width:100%; display: none;">
                        <p class="rn-field-desc">
                            <?php echo wp_kses_post($import_export['export']['url_textbox_desc']); ?>
                        </p>
                        <textarea id="rn_export_url" autofocus="autofocus" onfocus="this.select()" style="width:100%;height:50px;" cols="80" rows="5"><?php echo esc_url($page['export_url']); ?></textarea>                                        
                    </div>
                    <div class="rn-field-wrapper rn-import-export-wrapper rn-data-export-panel" style="width:100%; display: none;">
                        <p class="rn-field-desc">
                            <?php echo wp_kses_post($import_export['export']['data_textbox_desc']); ?>
                        </p>
                        <textarea id="rn_export_data" autofocus="autofocus" onfocus="this.select()" style="width:100%;height:150px;" cols="80" rows="5" data-option-url="<?php echo esc_url($page['export_url']); ?>"></textarea>                                        
                    </div>
                    <div class="rn-clear"></div>
                </td><!-- .rn-ui-block -->
            </tr><!-- .rn-ui-section -->
        </table>
    </div><!-- .rn-option-page-group -->
<?php
}