<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List_Tax_Classes' ) ) {

    class WTARS_Shipped_Admin_Data_List_Tax_Classes {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-data-list-tax_options', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            $result[ '--1' ] = esc_html__( 'Not taxable', 'table-rate-shipping-rates' );
            $result[ '--0' ] = esc_html__( 'Based on package items tax option', 'table-rate-shipping-rates' );

            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List_Tax_Classes();
}