<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Shipping_Address' ) ) {

    class WTARS_Shipped_Admin_Conditions_Shipping_Address {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 30, 2 );
            add_filter( 'wtars_shipped_admin/get-shipping_address-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
            
            add_filter( 'wtars_shipped_admin/get-shipping_countries-condition-fields', array( $this, 'get_countries_fields' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-shipping_states-condition-fields', array( $this, 'get_states_fields' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-shipping_postcodes-condition-fields', array( $this, 'get_postcodes_fields' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'shipping_address' ] = esc_html__( 'Shipping Address', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $in_list[ 'prem_1' ] = esc_html__( 'Shipping Continents (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'shipping_countries' ] = esc_html__( 'Shipping Countries', 'table-rate-shipping-rates' );
                $in_list[ 'shipping_states' ] = esc_html__( 'Shipping States', 'table-rate-shipping-rates' );
                $in_list[ 'prem_2' ] = esc_html__( 'Shipping Cities (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'shipping_postcodes' ] = esc_html__( 'Shipping Postcode / ZIP', 'table-rate-shipping-rates' );
            }

            return $in_list;
        }

        public function get_countries_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                    'none' => esc_html__( 'None in the list', 'table-rate-shipping-rates' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'countries',
                'type' => 'select2',
                'multiple' => true,
                'allow_clear' => true,
                'minimum_input_length' => 2,
                'minimum_results_forsearch' => 10,
                'placeholder' => esc_html__( 'Shipping Countries...', 'table-rate-shipping-rates' ),
                'ajax_data' => 'wc:countries',
                'width' => '100%',
                'box_width' => '74%',
            );

            return $in_fields;
        }

        public function get_states_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                    'none' => esc_html__( 'None in the list', 'table-rate-shipping-rates' ),
                ),
                'width' => '98%',
                'box_width' => '26%',
            );

            $in_fields[] = array(
                'id' => 'states',
                'type' => 'select2',
                'multiple' => true,
                'allow_clear' => true,
                'minimum_input_length' => 2,
                'minimum_results_forsearch' => 10,
                'placeholder' => esc_html__( 'Shipping States...', 'table-rate-shipping-rates' ),
                'ajax_data' => 'wc:states',
                'width' => '100%',
                'box_width' => '74%',
            );

            return $in_fields;
        }

        public function get_postcodes_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'match' => esc_html__( 'Match', 'table-rate-shipping-rates' ),
                    'not_match' => esc_html__( 'Not match', 'table-rate-shipping-rates' ),
                ),
                'width' => '98%',
                'box_width' => '22%',
            );

            $in_fields[] = array(
                'id' => 'postcode',
                'type' => 'textbox',
                'input_type' => 'text',
                'default' => '',
                'placeholder' => esc_html__( 'e.g 1815, 870*, [1870 - 9999], DSE, LDS', 'table-rate-shipping-rates' ),
                'width' => '100%',
                'box_width' => '78%',
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Shipping_Address();
}