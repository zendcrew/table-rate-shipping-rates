<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Order_Util' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Order_Util {
        
        private static $instance;
        
        public static function get_instance(): self {

            if ( is_null( self::$instance ) ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public static function get_package_product_ids( $data ) {

            $product_ids = array();

            if ( !isset( $data[ 'wc' ][ 'package' ][ 'items' ] ) ) {
                return $product_ids;
            }

            foreach ( $data[ 'wc' ][ 'package' ][ 'items' ] as $package_item ) {

                if ( isset( $package_item[ 'product_id' ] ) && 0 < $package_item[ 'product_id' ] ) {
                    $product_ids[] = $package_item[ 'product_id' ];
                }
            }

            return $product_ids;
        }

        public static function get_package_category_ids( $data ) {

            $category_ids = array();

            if ( !isset( $data[ 'wc' ][ 'package' ][ 'items' ] ) ) {
                return $category_ids;
            }

            foreach ( self::get_package_items( $data ) as $package_item ) {

                if ( !isset( $package_item[ 'data' ][ 'category_ids' ] ) ) {
                    continue;
                }

                foreach ( $package_item[ 'data' ][ 'category_ids' ] as $category_id ) {
                    if ( !in_array( $category_id, $category_ids ) ) {
                        $category_ids[] = $category_id;
                    }
                }
            }

            return $category_ids;
        }

        public static function get_package_tag_ids( $data ) {
            $tag_ids = array();

            if ( !isset( $data[ 'wc' ][ 'package' ][ 'items' ] ) ) {
                return $tag_ids;
            }

            foreach ( self::get_package_items( $data ) as $package_item ) {

                if ( !isset( $package_item[ 'data' ][ 'tag_ids' ] ) ) {
                    continue;
                }

                foreach ( $package_item[ 'data' ][ 'tag_ids' ] as $tag_id ) {
                    if ( !in_array( $tag_id, $tag_ids ) ) {
                        $tag_ids[] = $tag_id;
                    }
                }
            }

            return $tag_ids;
        }

        public static function get_package_shipping_classes( $data ) {

            $shipping_classes = array();

            if ( !isset( $data[ 'wc' ][ 'package' ][ 'items' ] ) ) {
                return $shipping_classes;
            }

            foreach ( self::get_package_items( $data ) as $package_item ) {

                if ( !isset( $package_item[ 'data' ][ 'shipping_class_id' ] ) ) {
                    continue;
                }

                $shipping_class = $package_item[ 'data' ][ 'shipping_class_id' ];

                if ( 0 < $shipping_class ) {
                    $shipping_classes[] = $shipping_class;
                }
            }

            return $shipping_classes;
        }

        private static function get_package_items( $data ) {

            return self::get_package_items_data( $data[ 'wc' ][ 'package' ][ 'items' ], $data[ 'wc' ][ 'cart' ][ 'items' ] );
        }

        private static function get_package_items_data( $package_items, $cart_items ) {

            foreach ( array_keys( $package_items ) as $key ) {

                $package_items[ $key ][ 'data' ] = self::get_cart_item_data( $package_items[ $key ][ 'key' ], $cart_items );
            }

            return $package_items;
        }

        private static function get_cart_item_data( $key, $cart_items ) {

            foreach ( $cart_items as $cart_item ) {
                if ( $key == $cart_item[ 'key' ] && isset( $cart_item[ 'data' ] ) ) {
                    return $cart_item[ 'data' ];
                }
            }

            return array();
        }

    }

}