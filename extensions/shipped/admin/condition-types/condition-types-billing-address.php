<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Billing_Address' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_Billing_Address {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 50, 2 );
            add_filter( 'wtars_shipped_admin/get-billing_address-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'billing_address' ] = esc_html__( 'Billing Address', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_1' ] = esc_html__( 'Billing Continents (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_2' ] = esc_html__( 'Billing Countries (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_3' ] = esc_html__( 'Billing States (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_4' ] = esc_html__( 'Billing Cities (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_5' ] = esc_html__( 'Billing Postcode / ZIP (Premium)', 'table-rate-shipping-rates' );

            return $in_list;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Billing_Address();
}