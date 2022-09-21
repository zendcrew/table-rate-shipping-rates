<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_WMC_Cart' ) ) {

    class WTARS_Shipped_WMC_Cart {

        public function __construct() {

            add_filter( 'wtars_shipped/get-cart-totals', array( $this, 'get_cart_totals' ), 10, 2 );
        }

        public function get_cart_totals( $cart_totals, $cart ) {

            $converter = $this->get_converter();

            $cart_totals[ 'subtotal' ] = $converter->revert_amount( $cart_totals[ 'subtotal' ] );
            $cart_totals[ 'subtotal_tax' ] = $converter->revert_amount( $cart_totals[ 'subtotal_tax' ] );
            $cart_totals[ 'discount_total' ] = $converter->revert_amount( $cart_totals[ 'discount_total' ] );
            $cart_totals[ 'discount_tax' ] = $converter->revert_amount( $cart_totals[ 'discount_tax' ] );
            $cart_totals[ 'cart_contents_total' ] = $converter->revert_amount( $cart_totals[ 'cart_contents_total' ] );
            $cart_totals[ 'cart_contents_tax' ] = $converter->revert_amount( $cart_totals[ 'cart_contents_tax' ] );
            $cart_totals[ 'cart_contents_taxes' ] = $converter->revert_amount_list( $cart_totals[ 'cart_contents_taxes' ] );

            return $cart_totals;
        }

        private function get_converter() {

            return WTARS_Shipped_WMC::get_instance();
        }

    }

    new WTARS_Shipped_WMC_Cart();
}
