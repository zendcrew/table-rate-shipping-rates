<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Cart_Fees_Cache' ) ) {

    class WTARS_Shipped_Cart_Fees_Cache {

        private $fees = array();
        private $cache;
        private $loaded = false;

        public function __construct() {
            $this->cache = new WTARS_Shipped_Cache();

            add_filter( 'wtars_shipped/cache-data', array( $this, 'cache_data' ), 30, 1 );
        }

        public function cache_data( $data ) {

            //shipping instance needs to be set
            if ( !isset( $data[ 'wc' ][ 'instance_id' ] ) ) {
                return $data;
            }

            $instance_id = $data[ 'wc' ][ 'instance_id' ];

            //get in memory cached fees
            if ( isset( $this->fees[ $instance_id ] ) ) {
                $data[ 'fees' ][ $instance_id ] = $this->fees[ $instance_id ];
            }

            if ( isset( $data[ 'fees' ][ $instance_id ] ) ) {
                if ( !count( $data[ 'fees' ][ $instance_id ] ) ) {
                    unset( $data[ 'fees' ][ $instance_id ] );
                }
                if ( !count( $data[ 'fees' ] ) ) {
                    unset( $data[ 'fees' ] );
                }
                return $data;
            }

            // validate cache hash
            if ( !$this->cache->validate_hash( $instance_id ) ) {
                // reload fees
                $data[ 'wc' ][ 'reload' ] = true;
                return $data;
            }


            // try load fees from session cache
            $data = $this->load_cache( $data, $instance_id );

            if ( isset( $data[ 'fees' ][ $instance_id ] ) ) {

                $this->fees[ $instance_id ] = $data[ 'fees' ][ $instance_id ];
            } else if ( !$this->loaded ) {

                // reload fees
                $data[ 'wc' ][ 'reload' ] = true;
            }

            return $data;
        }

        public function store_cache( $fees, $instance_id ) {

            $instance_fees = array();

            if ( isset( $fees[ $instance_id ] ) ) {
                $instance_fees = $fees[ $instance_id ];
            }

            //set in memory cache
            $this->fees[ $instance_id ] = $instance_fees;

            //store cache hash
            $this->cache->store_hash( $instance_id );

            //store cache data
            $this->cache->store_cache( 'fees', $instance_fees, $instance_id );
        }

        private function load_cache( $data, $instance_id ) {

            $cache_data = $this->cache->load_cache( 'fees', $instance_id );

            $this->loaded = is_array( $cache_data );

            if ( $cache_data ) {

                $data[ 'fees' ][ $instance_id ] = $cache_data;
            }

            return $data;
        }

    }

}