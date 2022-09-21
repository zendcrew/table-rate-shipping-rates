<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Shipping_Rates_Panel' ) ) {

    class WTARS_Shipped_Admin_Shipping_Rates_Panel {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/shipping-rates/get-panels', array( $this, 'get_panel' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panel-fields', array( $this, 'get_panel_fields' ), 10, 2 );
        }

        public function get_panel( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'rate_id',
                'type' => 'autoid',
                'autoid' => 'shipped',
            );

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'last' => true,
                'fields' => apply_filters( 'wtars_shipped_admin/shipping-rates/get-panel-fields', array(), $args ),
            );

            return $in_fields;
        }

        public function get_panel_fields( $in_fields, $args ) {
           
            return apply_filters( 'wtars_shipped_admin/shipping-rates/get-panel-option-fields', $in_fields, $args );
        }

    }

    new WTARS_Shipped_Admin_Shipping_Rates_Panel();
}