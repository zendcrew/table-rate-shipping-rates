<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Shipping_Rates_Cache' ) ) {

    class WTARS_Shipped_Shipping_Rates_Cache {

        private $rates = array();
        private $cache;
        private $loaded = false;

        public function __construct() {

            $this->cache = new WTARS_Shipped_Cache();

            add_filter( 'wtars_shipped/cache-data', array( $this, 'cache_data' ), 20, 1 );
        }

        public function cache_data( $data ) {

            //shipping instance needs to be set
            if ( !isset( $data[ 'wc' ][ 'instance_id' ] ) ) {
                return $data;
            }

            $instance_id = $data[ 'wc' ][ 'instance_id' ];


            //get in memory cached rates
            if ( isset( $this->rates[ $instance_id ] ) ) {
                $data[ 'rates' ][ $instance_id ] = $this->rates[ $instance_id ];
            }

            if ( isset( $data[ 'rates' ][ $instance_id ] ) ) {
                if ( !count( $data[ 'rates' ][ $instance_id ] ) ) {
                    unset( $data[ 'rates' ][ $instance_id ] );
                }
                if ( !count( $data[ 'rates' ] ) ) {
                    unset( $data[ 'rates' ] );
                }
                return $data;
            }


            // validate cache hash
            if ( !$this->cache->validate_hash( $instance_id ) ) {
                // reload rates
                $data[ 'wc' ][ 'reload' ] = true;
                return $data;
            }

            // try load rates from session cache
            $data = $this->load_cache( $data, $instance_id );

            if ( isset( $data[ 'rates' ][ $instance_id ] ) ) {

                $this->rates[ $instance_id ] = $data[ 'rates' ][ $instance_id ];
            } else if ( !$this->loaded ) {

                // reload rates
                $data[ 'wc' ][ 'reload' ] = true;
            }

            return $data;
        }

        public function store_cache( $rates, $instance_id ) {

            $instance_rates = array();

            if ( isset( $rates[ $instance_id ] ) ) {
                $instance_rates = $rates[ $instance_id ];
            }

            //set in memory cache
            $this->rates[ $instance_id ] = $instance_rates;

            //store cache hash
            $this->cache->store_hash( $instance_id );

            //store cache data
            $this->cache->store_cache( 'rates', $instance_rates, $instance_id );
        }

        private function load_cache( $data, $instance_id ) {

            $cache_data = $this->cache->load_cache( 'rates', $instance_id );

            $this->loaded = is_array( $cache_data );

            if ( $cache_data ) {
                $data[ 'rates' ][ $instance_id ] = $cache_data;
            }

            return $data;
        }

    }

}
