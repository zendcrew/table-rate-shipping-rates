<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Package' ) ) {

    class WTARS_Shipped_Package {

        private static $instance = null;
        private $package_data = array();

        private static function get_instance() {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function get_data( $package ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return WTARS_Shipped_Premium_Package::get_data( $package );
            } else {

                return self::get_instance()->get_all_data( $package );
            }
        }

        public static function get_package_items_data( $package_items, $cart_items ) {

            foreach ( array_keys( $package_items ) as $key ) {
                $package_items[ $key ][ 'data' ] = self::get_instance()->get_cart_item_data( $package_items[ $key ][ 'key' ], $cart_items );
            }

            return $package_items;
        }

        private function get_all_data( $package ) {
            $hash_key = $this->get_hash_key( $package );


            if ( isset( $this->package_data[ $hash_key ] ) ) {

                return $this->package_data[ $hash_key ];
            }

            if ( !$package ) {

                return false;
            }

            $this->package_data[ $hash_key ] = $this->get_contents_data( array(), $package );

            return $this->package_data[ $hash_key ];
        }

        private function get_contents_data( $package_data, $package ) {

            $package_data[ 'items' ] = array();

            foreach ( $package[ 'contents' ] as $key => $item ) {

                $package_data[ 'items' ][ $key ] = $this->get_item( $item );
            }

            return $this->get_destination( $package_data, $package );
        }

        private function get_item( $package_item ) {

            $item = array(
                'key' => $package_item[ 'product_id' ] . '_' . $package_item[ 'variation_id' ],
                'product_id' => $package_item[ 'product_id' ],
                'quantity' => $package_item[ 'quantity' ],
                'line_subtotal' => $package_item[ 'line_subtotal' ],
                'line_subtotal_tax' => $package_item[ 'line_subtotal_tax' ],
            );

            if ( has_filter( 'wtars_shipped/get-package-item' ) ) {
                $item = apply_filters( 'wtars_shipped/get-package-item', $item, $package_item );
            }

            return $item;
        }

        private function get_destination( $package_data, $package ) {

            $package_data[ 'destination' ] = $package[ 'destination' ];

            if ( has_filter( 'wtars_shipped/get-package-destination' ) ) {
                $package_data[ 'package_cost' ] = apply_filters( 'wtars_shipped/get-package-destination', $package_data[ 'destination' ], $package );
            }

            return $package_data;
        }

        private function get_cart_item_data( $key, $cart_items ) {

            foreach ( $cart_items as $cart_item ) {
                if ( $key == $cart_item[ 'key' ] && isset( $cart_item[ 'data' ] ) ) {
                    return $cart_item[ 'data' ];
                }
            }

            return array();
        }

        private function get_hash_key( $package ) {

            return wp_json_encode( md5( wp_json_encode( $package ) ) );
        }

    }

}