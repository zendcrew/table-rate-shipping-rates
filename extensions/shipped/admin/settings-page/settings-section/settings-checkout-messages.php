<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Checkout_Messages_Settings' ) ) {

    class WTARS_Shipped_Admin_Checkout_Messages_Settings {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-settings-section-fields', array( $this, 'get_panel' ), 30, 2 );
        }

        public function get_panel( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'last' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'field_css_class' => array( 'shipped_setting_panel' ),
                'merge_fields' => false,
                'fields' => $this->get_fields( array() ),
            );

            return $in_fields;
        }

        private function get_fields( $in_fields ) {

            $in_fields[] = array(
                'id' => 'any_id_shipping_rates',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Cart Notifications Settings', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'Use these settings to control cart notification basic settings', 'table-rate-shipping-rates' ),
            );

            $in_fields[] = array(
                'id' => 'checkout_message_settings',
                'type' => 'columns-field',
                'columns' => 1,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'show_on_cart',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Enables Cart Notifications on cart page', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Enable On "Cart" Page', 'table-rate-shipping-rates' ),
                        'default' => array( 'yes' ),
                        'options' => array(
                            'no' => esc_html__( 'No', 'table-rate-shipping-rates' ),
                            'yes' => esc_html__( 'Yes', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Checkout_Messages_Settings();
}