<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List_Shipping_Rates' ) ) {


    class WTARS_Shipped_Admin_Data_List_Shipping_Rates {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-data-list-shipping_rates', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            global $wtars_shipped;

            if ( isset( $wtars_shipped[ 'shipping_rates' ] ) ) {

                foreach ( $wtars_shipped[ 'shipping_rates' ] as $shipping_rate ) {

                    $title = esc_html__( 'Shipping Rate', 'table-rate-shipping-rates' );

                    if ( isset( $shipping_rate[ 'title' ] ) && '' != $shipping_rate[ 'title' ] ) {

                        $title = $shipping_rate[ 'title' ];
                    }

                    $rate_id = $shipping_rate[ 'rate_id' ];

                    $result[ $rate_id ] = $title;
                }
            }


            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List_Shipping_Rates();
}