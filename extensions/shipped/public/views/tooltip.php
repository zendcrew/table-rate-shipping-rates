<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

?><span class="<?php echo esc_attr( WTARS_SHIPPED_METHOD_ID ) . '_tooltip dashicons dashicons-editor-help' ?>" title="<?php echo wc_sanitize_tooltip( $info['tooltip'] );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"></span>
