<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Cart_Fees_Engine' ) ) {

    class WTARS_Shipped_Cart_Fees_Engine {

        private $fee_cache;

        public function __construct() {

            $this->fee_cache = new WTARS_Shipped_Cart_Fees_Cache();
            add_filter( 'wtars_shipped/process-data', array( $this, 'process_data' ), 30, 1 );
        }

        public function process_data( $data ) {

            //shipping instance needs to be set
            if ( !isset( $data[ 'wc' ][ 'instance_id' ] ) ) {
                return $data;
            }

            $instance_id = $data[ 'wc' ][ 'instance_id' ];

            // check for cached or in memory data
            if ( isset( $data[ 'fees' ][ $instance_id ] ) ) {
                return $data;
            }

            //calculate handling fees
            $data = $this->calculate_fees( $data, $instance_id );

            //store cache data
            if ( isset( $data[ 'fees' ][ $instance_id ] ) ) {
                $this->fee_cache->store_cache( $data[ 'fees' ], $instance_id );
            } else {
                $this->fee_cache->store_cache( array(), $instance_id );
            }

            return $data;
        }

        public function calculate_fees( $data, $instance_id ) {
            
            //do not calculate fees if method is not available            
            $is_available = true;

            if ( isset( $data[ 'is_availables' ][ $instance_id ] ) ) {
                $is_available = $data[ 'is_availables' ][ $instance_id ];
            }
            
            if ( !$is_available ) {
                return $data;
            }


            //get settings            
            $fees_settings = $this->get_fees_settings( $instance_id );

            //check if handling fees should be calculated
            if ( 'no' == $fees_settings[ 'mode' ] ) {
                return $data;
            }

            // go through the handling fees and apply them
            foreach ( $this->get_fee_options( $instance_id ) as $fee_option ) {

                $fee_option_head = array(
                    'apply_mode' => $fee_option[ 'apply_mode' ],
                    'enable' => $fee_option[ 'enable' ],
                    'fee_id' => $fee_option[ 'fee_id' ],
                    'inclusive_tax' => $fee_option[ 'inclusive_tax' ],
                    'costs_limit' => $fee_option[ 'costs_limit' ],
                    'settings' => $fees_settings,
                    'instance_id' => $instance_id,
                    'module' => 'cart-fees',
                    'sub_module' => 'fees',
                );

                $fee_conditions = $fee_option[ 'fee_conditions' ];
                $fee_conditions[ 'instance_id' ] = $instance_id;
                $fee_conditions[ 'module' ] = 'cart-fees';
                $fee_conditions[ 'sub_module' ] = 'fees';

                if ( !$this->can_apply_fee( $fee_option_head, $fee_conditions, $data ) ) {
                    continue;
                }

                $cost = $this->calculate_cost( $fee_option, $instance_id, $data );

                if ( has_filter( 'wtars_shipped/cart-fees/calculated-cost' ) ) {
                    $cost = apply_filters( 'wtars_shipped/cart-fees/calculated-cost', $cost, $fee_option[ 'fee_id' ], $data );
                }

                $title = '';
                if ( isset( $fee_option[ 'title' ] ) ) {
                    $title = $fee_option[ 'title' ];
                }

                $tooltip = '';
                if ( isset( $fee_option[ 'tooltip' ] ) ) {
                    $tooltip = $fee_option[ 'tooltip' ];
                }

                $fee = array(
                    'title' => ('' != $title) ? $title : esc_html__( 'Handling Fee', 'table-rate-shipping-rates' ),
                    'fee_method' => $fee_option[ 'fee_method' ],
                    'tooltip' => $tooltip,
                    'shipping_rates' => $fee_option[ 'shipping_rates' ],
                    'compare' => $fee_option[ 'compare' ],
                    'inclusive_tax' => $fee_option[ 'inclusive_tax' ],
                    'cost' => $cost[ 'cost' ],
                    'tax_total' => $cost[ 'tax_total' ],
                    'taxes' => $cost[ 'taxes' ],
                );

                if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                    $data = WTARS_Shipped_Premium_Cart_Fees_Engine::add_fee( $data, $fee, $fee_option_head );
                } else {
                    $data = $this->add_fee( $data, $fee, $fee_option_head );
                }
            }

            if ( isset( $data[ 'fees' ][ $instance_id ] ) && has_filter( 'wtars_shipped/cart-fees/calculated-fees' ) ) {
                $data[ 'fees' ][ $instance_id ] = apply_filters( 'wtars_shipped/cart-fees/calculated-fees', $data[ 'fees' ][ $instance_id ], $data );
            }

            return $data;
        }

        private function calculate_cost( $fee_option, $instance_id, $data ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return WTARS_Shipped_Premium_Cart_Fees_Engine::calculate_costs( $fee_option, $instance_id, $data );
            }

            $costs_args = $this->get_valid_costs_args( $this->get_costs_args_from_fee_option( $fee_option, $instance_id ), $data );

            $costs = WTARS_Shipped_Cost_Types::calculate_costs( array(), $costs_args, $data );

            $merge_args = array(
                'fee_id' => $fee_option[ 'fee_id' ],
                'instance_id' => $instance_id,
                'module' => 'cart-fees',
                'sub_module' => 'fees',
            );

            $cost = WTARS_Shipped_Cost_Types::merge_costs( $costs, $merge_args );

            if ( has_filter( 'wtars_shipped/cart-fees/calculated-base-cost' ) ) {
                $cost = apply_filters( 'wtars_shipped/cart-fees/calculated-base-cost', $cost, $fee_option[ 'fee_id' ], $data );
            }

            return $cost;
        }

        private function get_costs_args_from_fee_option( $fee_option, $instance_id ) {

            $costs_args = array();

            if ( isset( $fee_option[ 'base_costs' ] ) ) {

                $costs_args = $fee_option[ 'base_costs' ];
            }

            $arg_keys = array_keys( $costs_args );

            $tax_type = $this->get_tax_type( $instance_id );

            foreach ( $arg_keys as $key ) {

                $costs_args[ $key ][ 'instance_id' ] = $instance_id;
                $costs_args[ $key ][ 'module' ] = 'cart-fees';
                $costs_args[ $key ][ 'sub_module' ] = 'base-costs';
                $costs_args[ $key ][ 'fee_id' ] = $fee_option[ 'fee_id' ];
                $costs_args[ $key ][ 'tax_type' ] = $tax_type;
                $costs_args[ $key ][ 'inclusive_tax' ] = $fee_option[ 'inclusive_tax' ];
            }

            return $costs_args;
        }

        private function get_valid_costs_args( $costs_args, $data ) {

            $args = array();
            foreach ( $costs_args as $key => $cost_args ) {

                if ( !isset( $cost_args[ 'conditions' ] ) ) {

                    $args[ $key ] = $cost_args;
                    continue;
                }

                if ( !WTARS_Shipped_Cost_Types::can_calculate_cost( $cost_args, $data ) ) {
                    continue; //do nothing
                }

                $args[ $key ] = $cost_args;

                unset( $args[ $key ][ 'set_conditions' ] );
                unset( $args[ $key ][ 'conditions' ] );
            }

            return $args;
        }

        private function can_apply_fee( $fee_option_head, $fee_conditions, $data ) {

            // allows active fee only

            if ( 'no' == $fee_option_head[ 'enable' ] ) {
                return false;
            }

            $bool_val = true;

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $bool_val = WTARS_Shipped_Premium_Cart_Fees_Engine::can_apply_fee( $fee_option_head, $data );
            }

            // allows other plugins to check if the fee should apply

            if ( has_filter( 'wtars_shipped/cart-fees/can-apply-fee' ) ) {
                $bool_val = apply_filters( 'wtars_shipped/cart-fees/can-apply-fee', $bool_val, $fee_option_head, $data );
            }

            // no need for validations
            if ( false == $bool_val ) {
                return $bool_val;
            }

            // validate fee conditions

            $bool_val = WTARS_Shipped_Condition_Types::validate_conditions( $fee_conditions, $data );


            return $bool_val;
        }

        private function add_fee( $data, $fee, $fee_option_head ) {

            $instance_id = $fee_option_head[ 'instance_id' ];
            
            $data[ 'fees' ][ $instance_id ][ $fee_option_head[ 'fee_id' ] ] = $fee;

            // allows other plugins to modify fee data once added
            if ( has_filter( 'wtars_shipped/cart-fees/added-fee' ) ) {
                if ( isset( $data[ 'fees' ][ $instance_id ][ $fee_option_head[ 'fee_id' ] ] ) ) {
                    $data[ 'fees' ][ $instance_id ][ $fee_option_head[ 'fee_id' ] ] = apply_filters( 'wtars_shipped/cart-fees/added-fee', $data[ 'fees' ][ $instance_id ][ $fee_option_head[ 'fee_id' ] ], $instance_id, $fee_option_head );
                }
            }

            return $data;
        }

        private function get_tax_type( $instance_id ) {

            $default = array(
                'tax_type' => 'ship_rates',
            );

            $shipping = WTARS_Shipped::get_option( 'cart_fee_settings', $instance_id, $default );

            return $shipping[ 'tax_type' ];
        }

        private function get_fees_settings( $instance_id ) {

            $default = array(
                'mode' => 'no',
            );

            return WTARS_Shipped::get_option( 'cart_fees_settings', $instance_id, $default );
        }

        private function get_fee_options( $instance_id ) {

            $fee_options = array();

            foreach ( WTARS_Shipped::get_option( 'cart_fees', $instance_id, array() ) as $fee_option ) {

                $fee_options[] = $fee_option;
            }

            return $fee_options;
        }

    }

}
