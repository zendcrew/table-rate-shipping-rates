<div class="rn-option-page-tool">
    <?php include 'option-page-content-header-title.php'; ?>
    <?php
    if (count($page['page_links']) > 0) {
        ?>
        <ul class="rn-option-page-links">            
            <?php
            foreach ($page['page_links'] as $page_link) {
                if ($page_link['show_in'] != 'admin_bar') {
                    ?>
            <li><a id="<?php echo esc_attr($page_link['id']); ?>"<?php echo (isset($page_link['target']) && $page_link['target'] != '') ? ' target="' . esc_attr($page_link['target']) . '"' : ''; ?> href="<?php echo esc_url($page_link['href']); ?>"><i class="<?php echo esc_attr($page_link['icon']); ?>"></i><?php echo wp_kses_post($page_link['title']); ?></a></li>
                    <?php
                }
            }
            ?>                   
        </ul><!-- .rn-option-page-links -->
        <?php
    }

    if (count($page['header_buttons']) > 0) {
        ?>
        <div class="rn-option-page-buttons">
            <div class="rn-ajax-icon"></div>
            <?php
            if (isset($page['footer_buttons']['reset_all_text'])) {
                ?><a href="#" class="rn-reset-btn rn-reset-all"><?php echo esc_html($page['footer_buttons']['reset_all_text']) ?></a><?php
            }
            if (isset($page['footer_buttons']['reset_section_text']) && $is_multi_section == true) {
                ?><a href="#" class="rn-reset-btn rn-reset-section rn-btn-sep"><?php echo esc_html($page['footer_buttons']['reset_section_text']) ?></a><?php
            }
            if (isset($page['footer_buttons']['save_all_text'])) {
                ?><a href="#" class="rn-save-btn rn-save-all"><?php echo esc_html($page['footer_buttons']['save_all_text']) ?></a><?php
                }
                if (isset($page['footer_buttons']['save_section_text']) && $is_multi_section == true) {
                    ?><a href="#" class="rn-save-btn rn-save-section"><?php echo esc_html($page['footer_buttons']['save_section_text']) ?></a><?php
                }
                ?>
        </div><!-- .rn-option-page-buttons -->
        <?php
    }
    ?>
    <div class="rn-clear"></div>
</div><!-- .rn-option-page-tool -->