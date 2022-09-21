<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_WPML_Shipping_Rates' ) ) {

    class WTARS_Shipped_WPML_Shipping_Rates {

        public function __construct() {

            add_filter( 'wtars_shipped/rate-title', array( $this, 'tranlate_title' ), 10, 3 );
            add_filter( 'wtars_shipped/rate-tooltip', array( $this, 'tranlate_tooltip' ), 10, 3 );
            add_filter( 'wtars_shipped/rate-desc', array( $this, 'tranlate_desc' ), 10, 3 );
        }

        public function tranlate_title( $title, $rate_id, $instance_id ) {

            $id_prefix = $this->get_string_id_prefix( $rate_id, $instance_id );

            $string_id = $id_prefix . 'title';

            return WTARS_Shipped_WPML::get_translated_string( $title, $string_id );
        }

        public function tranlate_tooltip( $tooltip, $rate_id, $instance_id ) {

            $id_prefix = $this->get_string_id_prefix( $rate_id, $instance_id );

            $string_id = $id_prefix . 'tooltip';

            return WTARS_Shipped_WPML::get_translated_string( $tooltip, $string_id );
        }

        public function tranlate_desc( $desc, $rate_id, $instance_id ) {

            $id_prefix = $this->get_string_id_prefix( $rate_id, $instance_id );

            $string_id = $id_prefix . 'desc';

            return WTARS_Shipped_WPML::get_translated_string( $desc, $string_id );
        }

        public function get_strings( $options, $instance_id ) {

            $rate_strings = array();

            foreach ( $this->get_shipping_rates( $options ) as $shipping_rate ) {

                $key = $this->get_string_id_prefix( $shipping_rate[ 'rate_id' ], $instance_id );

                $rate_strings[ $key . 'title' ] = $shipping_rate[ 'title' ];

                $rate_strings[ $key . 'tooltip' ] = $shipping_rate[ 'tooltip' ];

                $rate_strings[ $key . 'desc' ] = $shipping_rate[ 'desc' ];
            }

            return $rate_strings;
        }

        private function get_string_id_prefix( $rate_id, $instance_id ) {

            return 'wtars_shipped_rate_' . $instance_id . '_' . $rate_id . '_';
        }

        private function get_shipping_rates( $options ) {

            if ( !isset( $options[ 'shipping_rates' ] ) ) {

                return array();
            }

            return $options[ 'shipping_rates' ];
        }

    }

}

