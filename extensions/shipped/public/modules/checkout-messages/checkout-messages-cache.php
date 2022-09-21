<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Checkout_Messages_Cache' ) ) {

    class WTARS_Shipped_Checkout_Messages_Cache {

        private $messages = array();
        private $cache;
        private $loaded = false;

        public function __construct() {

            $this->cache = new WTARS_Shipped_Cache();

            add_filter( 'wtars_shipped/cache-data', array( $this, 'cache_data' ), 40, 1 );
        }

        public function cache_data( $data ) {

            //shipping instance needs to be set
            if ( !isset( $data[ 'wc' ][ 'instance_id' ] ) ) {
                return $data;
            }

            $instance_id = $data[ 'wc' ][ 'instance_id' ];

            //get in memory cached messages
            if ( isset( $this->messages[ $instance_id ] ) ) {
                
                $data[ 'messages' ][ $instance_id ] = $this->messages[ $instance_id ];
            }

            if ( isset( $data[ 'messages' ][ $instance_id ] ) ) {
                
                if ( !count( $data[ 'messages' ][ $instance_id ] ) ) {
                    
                    unset( $data[ 'messages' ][ $instance_id ] );
                }
                
                if ( !count( $data[ 'messages' ] ) ) {
                    
                    unset( $data[ 'messages' ] );
                }
                
                return $data;
            }

            // validate cache hash
            if ( !$this->cache->validate_hash( $instance_id ) ) {
                // reload messages
                $data[ 'wc' ][ 'reload' ] = true;
                return $data;
            }

            // try load fees from session cache
            $data = $this->load_cache( $data, $instance_id );

            if ( isset( $data[ 'messages' ][ $instance_id ] ) ) {

                $this->messages[ $instance_id ] = $data[ 'messages' ][ $instance_id ];
            } else if ( !$this->loaded ) {

                // reload messages
                $data[ 'wc' ][ 'reload' ] = true;
            }

            return $data;
        }

        public function store_cache( $messages, $instance_id ) {

            $instance_messages = array();

            if ( isset( $messages[ $instance_id ] ) ) {
                $instance_messages = $messages[ $instance_id ];
            }

            //set in memory cache
            $this->messages[ $instance_id ] = $instance_messages;

            //store cache hash
            $this->cache->store_hash( $instance_id );

            //store cache data
            $this->cache->store_cache( 'messages', $instance_messages, $instance_id );
        }

        private function load_cache( $data, $instance_id ) {

            $cache_data = $this->cache->load_cache( 'messages', $instance_id );

            $this->loaded = is_array( $cache_data );

            if ( $cache_data ) {

                $data[ 'messages' ][ $instance_id ] = $cache_data;
            }

            return $data;
        }

    }

}
