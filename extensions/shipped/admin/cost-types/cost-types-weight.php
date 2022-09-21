<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Weight' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Weight {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 30, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-weights', array( $this, 'get_types' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'weights' ] = sprintf( esc_html__( '%s Per Weight', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }

        public function get_types( $in_types, $args ) {

            $weight_unit = get_option( 'woocommerce_weight_unit' );

            $in_types[ 'prem_10' ] = sprintf( esc_html__( '%s per weight (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $weight_unit );
            $in_types[ 'prem_11' ] = sprintf( esc_html__( '%s x weight (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $weight_unit );

            return $in_types;
        }

    }

    new WTARS_Shipped_Admin_Cost_Type_Weight();
}