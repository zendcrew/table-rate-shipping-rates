<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List_Checkout_Message_Modes' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {


    class WTARS_Shipped_Admin_Data_List_Checkout_Message_Modes {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-data-list-checkout_messages_modes', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            $result[ 'all' ] = esc_html__( 'Apply all valid notifications', 'table-rate-shipping-rates' );
            
            $result[ 'no' ] = esc_html__( 'Do not apply any notification', 'table-rate-shipping-rates' );

            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List_Checkout_Message_Modes();
}