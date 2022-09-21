<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Width' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Width {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 50, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-widths', array( $this, 'get_types' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'widths' ] = sprintf( esc_html__( '%s Per Width', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }

        public function get_types( $in_types, $args ) {

            $width_unit = get_option( 'woocommerce_dimension_unit' );

            $in_types[ 'prem_14' ] = sprintf( esc_html__( '%s per width (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $width_unit );
            $in_types[ 'prem_15' ] = sprintf( esc_html__( '%s x width (%s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $width_unit );

            return $in_types;
        }

    }

    new WTARS_Shipped_Admin_Cost_Type_Width();
}
