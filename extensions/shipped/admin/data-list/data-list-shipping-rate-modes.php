<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List_Shipping_Rate_Modes' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {


    class WTARS_Shipped_Admin_Data_List_Shipping_Rate_Modes {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-data-list-shipping_rate_modes', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            $result[ 'all' ] = esc_html__( 'Apply all valid rates', 'table-rate-shipping-rates' );
            
            $result[ 'no' ] = esc_html__( 'Do not apply any rate', 'table-rate-shipping-rates' );

            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List_Shipping_Rate_Modes();
}