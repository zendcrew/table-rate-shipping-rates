<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_WPML' ) && function_exists( 'SitePress' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    require_once 'wpml-rates.php';
    require_once 'wpml-messages.php';

    class WTARS_Shipped_WPML {

        private $shipping_rates;
        private $fees;
        private $messages;

        public function __construct() {

            $option_name = WTARS_SHIPPED_OPTION_NAME;

            $this->shipping_rates = new WTARS_Shipped_WPML_Shipping_Rates();
            $this->fees = new WTARS_Shipped_WPML_Fees();
            $this->messages = new WTARS_Shipped_WPML_Messages();

            add_filter( 'reon/process-save-options-' . $option_name, array( $this, 'register_translations' ), 10, 3 );
        }

        public function register_translations( $options, $is_reset, $instance_id ) {

            // Register shipping rates strings
            foreach ( $this->shipping_rates->get_strings( $options, $instance_id ) as $string_id => $string_value ) {

                $this->register_string( $string_id, $string_value );
            }

            // Register handling fees strings
            foreach ( $this->fees->get_strings( $options, $instance_id ) as $string_id => $string_value ) {

                $this->register_string( $string_id, $string_value );
            }

            // Register cart notifications strings
            foreach ( $this->messages->get_strings( $options, $instance_id ) as $string_id => $string_value ) {

                $this->register_string( $string_id, $string_value );
            }

            return $options;
        }

        public function get_translated_string( $string_value, $string_id ) {

            if ( function_exists( 'icl_t' ) ) {

                return icl_t( 'table-rate-shipping-rates', $string_id, $string_value );
            }

            return $string_value;
        }

        private function register_string( $string_id, $string_value ) {

            if ( function_exists( 'icl_register_string' ) ) {

                icl_register_string( 'table-rate-shipping-rates', $string_id, $string_value );
            }
        }

    }

    new WTARS_Shipped_WPML();
}