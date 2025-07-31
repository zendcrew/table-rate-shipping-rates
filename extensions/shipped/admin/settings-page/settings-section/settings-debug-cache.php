<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Debug_Cache_Settings' ) ) {

    class WTARS_Shipped_Admin_Debug_Cache_Settings {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-settings-section-fields', array( $this, 'get_panel' ), 70, 2 );
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

            $dec_allowed_html = array(
                'br' => array(),
            );

            $in_fields[] = array(
                'id' => 'any_id_shipping_rates',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Debug &amp; Cache Settings', 'table-rate-shipping-rates' ),
                'desc' => wp_kses( __( 'Use these settings to enable debug mode or specify how shipping data should be cached.<br /> Debug mode will disable caching.', 'table-rate-shipping-rates' ), $dec_allowed_html ),
            );

            $in_fields[] = array(
                'id' => 'cache_settings',
                'type' => 'columns-field',
                'columns' => 5,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'enable_debug',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Enables debug mode for testing purposes only', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Enable Debug', 'table-rate-shipping-rates' ),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__( 'No', 'table-rate-shipping-rates' ),
                            'yes' => esc_html__( 'Yes', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                        'fold_id' => 'enable_debug'
                    ),
                    array(
                        'id' => 'cache_params',
                        'type' => 'select2',
                        'column_size' => 4,
                        'tooltip' => esc_html__( 'Specify when shipping data should be cached based on parameters', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Cache Parameters', 'table-rate-shipping-rates' ),
                        'multiple' => true,
                        'default' => array( 'package_items' ),
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => apply_filters( 'wtars_shipped_admin/get-hash-params', $this->get_params() ),
                        'width' => '100%',
                        'fold' => array(
                            'target' => 'enable_debug',
                            'attribute' => 'value',
                            'value' => 'no',
                            'oparator' => 'eq',
                            'clear' => false,
                        ),
                    ),
                ),
            );

            return $in_fields;
        }

        private function get_params() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return array(
                    'package_items' => esc_html__( 'Package items', 'table-rate-shipping-rates' ),
                    'cart_items' => esc_html__( 'Cart items', 'table-rate-shipping-rates' ),
                );
            }

            return array(
                'package_items' => esc_html__( 'Package items', 'table-rate-shipping-rates' ),
                'cart_items' => esc_html__( 'Cart items', 'table-rate-shipping-rates' ),
                'prem_1' => esc_html__( 'Applied coupons (Premium)', 'table-rate-shipping-rates' ),
                'prem_2' => esc_html__( 'Billing address (Premium)', 'table-rate-shipping-rates' ),
                'prem_3' => esc_html__( 'Shipping address (Premium)', 'table-rate-shipping-rates' ),
                'prem_4' => esc_html__( 'Payment methods (Premium)', 'table-rate-shipping-rates' ),
                'prem_5' => esc_html__( 'Year (Premium)', 'table-rate-shipping-rates' ),
                'prem_6' => esc_html__( 'Month (Premium)', 'table-rate-shipping-rates' ),
                'prem_7' => esc_html__( 'Day (Premium)', 'table-rate-shipping-rates' ),
                'prem_8' => esc_html__( 'Hour (Premium)', 'table-rate-shipping-rates' ),
                'prem_9' => esc_html__( '30 minutes (Premium)', 'table-rate-shipping-rates' ),
                'prem_10' => esc_html__( '10 minutes (Premium)', 'table-rate-shipping-rates' ),
                'prem_11' => esc_html__( 'Minute (Premium)', 'table-rate-shipping-rates' ),
            );
        }

    }

    new WTARS_Shipped_Admin_Debug_Cache_Settings();
}