<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_WMC' ) && function_exists( 'wmc_get_price' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    require_once 'wmc-cart.php';
    require_once 'wmc-package.php';
    require_once 'wmc-fees.php';

    class WTARS_Shipped_WMC {

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

            return wmc_get_price( $amount );
        }

        public function revert_amount( $amount ) {

            return wmc_revert_price( $amount );
        }

    }

}