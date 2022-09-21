<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Conditions_Order' ) ) {

    class WTARS_Shipped_Conditions_Order {

        public function __construct() {
          add_filter( 'wtars_shipped/validate-package_quantity-condition', array( $this, 'validate_quantity' ), 10, 2 );  
        }

        public function validate_quantity( $condition, $data ) {

            $quantity = $this->get_quantity( $data );

            $rule_compare = $condition[ 'compare' ];

            if ( '--' == $rule_compare ) {

                $rule_from_quantity = 0;

                if ( is_numeric( $condition[ 'from_quantity' ] ) ) {

                    $rule_from_quantity = $condition[ 'from_quantity' ];
                }

                $rule_to_quantity = 1;

                if ( is_numeric( $condition[ 'to_quantity' ] ) ) {

                    $rule_to_quantity = $condition[ 'to_quantity' ];
                }

                $from_is_valid = WTARS_Shipped_Validation_Util::validate_value( '>=', $quantity, $rule_from_quantity, 'no' );

                $to_is_valid = WTARS_Shipped_Validation_Util::validate_value( '<=', $quantity, $rule_to_quantity, 'no' );

                return (true == $from_is_valid && true == $to_is_valid);
            }
            
            
            $rule_quantity = 0;

            if ( is_numeric( $condition[ 'quantity' ] ) ) {

                $rule_quantity = $condition[ 'quantity' ];
            }

            return WTARS_Shipped_Validation_Util::validate_value( $rule_compare, $quantity, $rule_quantity, 'no' );
        }

        private function get_quantity( $data ) {

            $quantity = 0;

            if ( !isset( $data[ 'wc' ][ 'package' ][ 'items' ] ) ) {
                return $quantity;
            }

            foreach ( $data[ 'wc' ][ 'package' ][ 'items' ] as $package_item ) {
                $quantity += $package_item[ 'quantity' ];
            }

            return $quantity;
        }

    }

    new WTARS_Shipped_Conditions_Order();
}