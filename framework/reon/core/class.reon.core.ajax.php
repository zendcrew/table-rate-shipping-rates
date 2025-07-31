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

            if ( !self::is_ajax_action() ) {

                return;
            }

            add_action( 'wp_ajax_' . $action_name, $function );
            
            if ( $add_non_priv === true ) {
                
                add_action( 'wp_ajax_nopriv_' . $action_name, $function );
            }
        }

        public static function get_data_list_ajax() {

            $drop_list = array( 'results' => array() );

            $args = self::get_data_list_ajax_args();

            if ( !isset( $args[ 'source' ] ) ) {

                self::render_json( $drop_list );
            }

            $data_args = array(
                'source' => $args[ 'source' ],
                'pagesize' => 50,
            );


            if ( isset( $args[ 'search_term' ] ) && !empty( $args[ 'search_term' ] ) ) {

                $data_args[ 'search_term' ] = $args[ 'search_term' ];
            }

            if ( isset( $args[ 'pagesize' ] ) && !empty( $args[ 'pagesize' ] ) ) {

                $data_args[ 'pagesize' ] = $args[ 'pagesize' ];
            }

            if ( isset( $args[ 'show_value' ] ) ) {

                $data_args[ 'show_value' ] = ($args[ 'show_value' ] == 'true');
            }

            if ( isset( $args[ 'value_col' ] ) && !empty( $args[ 'value_col' ] ) ) {

                $data_args[ 'value_col' ] = $args[ 'value_col' ];
            }

            if ( isset( $args[ 'value_col_pre' ] ) && !empty( $args[ 'value_col_pre' ] ) ) {

                $data_args[ 'value_col_pre' ] = $args[ 'value_col_pre' ];
            }

            $src_dt = ReonApi::get_data_list( $data_args, array() );

            $disabled_list = array();

            $list_filter = '';

            if ( isset( $_POST[ 'ajaxsource_disabled_filter' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing

                $list_filter = self::sanitize_value( wp_unslash( $_POST[ 'ajaxsource_disabled_filter' ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing
            }

            if ( !empty( $list_filter ) ) {

                $disabled_list = apply_filters( $list_filter, $disabled_list, $src_dt );
            }

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

            echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            wp_die();
        }

        private static function get_data_list_ajax_args() {

            // phpcs:disable WordPress.Security.NonceVerification.Missing

            if ( !isset( $_POST[ 'ajaxsource' ] ) ) {

                return array();
            }

            $args = array(
                'source' => self::sanitize_value( wp_unslash( $_POST[ 'ajaxsource' ] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            );

            if ( isset( $_POST[ 'term' ] ) ) {

                $args[ 'search_term' ] = sanitize_text_field( wp_unslash( $_POST[ 'term' ] ) );
            }

            if ( isset( $_POST[ 'pagesize' ] ) ) {

                $args[ 'pagesize' ] = sanitize_key( wp_unslash( $_POST[ 'pagesize' ] ) );
            }

            if ( isset( $_POST[ 'ajaxsource_show_value' ] ) ) {

                $args[ 'show_value' ] = sanitize_key( wp_unslash( $_POST[ 'ajaxsource_show_value' ] ) );
            }

            if ( isset( $_POST[ 'ajaxsource_value_col' ] ) ) {

                $args[ 'value_col' ] = sanitize_key( wp_unslash( $_POST[ 'ajaxsource_value_col' ] ) );
            }

            if ( isset( $_POST[ 'ajaxsource_value_col_pre' ] ) ) {

                $args[ 'value_col_pre' ] = self::sanitize_value( wp_unslash( $_POST[ 'ajaxsource_value_col_pre' ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            }

            // phpcs:enable WordPress.Security.NonceVerification.Missing
            
            return $args;
        }

        private static function is_ajax_action() {

            if ( isset( $_GET[ 'rn-admin-options' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

                return true;
            }

            return false;
        }

        private static function sanitize_value( $key ) {

            return preg_replace( '/[^a-zA-Z0-9_:#\-\/]/', '', $key );
        }

    }

}
