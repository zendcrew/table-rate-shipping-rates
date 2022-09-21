<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List_Checkout_Message_Methods' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {


    class WTARS_Shipped_Admin_Data_List_Checkout_Message_Methods {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-data-list-checkout_messages_methods', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            $result[ 'with_others' ] = esc_html__( 'Apply this and other notifications', 'table-rate-shipping-rates' );
            
            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List_Checkout_Message_Methods();
}