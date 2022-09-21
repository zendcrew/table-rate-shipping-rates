<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Shipping_Rates_Engine' ) ) {

    class WTARS_Shipped_Shipping_Rates_Engine {

        private $rate_cache;

        public function __construct() {

            $this->rate_cache = new WTARS_Shipped_Shipping_Rates_Cache();
            add_filter( 'wtars_shipped/process-data', array( $this, 'process_data' ), 20, 1 );
        }

        public function process_data( $data ) {

            //shipping instance needs to be set
            if ( !isset( $data[ 'wc' ][ 'instance_id' ] ) ) {
                return $data;
            }

            $instance_id = $data[ 'wc' ][ 'instance_id' ];

            // check for cached or in memory data
            if ( isset( $data[ 'rates' ][ $instance_id ] ) ) {
                return $data;
            }

            //calculate shipping rates
            $data = $this->calculate_rates( $data, $instance_id );

            //store cache data
            if ( isset( $data[ 'rates' ][ $instance_id ] ) ) {
                $this->rate_cache->store_cache( $data[ 'rates' ], $instance_id );
            } else {
                $this->rate_cache->store_cache( array(), $instance_id );
            }

            return $data;
        }

        private function calculate_rates( $data, $instance_id ) {

            //do not calculate rates if method is not available
            $is_available = true;

            if ( isset( $data[ 'is_availables' ][ $instance_id ] ) ) {
                $is_available = $data[ 'is_availables' ][ $instance_id ];
            }

            if ( !$is_available ) {
                return $data;
            }


            //get settings
            $rates_settings = $this->get_rates_settings( $instance_id );

            //check if shipping rates should be calculated
            if ( 'no' == $rates_settings[ 'mode' ] ) {
                return $data;
            }


            // go through the shipping rates and apply them
            foreach ( $this->get_rate_options( $instance_id ) as $rate_option ) {

                $rate_option_head = array(
                    'apply_mode' => $rate_option[ 'apply_mode' ],
                    'enable' => $rate_option[ 'enable' ],
                    'rate_id' => $rate_option[ 'rate_id' ],
                    'inclusive_tax' => $rate_option[ 'inclusive_tax' ],
                    'costs_limit' => $rate_option[ 'costs_limit' ],
                    'settings' => $rates_settings,
                    'instance_id' => $instance_id,
                    'module' => 'shipping-rates',
                    'sub_module' => 'rates',
                );

                $rate_conditions = $rate_option[ 'rate_conditions' ];
                $rate_conditions[ 'instance_id' ] = $instance_id;
                $rate_conditions[ 'module' ] = 'shipping-rates';
                $rate_conditions[ 'sub_module' ] = 'rates';

                if ( !$this->can_apply_rate( $rate_option_head, $rate_conditions, $data ) ) {
                    continue;
                }

                $cost = $this->calculate_cost( $rate_option, $instance_id, $data );

                if ( has_filter( 'wtars_shipped/shipping-rates/calculated-cost' ) ) {
                    $cost = apply_filters( 'wtars_shipped/shipping-rates/calculated-cost', $cost, $rate_option[ 'rate_id' ], $data );
                }

                $rate = array(
                    'title' => ('' != $rate_option[ 'title' ]) ? $rate_option[ 'title' ] : esc_html__( 'Shipping Rate', 'table-rate-shipping-rates' ),
                    'desc' => $rate_option[ 'desc' ],
                    'tooltip' => $rate_option[ 'tooltip' ],
                    'is_default' => ($rate_option[ 'default' ] == 'yes'),
                    'inclusive_tax' => $rate_option[ 'inclusive_tax' ],
                    'cost' => $cost[ 'cost' ],
                    'tax_total' => $cost[ 'tax_total' ],
                    'taxes' => $cost[ 'taxes' ],
                );

                if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                    $data = WTARS_Shipped_Premium_Shipping_Rates_Engine::add_rate( $data, $rate, $rate_option_head );
                } else {
                    $data = $this->add_rate( $data, $rate, $rate_option_head );
                }
            }

            if ( isset( $data[ 'rates' ][ $instance_id ] ) && has_filter( 'wtars_shipped/shipping-rates/calculated-rates' ) ) {
                $data[ 'rates' ][ $instance_id ] = apply_filters( 'wtars_shipped/shipping-rates/calculated-rates', $data[ 'rates' ][ $instance_id ], $data );
            }

            return $data;
        }

        private function calculate_cost( $rate_option, $instance_id, $data ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return WTARS_Shipped_Premium_Shipping_Rates_Engine::calculate_costs( $rate_option, $instance_id, $data );
            }

            $costs_args = $this->get_valid_costs_args( $this->get_costs_args_from_rate_option( $rate_option, $instance_id ), $data );

            $costs = WTARS_Shipped_Cost_Types::calculate_costs( array(), $costs_args, $data );

            $merge_args = array(
                'rate_id' => $rate_option[ 'rate_id' ],
                'instance_id' => $instance_id,
                'module' => 'shipping-rates',
                'sub_module' => 'rates',
            );

            $cost = WTARS_Shipped_Cost_Types::merge_costs( $costs, $merge_args );

            if ( has_filter( 'wtars_shipped/shipping-rates/calculated-base-cost' ) ) {
                $cost = apply_filters( 'wtars_shipped/shipping-rates/calculated-base-cost', $cost, $rate_option[ 'rate_id' ], $data );
            }

            return $cost;
        }

        private function get_costs_args_from_rate_option( $rate_option, $instance_id ) {

            $costs_args = array();

            if ( isset( $rate_option[ 'base_costs' ] ) ) {

                $costs_args = $rate_option[ 'base_costs' ];
            }

            $arg_keys = array_keys( $costs_args );

            $tax_type = $this->get_tax_type( $instance_id );

            foreach ( $arg_keys as $key ) {

                $costs_args[ $key ][ 'instance_id' ] = $instance_id;
                $costs_args[ $key ][ 'module' ] = 'shipping-rates';
                $costs_args[ $key ][ 'sub_module' ] = 'base-costs';
                $costs_args[ $key ][ 'rate_id' ] = $rate_option[ 'rate_id' ];
                $costs_args[ $key ][ 'tax_type' ] = $tax_type;
                $costs_args[ $key ][ 'inclusive_tax' ] = $rate_option[ 'inclusive_tax' ];
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

        private function can_apply_rate( $rate_option_head, $rate_conditions, $data ) {

            // allows active rates only

            if ( 'no' == $rate_option_head[ 'enable' ] ) {
                return false;
            }

            $bool_val = true;

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $bool_val = WTARS_Shipped_Premium_Shipping_Rates_Engine::can_apply_rate( $rate_option_head, $data );
            }


            // allows other plugins to check if the rate should apply

            if ( has_filter( 'wtars_shipped/shipping-rates/can-apply-rate' ) ) {
                $bool_val = apply_filters( 'wtars_shipped/shipping-rates/can-apply-rate', $bool_val, $rate_option_head, $data );
            }

            // no need for validations
            if ( false == $bool_val ) {
                return $bool_val;
            }

            // validate rate conditions

            $bool_val = WTARS_Shipped_Condition_Types::validate_conditions( $rate_conditions, $data );


            return $bool_val;
        }

        private function add_rate( $data, $rate, $rate_option_head ) {

            $instance_id = $rate_option_head[ 'instance_id' ];

            $data[ 'rates' ][ $instance_id ][ $rate_option_head[ 'rate_id' ] ] = $rate;

            // allows other plugins to modify rate data once added
            if ( has_filter( 'wtars_shipped/shipping-rates/added-rate' ) ) {
                if ( isset( $data[ 'rates' ][ $instance_id ][ $rate_option_head[ 'rate_id' ] ] ) ) {
                    $data[ 'rates' ][ $instance_id ][ $rate_option_head[ 'rate_id' ] ] = apply_filters( 'wtars_shipped/shipping-rates/added-rate', $data[ 'rates' ][ $instance_id ][ $rate_option_head[ 'rate_id' ] ], $instance_id, $rate_option_head );
                }
            }

            return $data;
        }

        private function get_tax_type( $instance_id ) {

            $default = array(
                'tax_type' => 'ship_rates',
            );

            $shipping = WTARS_Shipped::get_option( 'shipping_rate_settings', $instance_id, $default );

            return $shipping[ 'tax_type' ];
        }

        private function get_rates_settings( $instance_id ) {

            $default = array(
                'mode' => 'no',
            );

            return WTARS_Shipped::get_option( 'shipping_rates_settings', $instance_id, $default );
        }

        private function get_rate_options( $instance_id ) {

            $rate_options = array();

            foreach ( WTARS_Shipped::get_option( 'shipping_rates', $instance_id, array() ) as $rate_option ) {

                $rate_options[] = $rate_option;
            }

            return $rate_options;
        }

    }

}