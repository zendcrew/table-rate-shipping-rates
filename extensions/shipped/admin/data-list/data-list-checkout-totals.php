<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List_Checkout_Totals' ) ) {


    class WTARS_Shipped_Admin_Data_List_Checkout_Totals {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-data-list-checkout_totals', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            global $wtars_shipped;

            $options = array();

            if ( isset( $wtars_shipped[ 'checkout_totals' ] ) ) {
                $options = $wtars_shipped[ 'checkout_totals' ];
            } else {
                $options = self::get_default_options();
            }

            foreach ( $options as $option ) {
                $result[ $option[ 'option_id' ] ] = $option[ 'title' ];
            }

            return $result;
        }

        private static function get_default_options() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                
                return array(
                    array(
                        'title' => esc_html__( 'Cart Subtotals', 'table-rate-shipping-rates' ),
                        'option_id' => '2234343',
                    ),
                    array(
                        'title' => esc_html__( 'Package Totals', 'table-rate-shipping-rates' ),
                        'option_id' => '2234344',
                    ),
                );
            }

            return array(
                array(
                    'title' => esc_html__( 'Order Totals', 'table-rate-shipping-rates' ),
                    'option_id' => '2234343',
                ),
            );
        }

    }

    new WTARS_Shipped_Admin_Data_List_Checkout_Totals();
}