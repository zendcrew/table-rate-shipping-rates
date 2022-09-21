<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Shipping_Rates_Settings' ) ) {

    class WTARS_Shipped_Admin_Shipping_Rates_Settings {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-settings-section-fields', array( $this, 'get_panel' ), 10, 2 );
        }

        public function get_panel( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'last' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
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
                'title' => esc_html__( 'Shipping Rates Settings', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'Use these settings to control shipping rates basic settings', 'table-rate-shipping-rates' ),
            );

            $in_fields[] = array(
                'id' => 'shipping_rate_settings',
                'type' => 'columns-field',
                'columns' => 7,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'title',
                        'type' => 'textbox',
                        'input_type' => 'text',
                        'column_size' => 3,
                        'tooltip' => esc_html__( 'Controls the method title for reference purposes', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Method Title', 'table-rate-shipping-rates' ),
                        'default' => esc_html__( 'Table Rate Shipping & Fees', 'table-rate-shipping-rates' ),
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'tax_type',
                        'type' => 'select2',
                        'column_size' => 2,
                        'tooltip' => esc_html__( 'Determines whether or not to use tax rates applied to shipping only.', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Tax Rate Options', 'table-rate-shipping-rates' ),
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'default' => array( 'ship_rates' ),
                        'options' => $this->get_tax_options(),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'show_on_cart',
                        'type' => 'select2',
                        'column_size' => 2,
                        'tooltip' => esc_html__( 'Enables shipping rates on cart page', 'table-rate-shipping-rates' ),
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

        private function get_tax_options() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return array(
                    'no' => esc_html__( 'Not taxable', 'table-rate-shipping-rates' ),
                    'ship_rates' => esc_html__( 'Use shipping tax rates only', 'table-rate-shipping-rates' ),
                    'reg_rates' => esc_html__( 'Use regular tax rates', 'table-rate-shipping-rates' ),
                );
            }

            return array(
                'no' => esc_html__( 'Not taxable', 'table-rate-shipping-rates' ),
                'ship_rates' => esc_html__( 'Use shipping tax rates only', 'table-rate-shipping-rates' ),
            );
        }

    }

    new WTARS_Shipped_Admin_Shipping_Rates_Settings();
}