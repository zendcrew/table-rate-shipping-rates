<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Cost_Type_Order' ) ) {

    class WTARS_Shipped_Cost_Type_Order {

        public function __construct() {

            add_filter( 'wtars_shipped/calculate-fixed_cost-cost', array( $this, 'calculate_fixed_cost' ), 1, 3 );
            
        }

        public function calculate_fixed_cost( $cost, $cost_args, $data ) {

            $calc_cost = $cost_args[ 'cost' ];

            return WTARS_Shipped_Cost_Types::prepare_cost( $cost, $calc_cost );
        }

    }

    new WTARS_Shipped_Cost_Type_Order();
}