<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Order_Items' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Order_Items {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 20, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-order_items', array( $this, 'get_types' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'order_items' ] = sprintf( esc_html__( '%s Per Items', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }

        public function get_types( $in_types, $args ) {

            $in_types[ 'prem_5' ] = sprintf( esc_html__( '%s per line item (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );
            $in_types[ 'prem_6' ] = sprintf( esc_html__( '%s per item quantity (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );
            $in_types[ 'prem_7' ] = sprintf( esc_html__( '%s per product (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );
            $in_types[ 'prem_8' ] = sprintf( esc_html__( '%s per variation (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );
            $in_types[ 'prem_9' ] = sprintf( esc_html__( 'Percentage %s per item (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_text' ] );

            return $in_types;
        }

    }

    new WTARS_Shipped_Admin_Cost_Type_Order_Items();
}