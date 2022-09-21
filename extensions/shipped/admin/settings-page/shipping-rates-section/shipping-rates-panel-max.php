<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Shipping_Rates_Panel_Max' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Shipping_Rates_Panel_Max {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panels', array( $this, 'get_panel' ), 40, 2 );
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panel-max-fields', array( $this, 'get_panel_fields' ), 10, 2 );
        }

        public function get_panel( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'merge_fields' => false,
                'css_class' => 'shipped_panel_calcs',
                'field_css_class' => array( 'shipped_panel_calcs_field' ),
                'last' => true,
                'fields' => apply_filters( 'wtars_shipped_admin/shipping-rates/get-panel-max-fields', array(), $args ),
            );

            return $in_fields;
        }

        public function get_panel_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'full_width' => true,
                'center_head' => true,
                'white_panel' => true,
                'panel_size' => 'smaller',
                'merge_fields' => false,
                'width' => '100%',
                'last' => true,
                'fields' => array(
                    array(
                        'id' => 'costs_limit',
                        'type' => 'columns-field',
                        'columns' => 4,
                        'merge_fields' => true,
                        'fields' => $this->get_fields( $in_fields, $args ),
                    ),
                ),
            );

            return $in_fields;
        }

        private function get_fields( $in_fields, $args ) {
          
            $in_fields[] = array(
                'id' => 'limit_type',
                'type' => 'select2',
                'column_size' => 1,
                'column_title' => esc_html__( 'Costs Limit', 'table-rate-shipping-rates' ),
                'tooltip' => esc_html__( 'Controls shipping rate costs limit', 'table-rate-shipping-rates' ),
                'default' => 'no',
                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                'options' => array(
                    'no' => esc_html__( 'No limit', 'table-rate-shipping-rates' ),
                    
                ),
                'width' => '100%',
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Shipping_Rates_Panel_Max();
}
