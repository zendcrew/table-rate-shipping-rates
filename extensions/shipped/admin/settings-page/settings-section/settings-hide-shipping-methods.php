<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Hide_Methods_Settings' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Hide_Methods_Settings {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-settings-section-fields', array( $this, 'get_panel' ), 60, 2 );
        }

        public function get_panel( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'last' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'field_css_class' => array( 'shipped_setting_panel' ),
                'merge_fields' => false,
                'fields' => $this->get_fields( array(), $args ),
            );

            return $in_fields;
        }

        private function get_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Hide Shipping Methods', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'Use these settings to hide this or other shipping methods', 'table-rate-shipping-rates' ),
            );

            $in_fields[] = array(
                'id' => 'hide_methods',
                'type' => 'columns-field',
                'columns' => 4,
                'merge_fields' => true,
                'fields' => array(
                    array(
                        'id' => 'mode',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Controls how shipping methods should hidden', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Mode', 'table-rate-shipping-rates' ),
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__( 'Do not hide any method', 'table-rate-shipping-rates' ),
                            'hide_others' => esc_html__( 'Hide other shipping methods', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                        'fold_id' => 'hide_mode'
                    ),
                ),
            );



            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Hide_Methods_Settings();
}
