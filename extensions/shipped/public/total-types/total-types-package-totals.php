<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Total_Types_Package_Totals' ) ) {

    class WTARS_Shipped_Total_Types_Package_Totals {

        public function calc_totals( $totals, $option, $data ) {

            $package_totals = $this->get_totals( $data );

            if ( 'package_total' == $option ) {
                $totals += $package_totals[ 'total' ];
            }
            
            if ( 'package_total_tax' == $option ) {
                $totals += $package_totals[ 'total_tax' ];
            }

            return $totals;
        }

        private function get_totals( $data ) {

            $package_totals = array(
                'total' => 0,
                'total_tax' => 0
            );

            if ( !isset( $data[ 'wc' ][ 'package' ][ 'items' ] ) ) {
                return $package_totals;
            }

            foreach ( $data[ 'wc' ][ 'package' ][ 'items' ] as $item ) {

                $package_totals[ 'total' ] += $item[ 'line_subtotal' ];
                $package_totals[ 'total_tax' ] += $item[ 'line_subtotal_tax' ];
            }

            return $package_totals;
        }

    }

}
