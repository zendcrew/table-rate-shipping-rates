<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_WMC_Package' ) ) {

    class WTARS_Shipped_WMC_Package {

        public function __construct() {

            add_filter( 'wtars_shipped/get-package-item', array( $this, 'get_package_item' ), 10, 2 );
        }

        public function get_package_item( $item, $package_item ) {

            $converter = $this->get_converter();

            $item[ 'line_subtotal' ] = $converter->revert_amount( $item[ 'line_subtotal' ] );
            $item[ 'line_subtotal_tax' ] = $converter->revert_amount( $item[ 'line_subtotal_tax' ] );

            return $item;
        }

        private function get_converter() {

            return WTARS_Shipped_WMC::get_instance();
        }

    }

    new WTARS_Shipped_WMC_Package();
}
