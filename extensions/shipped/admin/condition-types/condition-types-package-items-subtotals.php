<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Package_Items_Subtotals' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_Package_Items_Subtotals {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 100, 2 );
            add_filter( 'wtars_shipped_admin/get-package_items_subtotals-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'package_items_subtotals' ] = esc_html__( 'Package Item Subtotals', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            $in_list[ 'prem_1' ] = esc_html__( 'Products Subtotal (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_2' ] = esc_html__( 'Variations Subtotal (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_3' ] = esc_html__( 'Categories Subtotal (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_4' ] = esc_html__( 'Tags Subtotal (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_5' ] = esc_html__( 'Attributes Subtotal (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_6' ] = esc_html__( 'Tax Classes Subtotal (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_7' ] = esc_html__( 'Shipping Classes Subtotal (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_8' ] = esc_html__( 'Product Meta Fields Subtotal (Premium)', 'table-rate-shipping-rates' );

            return $in_list;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Package_Items_Subtotals();
}