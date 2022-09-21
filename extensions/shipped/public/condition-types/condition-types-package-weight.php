<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Conditions_Package_Weight' ) ) {

    class WTARS_Shipped_Conditions_Package_Weight {

        private $prefix = 'package_weight-';

        public function __construct() {

            add_filter( 'wtars_shipped/cache-data', array( $this, 'init' ), 1 );
        }

        public function init( $data ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return $data;
            }

            add_filter( 'wtars_shipped/validate-' . $this->prefix . '2234343-condition', array( $this, 'validate_weights' ), 10, 2 );

            return $data;
        }

        public function validate_weights( $condition, $data ) {

            $total_weight = $this->get_total_weight( $data );

            $rule_compare = $condition[ 'compare' ];

            if ( '--' == $rule_compare ) {

                $rule_from_weight = 0;

                if ( is_numeric( $condition[ 'from_weight' ] ) ) {

                    $rule_from_weight = $condition[ 'from_weight' ];
                }

                $rule_to_weight = .5;

                if ( is_numeric( $condition[ 'to_weight' ] ) ) {

                    $rule_to_weight = $condition[ 'to_weight' ];
                }

                $from_is_valid = WTARS_Shipped_Validation_Util::validate_value( '>=', $total_weight, $rule_from_weight, 'no' );

                $to_is_valid = WTARS_Shipped_Validation_Util::validate_value( '<=', $total_weight, $rule_to_weight, 'no' );

                return (true == $from_is_valid && true == $to_is_valid);
            }

            $rule_weight = 0;

            if ( is_numeric( $condition[ 'weight' ] ) ) {

                $rule_weight = $condition[ 'weight' ];
            }

            return WTARS_Shipped_Validation_Util::validate_value( $rule_compare, $total_weight, $rule_weight, 'no' );
        }

        private function get_total_weight( $data ) {

            if ( !isset( $data[ 'wc' ][ 'package' ][ 'items' ] ) ) {
                return 0;
            }

            $package_items = $this->get_package_items_data( $data[ 'wc' ][ 'package' ][ 'items' ], $data[ 'wc' ][ 'cart' ][ 'items' ] );

            $weight_calc = new WTARS_Shipped_Weight_Calculator();

            $total_weight = $weight_calc->get_total_weight( $package_items, array() );

            return $total_weight;
        }

        private function get_package_items_data( $package_items, $cart_items ) {

            foreach ( array_keys( $package_items ) as $key ) {
                
                $package_items[ $key ][ 'data' ] = $this->get_cart_item_data( $package_items[ $key ][ 'key' ], $cart_items );
            }

            return $package_items;
        }

        private function get_cart_item_data( $key, $cart_items ) {

            foreach ( $cart_items as $cart_item ) {
                if ( $key == $cart_item[ 'key' ] && isset( $cart_item[ 'data' ] ) ) {
                    return $cart_item[ 'data' ];
                }
            }

            return array();
        }

    }

    new WTARS_Shipped_Conditions_Package_Weight();
}