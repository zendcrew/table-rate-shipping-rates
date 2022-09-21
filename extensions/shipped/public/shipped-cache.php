<?php

if ( !class_exists( 'WTARS_Shipped_Cache' ) ) {

    class WTARS_Shipped_Cache {

        public function validate_hash( $instance_id ) {

            $stored_hash = $this->get_session_value( 'hash', $instance_id, false, 'cache' );

            $hash = $this->get_hash( $instance_id );

            return ($stored_hash == $hash);
        }

        public function store_hash( $instance_id ) {

            $hash = $this->get_hash( $instance_id );

            $this->set_session_value( 'hash', $this->get_hash( $instance_id ), $instance_id, 'cache' );
        }

        public function store_cache( $key, $object, $instance_id ) {

            $this->set_session_value( $key, $object, $instance_id, 'cache' );
        }

        public function load_cache( $key, $instance_id ) {
            return $this->get_session_value( $key, $instance_id, false, 'cache' );
        }

        public function get_global_hash() {

            $hash_instances = WTARS_Shipped_Cart::get_session_data( 'hash', array(), 'cache' );

            $instance_ids = array_keys( $hash_instances );

            $hashes = array();
            
            foreach ( $instance_ids as $instance_id ) {
                
                if ( is_numeric( $instance_id ) ) {
                    
                    $hashes[] = $this->get_hash( $instance_id );
                }
            }

            return md5( wp_json_encode( $hashes ) );
        }

        private function get_session_value( $key, $instance_id, $default, $suffix ) {

            $objoct = WTARS_Shipped_Cart::get_session_data( $key, $default, $suffix );

            if ( isset( $objoct[ $instance_id ] ) ) {
                return $objoct[ $instance_id ];
            }

            return $default;
        }

        private function set_session_value( $key, $object, $instance_id, $suffix ) {

            $stored_object = WTARS_Shipped_Cart::get_session_data( $key, array(), $suffix );

            $stored_object[ $instance_id ] = $object;

            WTARS_Shipped_Cart::set_session_data( $key, $stored_object, $suffix );
        }

        private function get_hash( $instance_id ) {


            $settings = $this->get_cache_settings( $instance_id );

            if ( 'yes' == $settings[ 'enable_debug' ] ) {
                return md5( wp_json_encode( array( current_time( 'U' ) ) ) );
            }

            $params = array();

            foreach ( $settings[ 'cache_params' ] as $cache_param ) {

                if ( 'package_items' == $cache_param ) {

                    $params[ 'package_items' ] = WTARS_Shipped_Cart::get_session_data( 'package-hash', md5( '' ), 'cache' );
                }

                if ( 'cart_items' == $cache_param ) {
                    $params[ 'cart_items' ] = WC()->session->get( 'cart' );
                }
            }

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $params = WTARS_Shipped_Premium_Cache::get_hash( $params, $settings );
            }

            $hash = md5( wp_json_encode( $params ) );

            if ( has_filter( 'wtars_shipped/get-hash' ) ) {
                $hash = apply_filters( 'wtars_shipped/get-hash', $hash, $params );
            }

            return $hash;
        }

        private function get_cache_settings( $instance_id ) {
            $default = array(
                'enable_debug' => 'no',
                'cache_params' => array( 'package_items' ),
            );

            return WTARS_Shipped::get_option( 'cache_settings', $instance_id, $default );
        }

    }

}