<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'ReonAjax' ) ) {

    class ReonAjax {

        public static function init() {
            self::add_action( 'rn_get_data_list_ajax', array( new self(), 'get_data_list_ajax' ) );
        }

        public static function add_action( $action_name, $function, $add_non_priv = false ) {
            add_action( 'wp_ajax_' . $action_name, $function );
            if ( $add_non_priv === true ) {
                add_action( 'wp_ajax_nopriv_' . $action_name, $function );
            }
        }

        public static function get_data_list_ajax() {

            $data_args = array(
                'source' => sanitize_text_field( $_POST[ 'ajaxsource' ] ),
                'pagesize' => 50,
            );

            if ( isset( $_POST[ 'term' ] ) && sanitize_text_field( $_POST[ 'term' ] ) != '' ) {
                $data_args[ 'search_term' ] = sanitize_text_field( $_POST[ 'term' ] );
            }

            if ( isset( $_POST[ 'pagesize' ] ) && sanitize_text_field( $_POST[ 'pagesize' ] ) != '' ) {
                $data_args[ 'pagesize' ] = sanitize_text_field( $_POST[ 'pagesize' ] );
            }

            if ( isset( $_POST[ 'ajaxsource_show_value' ] ) && sanitize_text_field( $_POST[ 'ajaxsource_show_value' ] ) != '' ) {
                $data_args[ 'show_value' ] = sanitize_text_field( $_POST[ 'ajaxsource_show_value' ] ) == 'true';
            }

            if ( isset( $_POST[ 'ajaxsource_value_col' ] ) && sanitize_text_field( $_POST[ 'ajaxsource_value_col' ] ) != '' ) {
                $data_args[ 'value_col' ] = sanitize_text_field( $_POST[ 'ajaxsource_value_col' ] );
            }

            if ( isset( $_POST[ 'ajaxsource_value_col_pre' ] ) && sanitize_text_field( $_POST[ 'ajaxsource_value_col_pre' ] ) != '' ) {
                $data_args[ 'value_col_pre' ] = sanitize_text_field( $_POST[ 'ajaxsource_value_col_pre' ] );
            }

            $src_dt = ReonApi::get_data_list( $data_args, array() );

            $disabled_list = array();

            if ( isset( $_POST[ 'ajaxsource_disabled_filter' ] ) && sanitize_text_field( $_POST[ 'ajaxsource_disabled_filter' ] ) != '' ) {

                $list_filter = sanitize_text_field( $_POST[ 'ajaxsource_disabled_filter' ] );

                $disabled_list = apply_filters( $list_filter, $disabled_list, $src_dt );
            }

            $drop_list = array( 'results' => array() );

            foreach ( $src_dt as $key => $value ) {

                $list_option = array(
                    'id' => esc_attr( $key ),
                    'text' => esc_html( $value ),
                );

                if ( in_array( $key, $disabled_list ) ) {

                    $list_option[ 'disabled' ] = true;
                }

                $drop_list[ 'results' ][] = $list_option;
            }


            self::render_json( $drop_list );
        }

        public static function render_json( $output ) {

            ob_get_flush();
            ob_end_clean();
            ob_start();

            header( "Access-Control-Allow-Origin: *" );
            header( "Content-Type: application/json; charset=UTF-8" );

            echo wp_json_encode( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            echo ob_get_clean();
            wp_die();
        }

    }

}
