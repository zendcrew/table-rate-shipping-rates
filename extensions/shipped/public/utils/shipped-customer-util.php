<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Customer_Util' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Customer_Util {

        public static function get_is_logged_in( $data ) {
            if ( !isset( $data[ 'wc' ][ 'cart' ][ 'customer' ][ 'id' ] ) ) {
                return false;
            }

            $user_id = $data[ 'wc' ][ 'cart' ][ 'customer' ][ 'id' ];

            return ($user_id > 0);
        }

        public static function get_user_email( $data ) {

            if ( !isset( $data[ 'wc' ][ 'cart' ][ 'customer' ][ 'email' ] ) ) {
                return '';
            }

            return $data[ 'wc' ][ 'cart' ][ 'customer' ][ 'email' ];
        }

    }

}
