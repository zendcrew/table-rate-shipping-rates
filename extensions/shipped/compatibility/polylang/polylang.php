<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Polylang' ) && class_exists( 'Polylang' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    require_once 'polylang-rates.php';
    require_once 'polylang-messages.php';

    class WTARS_Shipped_Polylang {

        private $shipping_rates;
        private $messages;

        public function __construct() {

            $option_name = WTARS_SHIPPED_OPTION_NAME;

            $this->shipping_rates = new WTARS_Shipped_Polylang_Shipping_Rates();
            $this->messages = new WTARS_Shipped_Polylang_Messages();

            add_filter( 'reon/process-save-options-' . $option_name, array( $this, 'register_translations' ), 10, 3 );
        }

        public function register_translations( $options, $is_reset, $instance_id ) {

            // Register shipping rates strings
            foreach ( $this->shipping_rates->get_strings( $options, $instance_id ) as $string_id => $string_data ) {

                $this->register_string( $string_id, $string_data[ 'value' ], $string_data[ 'is_multiline' ] );
            }

            // Register cart notifications strings
            foreach ( $this->messages->get_strings( $options, $instance_id ) as $string_id => $string_data ) {

                $this->register_string( $string_id, $string_data[ 'value' ], $string_data[ 'is_multiline' ] );
            }

            return $options;
        }

        public static function get_translated_string( $string_value, $string_id ) {

            if ( function_exists( 'pll__' ) ) {

                return pll__( $string_value );
            } else if ( function_exists( 'icl_t' ) ) {

                return icl_t( 'table-rate-shipping-rates', $string_id, $string_value );
            }

            return $string_value;
        }

        private function register_string( $string_id, $string_value, $is_multiline = true ) {

            if ( function_exists( 'icl_register_string' ) ) {

                icl_register_string( 'table-rate-shipping-rates', $string_id, $string_value );
            } else if ( function_exists( 'pll_register_string' ) ) {

                pll_register_string( $string_id, $string_value, 'table-rate-shipping-rates', $is_multiline );
            }
        }

    }

    new WTARS_Shipped_Polylang();
}

