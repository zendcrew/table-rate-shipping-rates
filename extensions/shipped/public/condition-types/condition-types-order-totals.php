<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Conditions_Order_Totals' ) ) {

    class WTARS_Shipped_Conditions_Order_Totals {

        private $prefix = 'order_total-';

        public function __construct() {

            add_filter( 'wtars_shipped/cache-data', array( $this, 'init' ), 1 );
        }

        public function init( $data ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return $data;
            }
            add_filter( 'wtars_shipped/validate-' . $this->prefix . '2234343-condition', array( $this, 'validate_totals' ), 10, 2 );

            return $data;
        }

        public function validate_totals( $condition, $data ) {

            $order_totals = $this->get_order_totals( $condition, $data );

            $rule_compare = $condition[ 'compare' ];

            if ( '--' == $rule_compare ) {

                $rule_from_totals = 0;

                if ( is_numeric( $condition[ 'from_totals' ] ) ) {

                    $rule_from_totals = $condition[ 'from_totals' ];
                }

                $rule_to_totals = .01;

                if ( is_numeric( $condition[ 'to_totals' ] ) ) {

                    $rule_to_totals = $condition[ 'to_totals' ];
                }

                $from_is_valid = WTARS_Shipped_Validation_Util::validate_value( '>=', $order_totals, $rule_from_totals, 'no' );

                $to_is_valid = WTARS_Shipped_Validation_Util::validate_value( '<=', $order_totals, $rule_to_totals, 'no' );

                return (true == $from_is_valid && true == $to_is_valid);
            }

            $rule_totals = 0;

            if ( is_numeric( $condition[ 'totals' ] ) ) {

                $rule_totals = $condition[ 'totals' ];
            }

            return WTARS_Shipped_Validation_Util::validate_value( $rule_compare, $order_totals, $rule_totals, 'no' );
        }

        private function get_order_totals( $condition, $data ) {

            $instance_id = $data[ 'wc' ][ 'instance_id' ];
            $option_id = preg_replace( '/' . $this->prefix . '/', '', $condition[ 'condition_type' ] );

            $args = array(
                'module' => $condition[ 'module' ],
                'option_id' => $option_id,
                'instance_id' => $instance_id
            );

            $order_totals = WTARS_Shipped_Total_Types::get_totals( $args, $data );

            return $order_totals;
        }

    }

    new WTARS_Shipped_Conditions_Order_Totals();
}
