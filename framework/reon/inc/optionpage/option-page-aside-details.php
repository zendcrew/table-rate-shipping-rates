<?php
$option_page_display = $page['display'];
$styles = array();
$a_styles = array();
if (isset($option_page_display['styles']['bg_image']) && $option_page_display['styles']['bg_image'] != '') {
    $styles['background-image'] = "url('" . esc_url($option_page_display['styles']['bg_image']) . "')";
}
if (isset($option_page_display['styles']['bg_color']) && $option_page_display['styles']['bg_color'] != '') {
    $styles['background-color'] = $option_page_display['styles']['bg_color'];
    $a_styles['color'] = $option_page_display['styles']['bg_color'];
}
if (isset($option_page_display['styles']['color']) && $option_page_display['styles']['color'] != '') {
    $styles['color'] = $option_page_display['styles']['color'];
}
if (isset($option_page_display['styles']['height']) && $option_page_display['styles']['height'] != '') {
    $styles['height'] = $option_page_display['styles']['height'];
}
?>
<div class="rn-option-page-aside-details" style="<?php echo ReonUtil::array_to_styles($styles);// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
    <div class="rn-option-page-aside-details-version"><?php echo esc_html($option_page_display['version']); ?></div>
    <div class="rn-option-page-aside-details-logo"><img src="<?php echo esc_url($option_page_display['image']); ?>" alt="<?php echo esc_attr($option_page_display['title']); ?>" /></div>
    <div class="rn-option-page-aside-details-title"><?php echo esc_html($option_page_display['title']); ?></div>
    <hr />
    <div class="rn-option-page-aside-details-subversion"><?php echo esc_html($option_page_display['sub_title']); ?></div>
</div>
<div class="rn-option-page-aside-details-after"><i class="fa fa-caret-down" style="<?php echo ReonUtil::array_to_styles($a_styles);// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></i></div>