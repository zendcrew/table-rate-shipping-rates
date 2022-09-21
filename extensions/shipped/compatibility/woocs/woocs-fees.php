<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_WOOCS_Fees' ) ) {

    class WTARS_Shipped_WOOCS_Fees {

        public function __construct() {

            add_filter( 'wtars_shipped/rate-fee', array( $this, 'convert_rate_fee' ), 10, 4 );
        }

        public function convert_rate_fee( $rate_fee, $rate, $fee, $instance_id ) {

            $converter = $this->get_converter();

            $rate_fee[ 'cost' ] = $converter->convert_amount( $rate_fee[ 'cost' ] );
            $rate_fee[ 'tax_total' ] = $converter->convert_amount( $rate_fee[ 'tax_total' ] );
            $rate_fee[ 'taxes' ] = $converter->convert_amount_list( $rate_fee[ 'taxes' ] );

            return $rate_fee;
        }

        private function get_converter() {

            return WTARS_Shipped_WOOCS::get_instance();
        }

    }

    new WTARS_Shipped_WOOCS_Fees();
}