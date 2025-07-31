<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Shipping_Rates_Panel_Options' ) ) {

    class WTARS_Shipped_Admin_Shipping_Rates_Panel_Options {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/shipping-rates/get-panel-option-fields', array( $this, 'get_fields' ), 10, 2 );
        }

        public function get_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 4,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'title',
                        'type' => 'textbox',
                        'tooltip' => esc_html__( 'Controls shipping rate title on cart and checkout pages', 'table-rate-shipping-rates' ),
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Title', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__( 'Adds a private note for reference purposes', 'table-rate-shipping-rates' ),
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Admin Note', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'default',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Default Selection', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls shipping rate default selection', 'table-rate-shipping-rates' ),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__( 'No', 'table-rate-shipping-rates' ),
                            'yes' => esc_html__( 'Yes', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                    ),
                ),
            );


            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 3,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'tooltip',
                        'type' => 'textarea',
                        'sanitize_type' => 'shipped_kses_post',
                        'tooltip' => esc_html__( 'Controls shipping rate tooltip on cart and checkout pages', 'table-rate-shipping-rates' ),
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Tooltip', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'rows' => 1,
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'inclusive_tax',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Tax Calculation', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls shipping rate tax calculations', 'table-rate-shipping-rates' ),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__( 'Calculate shipping cost exclusive of taxes', 'table-rate-shipping-rates' ),
                            'yes' => esc_html__( 'Calculate shipping cost inclusive of taxes', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                    ),
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 1,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'desc',
                        'type' => 'textarea',
                        'sanitize_type' => 'shipped_kses_post',
                        'tooltip' => esc_html__( 'Controls shipping rate description on cart and checkout pages', 'table-rate-shipping-rates' ),
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Description', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Shipping_Rates_Panel_Options();
}
