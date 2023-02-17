<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Cart' ) ) {

    class WTARS_Shipped_Cart {

        private static $instance = null;
        private $cart_data = array();
        private $parent_products = array();

        private static function get_instance() {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function get_session_data( $key, $default = array(), $suffix = false ) {

            $session_key = 'shipped';
            if ( $suffix ) {
                $session_key = $session_key . '-' . $suffix;
            }

            $session_data = WC()->session->get( $session_key, array() );
            if ( isset( $session_data[ $key ] ) ) {
                return $session_data[ $key ];
            }

            return $default;
        }

        public static function set_session_data( $key, $object, $suffix = false ) {

            $session_key = 'shipped';
            if ( $suffix ) {
                $session_key = $session_key . '-' . $suffix;
            }

            $session_data = WC()->session->get( $session_key, array() );
            $session_data[ $key ] = $object;
            WC()->session->set( $session_key, $session_data );
        }

        public static function get_data() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return WTARS_Shipped_Premium_Cart::get_data();
            } else {

                return self::get_instance()->get_all_data();
            }
        }

        private function get_all_data() {

            if ( count( $this->cart_data ) ) {

                return $this->cart_data;
            }

            $cart = $this->get_cart();

            if ( !$cart ) {

                return false;
            }

            $this->cart_data = $this->get_contents_data( array(), $cart );

            return $this->cart_data;
        }

        private function get_contents_data( $cart_data, $cart ) {

            $cart_data[ 'items' ] = array();

            foreach ( $cart->cart_contents as $key => $item ) {

                $cart_data[ 'items' ][ $key ] = $this->get_item( $cart->cart_contents[ $key ] );
                $cart_data[ 'items' ][ $key ][ 'data' ] = $this->get_product_data( $cart->cart_contents[ $key ][ 'data' ], $key );
            }

            return $this->get_totals_data( $cart_data, $cart );
        }

        private function get_item( $cart_item ) {
            $item = array(
                'key' => $cart_item[ 'product_id' ] . '_' . $cart_item[ 'variation_id' ],
                'product_id' => $cart_item[ 'product_id' ],
                'quantity' => $cart_item[ 'quantity' ],
            );

            if ( has_filter( 'wtars_shipped/get-cart-item' ) ) {
                $item = apply_filters( 'wtars_shipped/get-cart-item', $item, $cart_item );
            }

            return $item;
        }

        private function get_product_data( $product, $item_key ) {

            $item_data = array(
                'is_taxable' => $product->is_taxable(),
                'tax_status' => $product->get_tax_status(),
                'tax_class' => $product->get_tax_class(),
                'shipping_class_id' => $product->get_shipping_class_id(),
                'weight' => $product->get_weight(),
            );

            if ( $product->get_parent_id() > 0 ) {

                $v_product = $this->get_parent_product( $product->get_parent_id() );

                $item_data[ 'category_ids' ] = $v_product->get_category_ids();
                $item_data[ 'tag_ids' ] = $v_product->get_tag_ids();
            } else {

                $item_data[ 'category_ids' ] = $product->get_category_ids();
                $item_data[ 'tag_ids' ] = $product->get_tag_ids();
            }

            if ( has_filter( 'wtars_shipped/get-cart-product-data' ) ) {

                $item_data = apply_filters( 'wtars_shipped/get-cart-product-data', $item_data, $product, $item_key );
            }
            return $item_data;
        }

        private function get_totals_data( $cart_data, $cart ) {

            $cart_data[ 'totals' ] = $cart->get_totals();

            if ( has_filter( 'wtars_shipped/get-cart-totals' ) ) {

                $cart_data[ 'totals' ] = apply_filters( 'wtars_shipped/get-cart-totals', $cart_data[ 'totals' ], $cart );
            }

            return self::get_customer( $cart_data, $cart );
        }

        private function get_customer( $cart_data, $cart ) {

            $cart_data[ 'customer' ] = array();

            $customer = WC()->session->get( 'customer', false );

            if ( $customer ) {

                $cart_data[ 'customer' ] = array(
                    'id' => $customer[ 'id' ],
                    'email' => $customer[ 'email' ],
                );
            }

            if ( has_filter( 'wtars_shipped/get-cart-customer' ) ) {
                $cart_data[ 'customer' ] = apply_filters( 'wtars_shipped/get-cart-customer', $cart_data[ 'customer' ], $cart );
            }

            return $cart_data;
        }

        private function get_parent_product( $parent_id ) {

            if ( isset( $this->parent_products[ $parent_id ] ) ) {

                return $this->parent_products[ $parent_id ];
            }

            $this->parent_products[ $parent_id ] = wc_get_product( $parent_id );

            return $this->parent_products[ $parent_id ];
        }

        private function get_cart() {

            if ( method_exists( WC()->cart, 'is_empty' ) && WC()->cart->is_empty() ) {

                return false;
            }

            return WC()->cart;
        }

    }

}