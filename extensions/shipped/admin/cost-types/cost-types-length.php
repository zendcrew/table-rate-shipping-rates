<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Length' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Length {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 40, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-lengths', array( $this, 'get_types' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'lengths' ] = sprintf( esc_html__( '%s Per Length', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }

        public function get_types( $in_types, $args ) {

            $length_unit = get_option( 'woocommerce_dimension_unit' );

            $in_types[ 'prem_12' ] = sprintf( esc_html__( '%s per length (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $length_unit );
            $in_types[ 'prem_13' ] = sprintf( esc_html__( '%s x length (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $length_unit );

            return $in_types;
        }

    }

    new WTARS_Shipped_Admin_Cost_Type_Length();
}