<div class="rn-option-page-tool rn-option-page-footer">
    <?php
    if (count($page['social_links']) > 0) {
        ?>
        <ul class="rn-option-page-links rn-social-links">
            <?php
            foreach ($page['social_links'] as $social_link) {
                ?>
                <li><a id="<?php echo esc_attr($social_link['id']); ?>"<?php echo (isset($social_link['target']) && $social_link['target'] != '') ? ' target="' . esc_attr($social_link['target']) . '"' : ''; ?> href="<?php echo esc_attr($social_link['href']); ?>" title="<?php echo esc_attr($social_link['title']); ?>"><i class="<?php echo esc_attr($social_link['icon']); ?>"></i></a></li>
                <?php
            }
            ?>      
        </ul><!-- .rn-option-page-links -->
        <?php
    }

    if (count($page['footer_buttons']) > 0) {
        ?>
        <div class="rn-option-page-buttons">
            <div class="rn-ajax-icon"></div>
            <?php
            if (isset($page['footer_buttons']['reset_all_text'])) {
                ?><a href="#" class="rn-reset-btn rn-reset-all"><?php echo esc_html($page['footer_buttons']['reset_all_text']) ?></a><?php
            }
            if (isset($page['footer_buttons']['reset_section_text'])&&$is_multi_section==true) {
                ?><a href="#" class="rn-reset-btn rn-reset-section rn-btn-sep"><?php echo esc_html($page['footer_buttons']['reset_section_text']) ?></a><?php
            }
            if (isset($page['footer_buttons']['save_all_text'])) {
                ?><a href="#" class="rn-save-btn rn-save-all"><?php echo esc_html($page['footer_buttons']['save_all_text']) ?></a><?php
                }
                if (isset($page['footer_buttons']['save_section_text'])&&$is_multi_section==true) {
                    ?><a href="#" class="rn-save-btn rn-save-section"><?php echo esc_html($page['footer_buttons']['save_section_text']) ?></a><?php
                }
                ?>
        </div><!-- .rn-option-page-buttons -->
        <?php
    }
    ?>    
    <div class="rn-clear"></div>
</div><!-- .rn-option-page-tool -->