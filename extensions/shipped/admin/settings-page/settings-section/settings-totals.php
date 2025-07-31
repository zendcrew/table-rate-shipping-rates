<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Checkout_Totals_Settings' ) ) {

    class WTARS_Shipped_Admin_Checkout_Totals_Settings {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-settings-section-fields', array( $this, 'get_panel' ), 40, 2 );

            add_filter( 'reon/get-simple-repeater-field-checkout_totals-templates', array( $this, 'get_templates' ), 10, 2 );
            add_filter( 'roen/get-simple-repeater-template-checkout_totals-calc_default-fields', array( $this, 'get_template_fields' ), 10, 2 );
            add_filter( 'roen/get-simple-repeater-template-checkout_totals-calc_option-fields', array( $this, 'get_template_fields' ), 10, 2 );
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

        public function get_templates( $in_templates, $repeater_args ) {

            $in_templates[] = array(
                'id' => 'calc_default',
                'empy_button' => true,
            );

            $in_templates[] = array(
                'id' => 'calc_option',
            );


            return $in_templates;
        }

        public function get_template_fields( $in_fields, $repeater_args ) {

            $in_fields[] = array(
                'id' => 'option_id',
                'type' => 'autoid',
                'autoid' => 'shipped',
            );

            $in_fields[] = array(
                'id' => 'title',
                'type' => 'textbox',
                'default' => esc_html__( 'Cart Subtotals', 'table-rate-shipping-rates' ),
                'placeholder' => esc_html__( 'Title here...', 'table-rate-shipping-rates' ),
                'width' => '98%',
                'box_width' => '36%',
            );


            $in_fields[] = array(
                'id' => 'include',
                'type' => 'select2',
                'multiple' => true,
                'minimum_results_forsearch' => 10,
                'default' => array( 'subtotal' ),
                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                'options' => $this->get_options(),
                'width' => '100%',
                'box_width' => '64%',
            );

            return $in_fields;
        }

        private function get_fields( $in_fields ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Cart &amp; Package Totals Calculation', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'Use these settings to control what are included in the "based on" percentage totals &amp; the "cart &amp; package totals" conditions', 'table-rate-shipping-rates' ),
            );

            $max_sections = 1;
            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $max_sections = 99999;
            }

            $in_fields[] = array(
                'id' => 'checkout_totals',
                'type' => 'simple-repeater',
                'full_width' => true,
                'center_head' => true,
                'white_repeater' => true,
                'repeater_size' => 'smaller',
                'buttons_sep' => false,
                'buttons_box_width' => '65px',
                'max_sections' => $max_sections,
                'max_sections_msg' => esc_html__( 'Please upgrade to premium version in order to add more options', 'table-rate-shipping-rates' ),
                'width' => '100%',
                'default' => $this->get_defaults(),
                'static_template' => 'calc_default',
                'section_type_id' => 'calc_option_type',
                'sortable' => array(
                    'enabled' => false,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'show_list' => false,
                    'button_text' => esc_html__( 'Add Option', 'table-rate-shipping-rates' ),
                ),
            );

            return $in_fields;
        }

        private function get_options() {

            $options = array(
                'subtotal' => esc_html__( 'Add subtotal', 'table-rate-shipping-rates' ),
                'subtotal_tax' => esc_html__( 'Add subtotal Tax', 'table-rate-shipping-rates' ),
                'package_total' => esc_html__( 'Add package total', 'table-rate-shipping-rates' ),
                'package_total_tax' => esc_html__( 'Add package total Tax', 'table-rate-shipping-rates' ),
            );

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $options[ 'prem_1' ] = esc_html__( 'Add cart coupons (Premium)', 'table-rate-shipping-rates' );
                $options[ 'prem_2' ] = esc_html__( 'Add cart coupons taxes (Premium)', 'table-rate-shipping-rates' );
                $options[ 'prem_3' ] = esc_html__( 'Subtract cart coupons (Premium)', 'table-rate-shipping-rates' );
                $options[ 'prem_4' ] = esc_html__( 'Subtract cart coupon taxes (Premium)', 'table-rate-shipping-rates' );

                $options[ 'prem_5' ] = esc_html__( 'Add package discounts (Premium)', 'table-rate-shipping-rates' );
                $options[ 'prem_6' ] = esc_html__( 'Add package discounts taxes (Premium)', 'table-rate-shipping-rates' );
                $options[ 'prem_7' ] = esc_html__( 'Subtract package discounts (Premium)', 'table-rate-shipping-rates' );
                $options[ 'prem_8' ] = esc_html__( 'Subtract package discounts taxes (Premium)', 'table-rate-shipping-rates' );
            }

            return apply_filters( 'wtars_shipped_admin/get-totals-options', $options );
        }

        private function get_defaults() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return array(
                    array(
                        'calc_option_type' => 'calc_default',
                        'title' => esc_html__( 'Cart Subtotals', 'table-rate-shipping-rates' ),
                        'include' => array( 'subtotal', 'subtotal_tax' ),
                        'option_id' => '2234343',
                    ),
                    array(
                        'calc_option_type' => 'calc_option',
                        'title' => esc_html__( 'Package Totals', 'table-rate-shipping-rates' ),
                        'include' => array( 'package_total', 'package_total_tax' ),
                        'option_id' => '2234344',
                    ),
                );
            }

            return array(
                array(
                    'calc_option_type' => 'calc_default',
                    'title' => esc_html__( 'Order Totals', 'table-rate-shipping-rates' ),
                    'include' => array( 'subtotal', 'subtotal_tax' ),
                    'option_id' => '2234343',
                ),
            );
        }

    }

    new WTARS_Shipped_Admin_Checkout_Totals_Settings();
}