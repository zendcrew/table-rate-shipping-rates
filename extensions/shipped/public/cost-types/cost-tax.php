<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Cost_Tax' ) ) {

    class WTARS_Shipped_Cost_Tax {

        private static function get_instance() {
            return new self();
        }

        public static function calculate( $cost, $tax_args, $package_items = array() ) {

            if ( 0 == $cost ) {
                return array();
            }

            if ( !wc_tax_enabled() ) {
                return array();
            }

            if ( 'no' == $tax_args[ 'tax_type' ] ) {
                return array();
            }

            if ( '--1' == $tax_args[ 'tax_class' ] ) {
                return array();
            } else if ( '--0' == $tax_args[ 'tax_class' ] ) {
                return self::get_instance()->calculate_based_on_items( $cost, $tax_args, $package_items );
            } else if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return WTARS_Shipped_Premium_Cost_Tax::calculate( $cost, $tax_args, $package_items );
            }

            return array();
        }

        private function calculate_based_on_items( $cost, $tax_args, $package_items ) {

            if ( count( $package_items ) == 0 ) {
                return array();
            }

            $total_qty = 0;
            $item_rates = array();

            // get item tax class and quantities
            foreach ( $package_items as $key => $package_item ) {

                if ( isset( $package_item[ 'data' ][ 'is_taxable' ] ) && $package_item[ 'data' ][ 'is_taxable' ] == true ) {
                    $item_rates[ $key ] = array(
                        'tax_class' => $package_item[ 'data' ][ 'tax_class' ],
                        'quantity' => $package_item[ 'quantity' ],
                    );
                    $total_qty += $package_item[ 'quantity' ];
                }
            }

            $sliced_costs = self::get_instance()->get_sliced_costs( $cost, $item_rates, $total_qty );
            $taxes = array();

            //calculate item taxes
            foreach ( $sliced_costs as $sliced_cost ) {
                $tax_args[ 'tax_class' ] = $sliced_cost[ 'tax_class' ];

                $tax_rates = WC_Tax::get_shipping_tax_rates( $tax_args[ 'tax_class' ] );

                $txs = WC_Tax::calc_tax( $sliced_cost[ 'price' ], $tax_rates, $tax_args[ 'inclusive_tax' ] );

                foreach ( $txs as $key => $tx ) {
                    if ( isset( $taxes[ $key ] ) ) {
                        $taxes[ $key ] += $tx;
                    } else {
                        $taxes[ $key ] = $tx;
                    }
                }
            }

            return array_map( array( $this, 'round_tax' ), $taxes );
        }

        private function get_sliced_costs( $cost, $item_rates, $total_quantity ) {

            if(0>=$total_quantity){
                return $item_rates;
            }
            
            // slices the total cost based on item quantities
            $sl_cost = array();

            $sl = WTARS_Shipped_Util::round_num( ($cost / $total_quantity ), wc_get_price_decimals() );


            for ( $i = 0; $i < $total_quantity; $i++ ) {

                if ( $cost - $sl >= 0 ) {
                    $sl_cost[] = $sl;
                    $cost = $cost - $sl;
                }
            }

            if ( WTARS_Shipped_Util::round_num( $cost, wc_get_price_decimals() ) > 0 ) {
                $sl_cost[] = WTARS_Shipped_Util::round_num( $cost, wc_get_price_decimals() );
            }

            if ( count( $sl_cost ) > $total_quantity && $total_quantity > 1 ) {
                $sl_cost[ $total_quantity - 1 ] = $sl_cost[ $total_quantity - 1 ] + $sl_cost[ $total_quantity ];
                unset( $sl_cost[ $total_quantity ] );
            }



            // merge costs based on quantities
            $index = 0;
            $keys = array_keys( $item_rates );
            foreach ( $keys as $key ) {
                if ( !isset( $item_rates[ $key ][ 'price' ] ) ) {
                    $item_rates[ $key ][ 'price' ] = 0;
                }
                for ( $i = 0; $i < $item_rates[ $key ][ 'quantity' ]; $i++ ) {

                    $item_rates[ $key ][ 'price' ] += $sl_cost[ $index ];

                    $index++;
                }
            }
            return $item_rates;
        }

        private function round_tax( $tax ) {
            return WTARS_Shipped_Util::round_num( $tax, wc_get_price_decimals() );
        }

    }

}