<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Weight_Calculator' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Weight_Calculator {

        public function get_total_weight( $package_items, $weight_args ) {

            $total_weight = 0;

            foreach ( $package_items as $package_item ) {

                $total_weight += $this->get_item_weight( $package_item );
            }

            return $total_weight;
        }

        public function get_item_weight( $package_item ) {

            if ( !isset( $package_item[ 'data' ][ 'weight' ] ) ) {

                return 0;
            }

            if ( !is_numeric( $package_item[ 'data' ][ 'weight' ] ) ) {

                return 0;
            }

            return $package_item[ 'data' ][ 'weight' ] * $package_item[ 'quantity' ];
        }

    }

}
