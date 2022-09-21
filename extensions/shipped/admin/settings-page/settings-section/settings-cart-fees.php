<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cart_Fees_Settings' ) ) {

    class WTARS_Shipped_Admin_Cart_Fees_Settings {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-settings-section-fields', array( $this, 'get_panel' ), 20, 2 );
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
                'id' => 'any_id_cart_fees',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Handling Fees Settings', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'Use these settings to control handling fees basic settings', 'table-rate-shipping-rates' ),
            );

            $in_fields[] = array(
                'id' => 'cart_fee_settings',
                'type' => 'columns-field',
                'columns' => 2,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'tax_type',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Determines whether or not to use tax rates applied to shipping only for fees tax rates', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Tax Rate Options', 'table-rate-shipping-rates' ),
                        'default' => array( 'ship_rates' ),
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => $this->get_tax_options(),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'show_on_cart',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Enables handling fees on cart page', 'table-rate-shipping-rates' ),
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
                'prem_1' => esc_html__( 'Use regular tax rates (Premium)', 'table-rate-shipping-rates' ),
            );
        }

    }

    new WTARS_Shipped_Admin_Cart_Fees_Settings();
}