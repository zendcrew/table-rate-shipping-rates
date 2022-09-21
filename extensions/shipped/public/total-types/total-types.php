<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Total_Types' ) ) {

    WTARS_Shipped_Inc::recursive_require( dirname( __FILE__ ), array( 'total-types.php' ) );

    class WTARS_Shipped_Total_Types {

        private $totals_list = array();
        private static $instance = null;

        private static function get_instance() {
            if ( null == self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function get_totals( $args, $data ) {

            $this_obj = self::get_instance();

            $totals_key = $this_obj->get_args_key( $args );

            if ( isset( $this_obj->totals_list[ $totals_key ] ) ) {
                return $this_obj->totals_list[ $totals_key ];
            }

            $totals = 0;

            $options = $this_obj->get_option( $args );

            foreach ( $options as $option ) {
                switch ( $option ) {

                    case 'subtotal':
                    case 'subtotal_tax':
                        $cart_subtotals = new WTARS_Shipped_Total_Types_Subtotals();
                        $totals = $cart_subtotals->calc_totals( $totals, $option, $data );
                        break;

                    case 'package_total':
                    case 'package_total_tax':
                        $package_totals = new WTARS_Shipped_Total_Types_Package_Totals();
                        $totals = $package_totals->calc_totals( $totals, $option, $data );
                        break;

                    default:

                        if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                            $prem_totals = new WTARS_Shipped_Premium_Total_Types();
                            $totals = $prem_totals->get_totals( $totals, $option, $data );
                        }
                        break;
                }

                if ( has_filter( 'wtars_shipped/calculate-' . $option . '-totals' ) ) {
                    $totals = apply_filters( 'wtars_shipped/calculate-' . $option . '-totals', $totals, $data );
                }
            }

            $this_obj->totals_list[ $totals_key ] = $totals;

            return $totals;
        }

        private function get_option( $args ) {

            $options = array();

            $opts = WTARS_Shipped::get_option( 'checkout_totals', $args[ 'instance_id' ], $this->get_defaults() );

            foreach ( $opts as $opt ) {

                if ( isset( $opt[ 'include' ] ) ) {

                    $options[ $opt[ 'option_id' ] ] = $opt[ 'include' ];
                } else {

                    $options[ $opt[ 'option_id' ] ] = array();
                }
            }

            if ( isset( $options[ $args[ 'option_id' ] ] ) ) {

                return $options[ $args[ 'option_id' ] ];
            }

            return $options[ $opts[ 0 ][ 'option_id' ] ];
        }

        private function get_defaults() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                
                return array(
                    array(
                        'option_id' => '2234343',
                        'include' => array( 'subtotal', 'subtotal_tax' ),
                    ),
                    array(
                        'option_id' => '2234344',
                        'include' => array( 'package_total', 'package_total_tax' ),
                    ),
                );
            }
            
            return array(
                array(
                    'option_id' => '2234343',
                    'include' => array( 'subtotal', 'subtotal_tax' ),
                ),
            );
        }

        private function get_args_key( $args ) {
            return md5( wp_json_encode( $args ) );
        }

    }

}
    