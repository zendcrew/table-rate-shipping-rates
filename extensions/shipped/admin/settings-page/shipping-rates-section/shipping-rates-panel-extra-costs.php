<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Shipping_Rates_Panel_Extra_Costs' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Shipping_Rates_Panel_Extra_Costs {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panels', array( $this, 'get_panel' ), 30, 2 );
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panel-extra-costs-fields', array( $this, 'get_panel_fields' ), 10, 2 );
            
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
                'field_css_class' => array( 'shipped_panel_calcs_field', 'shipped_mid_panel_calcs' ),
                'last' => true,
                'fields' => apply_filters( 'wtars_shipped_admin/shipping-rates/get-panel-extra-costs-fields', array(), $args ),
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
                'width' => '100%',
                'last' => true,
                'css_class' => 'shipped_calcs_title',
                'fields' => array(
                    array(
                        'id' => 'any_id',
                        'type' => 'paneltitle',
                        'full_width' => true,
                        'center_head' => true,
                        'title' => esc_html__( 'Grouped Shipping Costs', 'table-rate-shipping-rates' ),
                        'desc' => esc_html__( 'List of grouped shipping costs to apply, empty list will apply zero cost', 'table-rate-shipping-rates' ),
                    ),
                    array(
                        'id' => 'is_any',
                        'type' => 'textblock',
                        'show_box' => false,
                        'full_width' => true,
                        'center_head' => true,
                        'text' => WTARS_Shipped_Admin_Page::get_premium_messages(),
                        'field_css_class' => array( 'shipped-title-message' ),
                        'width' => '100%',
                        'box_width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Shipping_Rates_Panel_Extra_Costs();
}
    