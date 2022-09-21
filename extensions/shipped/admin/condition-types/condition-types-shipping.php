<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Shipping' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_Shipping {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 20, 2 );

            add_filter( 'wtars_shipped_admin/get-shipping-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            if ( !$this->can_include( $args ) ) {
                return $in_groups;
            }

            $in_groups[ 'shipping' ] = esc_html__( 'Shipping', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_1' ] = esc_html__( 'Needs Shipping (Premium)', 'table-rate-shipping-rates' );

            return $in_list;
        }

        private function can_include( $args ) {

            if ( 'cart-fees' == $args[ 'module' ] ) {
                return false;
            }

            if ( 'shipping-rates' == $args[ 'module' ] ) {
                return false;
            }

            if ( 'method' == $args[ 'module' ] ) {
                return false;
            }

            return true;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Shipping();
}
