<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( isset( $page[ 'enable_section_title' ] ) && $page[ 'enable_section_title' ] ) {

    $section_title = '';

    if ( isset( $page[ 'header_title' ] ) ) {

        $section_title = $page[ 'header_title' ];
    } else if ( (!isset( $page[ 'group' ] ) || $page[ 'group' ] == 0) && isset( $page[ 'import_export' ][ 'header_title' ] ) ) {

        $section_title = $page[ 'import_export' ][ 'header_title' ];
    }

    if ( !empty( $section_title ) ) {
        ?>
        <h4 class="rn-option-page-section-title"><?php echo esc_html( $section_title ); ?></h4>
        <?php
    }
}

