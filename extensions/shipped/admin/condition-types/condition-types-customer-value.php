<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Customer_Value' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_Customer_Value {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 140, 2 );
            add_filter( 'wtars_shipped_admin/get-customers_value-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'customers_value' ] = esc_html__( 'Customer Values', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            $currency = get_woocommerce_currency_symbol( get_woocommerce_currency() );

            $in_list[ 'prem_1' ] = esc_html__( 'Coupons Used (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_2' ] = sprintf( esc_html__( 'Totals Spent (%s) (Premium)', 'table-rate-shipping-rates' ), $currency );
            $in_list[ 'prem_3' ] = esc_html__( 'Last Order Date (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_4' ] = sprintf( esc_html__( 'Last Order Totals (%s) (Premium)', 'table-rate-shipping-rates' ), $currency );
            $in_list[ 'prem_5' ] = sprintf( esc_html__( 'Average (%s) Per Order (Premium)', 'table-rate-shipping-rates' ), $currency );
            $in_list[ 'prem_6' ] = sprintf( esc_html__( 'Maximum (%s) Per Order (Premium)', 'table-rate-shipping-rates' ), $currency );
            $in_list[ 'prem_7' ] = sprintf( esc_html__( 'Minimum (%s) Per Order (Premium)', 'table-rate-shipping-rates' ), $currency );
            $in_list[ 'prem_8' ] = esc_html__( 'Number Of Orders (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_9' ] = esc_html__( 'Number Of Reviews (Premium)', 'table-rate-shipping-rates' );

            return $in_list;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Customer_Value();
}