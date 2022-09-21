<?php
if ( isset( $page[ 'enable_section_title' ] ) && $page[ 'enable_section_title' ] ) {

    $section_title = apply_filters( 'reon/get-option-page-' . $page[ 'option_name' ] . '-section-title', '', $page[ 'tab' ] );

    if ( !empty( $section_title ) ) {
        ?>
        <h4 class="rn-option-page-section-title"><?php echo esc_html( $section_title ); ?></h4>
        <?php
    }
}

