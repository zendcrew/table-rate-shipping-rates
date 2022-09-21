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


            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $result[ 'prem_1' ] = esc_html__( 'Standard rates (Premium)', 'table-rate-shipping-rates' );

                $tax_object = new WC_Tax();
                $tax_classes = $tax_object->get_tax_classes();
                $tax_classes_slug = $tax_object->get_tax_class_slugs();

                $tax_classes_count = count( $tax_classes ) + 1;

                for ( $i = 1; $i < $tax_classes_count; $i++ ) {
                    $result[ 'prem_' . ($i + 1) ] = $tax_classes[ $i - 1 ] . esc_html__( ' (Premium)', 'table-rate-shipping-rates' );
                }
            }

            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List_Tax_Classes();
}