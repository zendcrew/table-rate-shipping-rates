<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Checkout_Messages_Panel_Parameters' ) ) {

    class WTARS_Shipped_Admin_Checkout_Messages_Panel_Parameters {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/checkout-messages/get-panels', array( $this, 'get_panel' ), 20, 2 );

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                add_filter( 'wtars_shipped_admin/checkout-messages/get-panel-parameters-fields', array( $this, 'get_panel_fields' ), 10, 2 );
            }
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
                'fields' => apply_filters( 'wtars_shipped_admin/checkout-messages/get-panel-parameters-fields', array(), $args ),
            );

            return $in_fields;
        }

        public function get_panel_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'last' => true,
                'css_class' => 'shipped_calcs_title',
                'fields' => array(
                    array(
                        'id' => 'any_id',
                        'type' => 'paneltitle',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__( 'Message Parameters', 'table-rate-shipping-rates' ),
                        'desc' => esc_html__( 'Specify what parameters needs to be included in the message', 'table-rate-shipping-rates' ),
                    ),
                    array(
                        'id' => 'is_any',
                        'type' => 'textblock',
                        'show_box' => false,
                        'full_width' => true,
                        'center_head' => true,
                        'text' => WTARS_Shipped_Admin_Page::get_premium_messages(),
                        'field_css_class' => array( 'shipped-title-message' ),
                        'width' => '100%',
                        'box_width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Checkout_Messages_Panel_Parameters();
}
