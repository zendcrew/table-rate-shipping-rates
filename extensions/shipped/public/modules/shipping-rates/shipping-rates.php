<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Shipping_Rates' ) ) {
    WTARS_Shipped_Inc::recursive_require( dirname( __FILE__ ), array( 'shipping-rates.php' ) );

    class WTARS_Shipped_Shipping_Rates {

        private $default_rate_ids = array();
        private $rate_infos = array();

        public function before_calculate( $packages ) {

            $keys = array_keys( $packages );

            // generates package hash
            $package_data = array();

            foreach ( $keys as $key ) {

                $package_data = $this->get_package_data_for_hash( $packages[ $key ] );
            }

            $this->save_package_hash( $package_data );

            // create new custom hash for shipping calculation
            $cache = new WTARS_Shipped_Cache();

            $hash = $cache->get_global_hash();

            foreach ( $keys as $key ) {

                $packages[ $key ][ 'shipped-hash' ] = $hash;
            }

            return $packages;
        }

        public function get_calculated_rates( $data, $package, $instance_id ) {

            $rates = array();

            //get settings
            $settings = WTARS_Shipped::get_option( 'shipping_rate_settings', $instance_id, array( 'show_on_cart' => 'yes' ) );

            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                return $rates;
            }


            //check for calculated rates
            if ( !isset( $data[ 'rates' ][ $instance_id ] ) ) {
                return $rates;
            }

            // go through each calculated rates and process them
            foreach ( $data[ 'rates' ][ $instance_id ] as $rate_id => $calc_rate ) {

                $rate = array(
                    'id' => WTARS_Shipped::get_rate_client_id( $instance_id, $rate_id ),
                    'label' => apply_filters( 'wtars_shipped/rate-title', $calc_rate[ 'title' ], $rate_id, $instance_id ),
                    'cost' => apply_filters( 'wtars_shipped/rate-cost', $calc_rate[ 'cost' ], $rate_id, $instance_id ),
                );

                if ( count( $calc_rate[ 'taxes' ] ) ) {

                    $rate[ 'taxes' ] = apply_filters( 'wtars_shipped/rate-taxes', $calc_rate[ 'taxes' ], $rate_id, $instance_id );
                } else {

                    $rate[ 'taxes' ] = false;
                }

                $meta_data = apply_filters( 'wtars_shipped/rate-meta', array(), $rate_id, $instance_id );

                if ( count( $meta_data ) ) {

                    $rate[ 'meta_data' ] = $meta_data;
                }

                $rate[ 'package' ] = $package;

                $rates[] = $rate;
            }

            return $rates;
        }

        public function get_instance_ids_from_rates( $rates ) {

            $instance_ids = array();

            foreach ( $rates as $rate ) {

                $method_id = $rate->get_method_id();

                $instance_id = $rate->get_instance_id();

                if ( WTARS_SHIPPED_METHOD_ID == $method_id && !in_array( $instance_id, $instance_ids ) ) {

                    $instance_ids[] = $instance_id;
                }
            }

            return $instance_ids;
        }

        public function prepare_rates_for_display( $data, $instance_id ) {

            if ( !isset( $data[ 'rates' ] ) ) {
                return;
            }

            //get settings
            $settings = WTARS_Shipped::get_option( 'shipping_rate_settings', $instance_id, array( 'show_on_cart' => 'yes' ) );

            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                return;
            }


            foreach ( $data[ 'rates' ][ $instance_id ] as $rate_id => $rate ) {

                $shipping_rate_id = WTARS_Shipped::get_rate_client_id( $instance_id, $rate_id );

                //prepare display info

                $rate_info = array();
                if ( '' != $rate[ 'desc' ] ) {
                    $rate_info[ 'desc' ] = apply_filters( 'wtars_shipped/rate-desc', $rate[ 'desc' ], $rate_id, $instance_id );
                }

                if ( '' != $rate[ 'tooltip' ] ) {
                    $rate_info[ 'tooltip' ] = apply_filters( 'wtars_shipped/rate-tooltip', $rate[ 'tooltip' ], $rate_id, $instance_id );
                }

                $this->rate_infos[ $shipping_rate_id ] = $rate_info;


                //prepare default selections
                if ( $rate[ 'is_default' ] ) {

                    $this->default_rate_ids[] = $shipping_rate_id;
                }
            }
        }

        public function get_rate_info( $shipping_rate ) {

            $shipping_rate_id = $shipping_rate->get_id();

            if ( isset( $this->rate_infos[ $shipping_rate_id ] ) ) {
                return $this->rate_infos[ $shipping_rate_id ];
            }

            return array();
        }

        public function get_default_rate( $default, $rates ) {

            if ( !count( $this->default_rate_ids ) ) {
                return $default;
            }

            if ( isset( $rates[ $this->default_rate_ids[ 0 ] ] ) ) {
                return $this->default_rate_ids[ 0 ];
            }

            return $default;
        }

        public function maybe_hide_methods( $rates, $instance_id ) {

            $settings = WTARS_Shipped::get_option( 'shipping_rate_settings', $instance_id, array( 'show_on_cart' => 'yes' ) );

            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                return $rates;
            }

            $hide_args = WTARS_Shipped::get_option( 'hide_methods', $instance_id, array( 'mode' => 'no' ) );

            if ( 'no' == $hide_args[ 'mode' ] ) {
                return $rates;
            }

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return WTARS_Shipped_Premium_Shipping_Rates::maybe_hide_methods( $rates, $instance_id, $hide_args );
            }

            // hide other methods
            $keys = array_keys( $rates );

            foreach ( $keys as $key ) {
                $method_instance_id = $rates[ $key ]->get_instance_id();
                if ( $method_instance_id != $instance_id && isset( $rates[ $key ] ) ) {
                    unset( $rates[ $key ] );
                }
            }

            return $rates;
        }

        private function get_package_data_for_hash( $package ) {

            $p_data = array();

            foreach ( $package[ 'contents' ] as $key => $item ) {

                $p_data[ $key ][ 'product_id' ] = $item[ 'product_id' ];
                $p_data[ $key ][ 'variation_id' ] = $item[ 'variation_id' ];
                $p_data[ $key ][ 'quantity' ] = $item[ 'quantity' ];
                $p_data[ $key ][ 'line_subtotal' ] = $item[ 'line_subtotal' ];
                $p_data[ $key ][ 'line_subtotal_tax' ] = $item[ 'line_subtotal_tax' ];
                $p_data[ $key ][ 'line_total' ] = $item[ 'line_total' ];
                $p_data[ $key ][ 'line_tax' ] = $item[ 'line_tax' ];
            }

            return $p_data;
        }

        private function save_package_hash( $package_data ) {
            $hash = md5( wp_json_encode( $package_data ) );
            WTARS_Shipped_Cart::set_session_data( 'package-hash', $hash, 'cache' );
        }

    }

}