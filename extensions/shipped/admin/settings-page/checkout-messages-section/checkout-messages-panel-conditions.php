<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Checkout_Messages_Panel_Conditions' ) ) {

    class WTARS_Shipped_Admin_Checkout_Messages_Panel_Conditions {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/checkout-messages/get-panels', array( $this, 'get_panel' ), 50, 2 );
        }

        public function get_panel( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'css_class' => 'shipped_panel_calcs',
                'field_css_class' => array( 'shipped_panel_calcs_field', 'shipped_mid_panel_calcs' ),
                'last' => true,
                'fields' => $this->get_inner_panel( array(), $args ),
            );

            return $in_fields;
        }

        private function get_inner_panel( $in_fields, $args ) {

            $args[ 'module' ] = 'checkout-messages';
            $args[ 'sub_module' ] = 'messages';

            $in_fields[] = array(
                'id' => 'message_conditions',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => true,
                'last' => true,
                'fields' => apply_filters( 'wtars_shipped_admin/checkout-messages/get-panel-conditions-fields', array(), $args ),
            );
            
            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Checkout_Messages_Panel_Conditions();
}
        
        