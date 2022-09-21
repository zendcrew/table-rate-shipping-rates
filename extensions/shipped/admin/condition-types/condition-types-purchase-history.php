<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Purchase_History' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_Purchase_History {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 160, 2 );
            add_filter( 'wtars_shipped_admin/get-purchase_history-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'purchase_history' ] = esc_html__( 'Purchase History', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_1' ] = esc_html__( 'Purchased Products (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_2' ] = esc_html__( 'Purchased Variations (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_3' ] = esc_html__( 'Purchased Categories (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_4' ] = esc_html__( 'Purchased Tags (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_5' ] = esc_html__( 'Purchased Attributes (Premium)', 'table-rate-shipping-rates' );

            return $in_list;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Purchase_History();
}

