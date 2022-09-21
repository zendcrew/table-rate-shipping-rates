<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Cart_Fees' ) ) {
    WTARS_Shipped_Inc::recursive_require( dirname( __FILE__ ), array( 'cart-fees.php' ) );

    class WTARS_Shipped_Cart_Fees {

        public function process_fees( $package, $data, $instance_id ) {

            //check for calculated rates
            if ( !isset( $data[ 'rates' ][ $instance_id ] ) ) {
                return $package;
            }

            //check for calculated fees
            if ( !isset( $data[ 'fees' ][ $instance_id ] ) ) {
                return $package;
            }

            //get settings
            $settings = WTARS_Shipped::get_option( 'cart_fee_settings', $instance_id, array( 'show_on_cart' => 'yes' ) );

            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                return $package;
            }

            $package = apply_filters( 'wtars_shipped/process-cart-fees', $package, $data, $instance_id );

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return $package;
            }

            // rates cost with fees cost

            $rates = array();

            foreach ( $data[ 'fees' ][ $instance_id ] as $fee ) {

                $rates = $this->apply_rate_fee( $rates, $fee, $data[ 'rates' ][ $instance_id ], $instance_id );
            }

            return $this->update_package_rates( $package, $rates, $instance_id );
        }

        private function apply_rate_fee( $rates, $fee, $data_rates, $instance_id ) {

            $rate_args = array(
                'shipping_rates' => $fee[ 'shipping_rates' ],
                'compare' => $fee[ 'compare' ],
            );

            $fee_rates = $this->get_rates( $rate_args, $data_rates, $instance_id );


            foreach ( $fee_rates as $key => $fee_rate ) {

                if ( !isset( $rates[ $key ] ) ) {
                    $rates[ $key ] = $fee_rate;
                }

                $rate_fee = $this->add_fee_to_rate( $rates[ $key ], $fee );

                $rates[ $key ] = apply_filters( 'wtars_shipped/rate-fee', $rate_fee, $rates[ $key ], $fee, $instance_id );
            }

            return $rates;
        }

        private function update_package_rates( $package, $rates, $instance_id ) {

            foreach ( $rates as $rate_id => $rate ) {

                $shipping_rate_id = WTARS_Shipped::get_rate_client_id( $instance_id, $rate_id );

                if ( isset( $package[ 'rates' ][ $shipping_rate_id ] ) ) {

                    $package[ 'rates' ][ $shipping_rate_id ]->set_cost( apply_filters( 'wtars_shipped/rate-cost', $rate[ 'cost' ], $rate_id, $instance_id ) );
                    $package[ 'rates' ][ $shipping_rate_id ]->set_taxes( apply_filters( 'wtars_shipped/rate-taxes', $rate[ 'taxes' ], $rate_id, $instance_id ) );
                }
            }

            return $package;
        }

        private function add_fee_to_rate( $rate, $fee ) {

            $rate[ 'cost' ] += $fee[ 'cost' ];
            $rate[ 'tax_total' ] += $fee[ 'tax_total' ];
            $rate[ 'taxes' ] = $this->add_fee_taxes_to_rate( $rate[ 'taxes' ], $fee[ 'taxes' ] );

            return $rate;
        }

        private function add_fee_taxes_to_rate( $rate_taxes, $fee_taxes ) {

            $keys = array_keys( $fee_taxes );

            foreach ( $keys as $key ) {

                if ( !isset( $rate_taxes[ $key ] ) ) {
                    $rate_taxes[ $key ] = 0;
                }

                $rate_taxes[ $key ] += $fee_taxes[ $key ];
            }

            return $rate_taxes;
        }

        private function get_rates( $args, $data_rates, $instance_id ) {

            if ( !is_array( $args[ 'shipping_rates' ] ) ) {
                return $data_rates;
            }

            $rates = array();

            foreach ( $data_rates as $key => $data_rate ) {

                if ( WTARS_Shipped_Validation_Util::validate_value_list( $key, $args[ 'shipping_rates' ], $args[ 'compare' ] ) ) {

                    $rates[ $key ] = $data_rate;
                }
            }

            return $rates;
        }

    }

}