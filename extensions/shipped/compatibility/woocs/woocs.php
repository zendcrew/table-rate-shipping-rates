<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_WOOCS' ) && class_exists( 'WOOCS' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    require_once 'woocs-cart.php';
    require_once 'woocs-package.php';
    require_once 'woocs-fees.php';

    class WTARS_Shipped_WOOCS {

        private static $instance = null;

        public static function get_instance() {

            if ( null == self::$instance ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public function convert_amount_list( $amount_list ) {

            if ( !is_array( $amount_list ) ) {

                return $this->convert_amount( $amount_list );
            }

            $amounts = array();

            foreach ( $amount_list as $key => $amount ) {

                $amounts[ $key ] = $this->convert_amount( $amount );
            }

            return $amounts;
        }

        public function revert_amount_list( $amount_list ) {

            if ( !is_array( $amount_list ) ) {

                return $this->revert_amount( $amount_list );
            }

            $amounts = array();

            foreach ( $amount_list as $key => $amount ) {

                $amounts[ $key ] = $this->revert_amount( $amount );
            }

            return $amounts;
        }

        public function convert_amount( $amount ) {

            return apply_filters( 'woocs_convert_price', $amount );
        }

        public function revert_amount( $amount ) {

            return apply_filters( 'woocs_back_convert_price', $amount );
        }

    }

}
