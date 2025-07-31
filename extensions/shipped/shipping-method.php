<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Shipping_Method' ) ) {

    class WTARS_Shipped_Shipping_Method extends WC_Shipping_Method {

        public function __construct( $instance_id = 0 ) {

            $this->instance_id = absint( $instance_id );
            $this->id = WTARS_SHIPPED_METHOD_ID;
            $this->method_title = esc_html__( 'Table Rate Shipping', 'table-rate-shipping-rates' );
            $this->method_description = esc_html__( 'Table Rate Shipping & Handling Fees for WooCommerce', 'table-rate-shipping-rates' );
            $this->plugin_id = WTARS_SHIPPED_METHOD_ID;
            $this->supports = array(
                'shipping-zones',
                'instance-settings',
            );

            $this->init();
        }

        function init() {

            // Load the settings API
            $this->init_form_fields();
            $this->init_settings();

            $settings = $this->get_shipped_settings();

            $this->tax_status = $settings[ 'tax_status' ];

            $this->title = $settings[ 'title' ];
        }

        public function calculate_shipping( $package = array() ) {

            //calculate shipping rates
            $shipping_rates = apply_filters( 'wtars_shipped/calculate-shipping', $package, $this->instance_id );

            //apply calculated rates
            foreach ( $shipping_rates as $shipping_rate ) {

                $this->add_rate( $shipping_rate );
            }
        }

        public function get_rate_id( $suffix = '' ) {

            //get shipping rate id used on cart and checkout page

            if ( !class_exists( 'WTARS_Shipped' ) ) {

                return WTARS_SHIPPED_METHOD_ID . ':' . $this->instance_id . '_' . $suffix;
            }

            //get shipping rate id used on cart and checkout page
            return WTARS_Shipped::get_rate_client_id( $this->instance_id, $suffix );
        }

        public function init_form_fields() {

            $this->instance_form_fields = array(
                'settings_page' => array(
                    'type' => 'wtars_shipped_admin',
                    'id' => 'wtars_shipped_page',
                    'instance_id' => $this->instance_id,
                    'option_name' => WTARS_SHIPPED_OPTION_NAME,
                ),
            );
        }

        public function generate_wtars_shipped_admin_html( $key, $field ) {

            ob_start();

            if ( !defined( 'WTARS_SHIPPED_ADMIN_PAGE' ) ) {
                define( 'WTARS_SHIPPED_ADMIN_PAGE', 1 );
            }

            ReonOptionPageWC::render_wc_page_field( $field );

            $html = ob_get_clean();

            return $html;
        }

        public function admin_options() {

            echo $this->get_admin_options_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        public function get_admin_options_html() {

            $settings_html = $this->generate_settings_html( $this->get_instance_form_fields(), false );

            return $settings_html;
        }

        private function get_shipped_settings() {

            $default_settings = array(
                'title' => esc_html__( 'Table Rate Shipping & Fees', 'table-rate-shipping-rates' ),
                'tax_type' => 'no',
                'tax_status' => 'none'
            );

            if ( !class_exists( 'WTARS_Shipped' ) ) {

                return $default_settings;
            }

            if ( class_exists( 'WTARS_Shipped_Shipping_Method' ) ) {

                $settings = WTARS_Shipped::get_option( 'shipping_rate_settings', $this->instance_id, $default_settings );
            } else {

                $settings = $default_settings;
            }

            if ( '' == $settings[ 'title' ] ) {
                $settings[ 'title' ] = $default_settings[ 'title' ];
            }

            if ( 'no' != $settings[ 'tax_type' ] ) {

                $settings[ 'tax_status' ] = 'taxable';
            } else {

                $settings[ 'tax_status' ] = 'none';
            }

            return $settings;
        }

    }

}

