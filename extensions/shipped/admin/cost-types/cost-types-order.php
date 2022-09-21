<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Order' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Order {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-order', array( $this, 'get_types' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'order' ] = sprintf( esc_html__( '%s Per Order', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }

        public function get_types( $in_types, $args ) {

            $in_types[ 'fixed_cost' ] = sprintf( esc_html__( 'Fixed %s', 'table-rate-shipping-rates' ), $args[ 'format_text' ] );

            return $in_types;
        }

    }

    new WTARS_Shipped_Admin_Cost_Type_Order();
}