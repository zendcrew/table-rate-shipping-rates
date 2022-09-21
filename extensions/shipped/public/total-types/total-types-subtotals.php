<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Total_Types_Subtotals' ) ) {

    class WTARS_Shipped_Total_Types_Subtotals {

        public function calc_totals( $totals, $option, $data ) {
            
            $subtotals = $this->get_subtotals( $data );

            if ( 'subtotal' == $option ) {
                $totals += $subtotals[ 'subtotal' ];
            }
            
            if ( 'subtotal_tax' == $option ) {
                $totals += $subtotals[ 'subtotal_tax' ];
            }

            return $totals;
        }

        private function get_subtotals( $data ) {

            $subtotals = array(
                'subtotal' => 0,
                'subtotal_tax' => 0
            );

            if ( !isset( $data[ 'wc' ]['cart'][ 'totals' ][ 'subtotal' ] ) ) {
                return $subtotals;
            }

            $subtotals[ 'subtotal' ] = $data[ 'wc' ]['cart'][ 'totals' ][ 'subtotal' ];
            $subtotals[ 'subtotal_tax' ] = $data[ 'wc' ]['cart'][ 'totals' ][ 'subtotal_tax' ];

            return $subtotals;
        }

    }

}