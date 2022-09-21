<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Height' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Height {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 60, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-heights', array( $this, 'get_types' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'heights' ] = sprintf( esc_html__( '%s Per Height', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }

        public function get_types( $in_types, $args ) {

            $height_unit = get_option( 'woocommerce_dimension_unit' );

            $in_types[ 'prem_16' ] = sprintf( esc_html__( '%s per height (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $height_unit );
            $in_types[ 'prem_17' ] = sprintf( esc_html__( '%s x height (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $height_unit );

            return $in_types;
        }

    }

    new WTARS_Shipped_Admin_Cost_Type_Height();
}
