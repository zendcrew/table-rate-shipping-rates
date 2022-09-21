<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Cost_Types' ) ) {
    WTARS_Shipped_Inc::recursive_require( dirname( __FILE__ ), array( 'cost-types.php' ) );

    class WTARS_Shipped_Cost_Types {

        private static $instance = null;

        private static function get_instance() {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function calculate_costs( $costs, $costs_args, $data ) {

            // go through each of the cost args and apply them
            foreach ( $costs_args as $cost_args ) {

                $cost = apply_filters( 'wtars_shipped/calculate-' . $cost_args[ 'cost_type' ] . '-cost', array( 'cost' => 0 ), $cost_args, $data );

                //calculates tax if not calculated
                if ( !isset( $cost[ 'tax_total' ] ) ) {

                    $tax_args = array(
                        'tax_class' => $cost_args[ 'taxable' ],
                        'tax_type' => $cost_args[ 'tax_type' ],
                        'inclusive_tax' => ('yes' == $cost_args[ 'inclusive_tax' ])
                    );

                    $cost_taxes = self::get_instance()->calculate_cost_tax( $cost[ 'cost' ], $tax_args, $data );

                    $cost[ 'tax_total' ] = array_sum( $cost_taxes );

                    $cost[ 'taxes' ] = $cost_taxes;
                }

                if ( true == ('yes' == $cost_args[ 'inclusive_tax' ]) && $cost[ 'tax_total' ] > 0 ) {

                    $cost[ 'cost' ] = $cost[ 'cost' ] - $cost[ 'tax_total' ];
                }

                $cost[ 'add_type' ] = isset( $cost_args[ 'add_type' ] ) ? $cost_args[ 'add_type' ] : 'add';

                // allows other plugins to modify the processed cost
                if ( has_filter( 'wtars_shipped/calculated-cost' ) ) {
                    $cost = apply_filters( 'wtars_shipped/calculated-cost', $cost, $cost_args, $data );
                }

                $costs[] = $cost;
            }

            return $costs;
        }

        public static function prepare_cost( $cost, $calc_cost, $cost_args = array(), $package_items = array() ) {

            if ( 0 >= $calc_cost ) {
                return $cost;
            }

            $cost[ 'cost' ] = $calc_cost;

            if ( !count( $package_items ) ) {
                return $cost;
            }

            if ( !count( $cost_args ) ) {
                return $cost;
            }

            $tax_args = array(
                'tax_class' => $cost_args[ 'taxable' ],
                'tax_type' => $cost_args[ 'tax_type' ],
                'inclusive_tax' => ('yes' == $cost_args[ 'inclusive_tax' ])
            );

            $cost_taxes = WTARS_Shipped_Cost_Tax::calculate( $calc_cost, $tax_args, $package_items );
            $cost[ 'tax_total' ] = array_sum( $cost_taxes );
            $cost[ 'taxes' ] = $cost_taxes;

            return $cost;
        }

        public static function merge_costs( $costs, $merge_args ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $calc_cost = WTARS_Shipped_Premium_Cost_Types::merge_costs( $costs );
            } else {

                $calc_cost = array(
                    'cost' => 0,
                    'tax_total' => 0,
                    'taxes' => array(),
                );

                foreach ( $costs as $cost ) {
                    
                    $calc_cost[ 'cost' ] += $cost[ 'cost' ];
                    $calc_cost[ 'tax_total' ] += $cost[ 'tax_total' ];

                    $calc_cost[ 'taxes' ] = self::get_instance()->add_taxes( $calc_cost[ 'taxes' ], $cost[ 'taxes' ] );
                }
            }

            if ( $calc_cost[ 'cost' ] <= 0 ) {
                $calc_cost[ 'cost' ] = 0;
                $calc_cost[ 'taxes' ] = array();
            }

            if ( $calc_cost[ 'tax_total' ] <= 0 ) {
                $calc_cost[ 'tax_total' ] = 0;
                $calc_cost[ 'taxes' ] = array();
            }

            if ( !isset( $calc_cost[ 'taxes' ] ) ) {
                $calc_cost[ 'taxes' ] = array();
            }

            // allows other plugins to modify the merged costs
            if ( has_filter( 'wtars_shipped/merged-cost' ) ) {
                $calc_cost = apply_filters( 'wtars_shipped/merged-cost', $calc_cost, $costs, $merge_args );
            }

            return $calc_cost;
        }

        public static function can_calculate_cost( $cost_args, $data ) {

            if ( 'no' == $cost_args[ 'set_conditions' ] ) {
                return true;
            }

            if ( !isset( $cost_args[ 'conditions' ] ) ) {
                return true;
            }

            $conditions = $cost_args[ 'conditions' ];

            for ( $key = 0; $key < count( $conditions ); $key++ ) {

                $conditions[ $key ][ 'module' ] = $cost_args[ 'module' ];
                $conditions[ $key ][ 'sub_module' ] = $cost_args[ 'sub_module' ];
                
            }

            return WTARS_Shipped_Condition_Types::validate_cost_conditions( $conditions, $data );
        }

        private function calculate_cost_tax( $cost, $tax_args, $data ) {

            $package_items = WTARS_Shipped_Package::get_package_items_data( $data[ 'wc' ][ 'package' ][ 'items' ], $data[ 'wc' ][ 'cart' ][ 'items' ] );

            if ( $tax_args[ 'tax_class' ] == '--0' ) {
                return WTARS_Shipped_Cost_Tax::calculate( $cost, $tax_args, $package_items );
            } else {
                return WTARS_Shipped_Cost_Tax::calculate( $cost, $tax_args );
            }
        }

        private function add_taxes( $prev_cost_taxes, $new_cost_taxes ) {

            foreach ( $new_cost_taxes as $key => $tax ) {
                if ( isset( $prev_cost_taxes[ $key ] ) ) {
                    $prev_cost_taxes[ $key ] += $tax;
                } else {
                    $prev_cost_taxes[ $key ] = $tax;
                }
            }
            return $prev_cost_taxes;
        }

    }

}
