<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cart_Fees_Panel_Conditions' ) ) {

    class WTARS_Shipped_Admin_Cart_Fees_Panel_Conditions {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/cart-fees/get-panels', array( $this, 'get_panel' ), 50, 2 );
        }

        public function get_panel( $in_fields, $args ) {

            $args[ 'module' ] = 'cart-fees';
            $args[ 'sub_module' ] = 'fees';

            $in_fields[] = array(
                'id' => 'fee_conditions',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => true,
                'last' => true,
                'fields' => apply_filters( 'wtars_shipped_admin/cart-fees/get-panel-conditions-fields', array(), $args ),
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Cart_Fees_Panel_Conditions();
}