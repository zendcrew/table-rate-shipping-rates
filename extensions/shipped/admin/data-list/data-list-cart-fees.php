<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List_Cart_Fees' ) ) {


    class WTARS_Shipped_Admin_Data_List_Cart_Fees {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-data-list-cart_fees', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            global $wtars_shipped;

            if ( isset( $wtars_shipped[ 'cart_fees' ] ) ) {

                foreach ( $wtars_shipped[ 'cart_fees' ] as $cart_fee ) {

                    $title = esc_html__( 'Handling Fee', 'table-rate-shipping-rates' );

                    if ( isset( $cart_fee[ 'title' ] ) && '' != $cart_fee[ 'title' ] ) {

                        $title = $cart_fee[ 'title' ];
                    }

                    $fee_id = $cart_fee[ 'fee_id' ];

                    $result[ $fee_id ] = $title;
                }
            }


            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List_Cart_Fees();
}