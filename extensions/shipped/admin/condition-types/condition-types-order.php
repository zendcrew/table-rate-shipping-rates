<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Order' ) ) {

    class WTARS_Shipped_Admin_Conditions_Order {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 60, 2 );
            add_filter( 'wtars_shipped_admin/get-order-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
            
            add_filter( 'wtars_shipped_admin/get-package_quantity-condition-fields', array( $this, 'get_quantity_fields' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'order' ] = esc_html__( 'Cart &amp; Package', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $in_list[ 'prem_1' ] = esc_html__( 'Cart Total Quantity (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'package_quantity' ] = esc_html__( 'Package Total Quantity', 'table-rate-shipping-rates' );
                $in_list[ 'prem_2' ] = esc_html__( 'Number Of Cart Items (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_3' ] = esc_html__( 'Number Of Package Items (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_4' ] = esc_html__( 'Applied Coupons (Premium)', 'table-rate-shipping-rates' );
            }

            return $in_list;
        }
        
        public function get_quantity_fields( $in_fields, $args ) {
            
            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => '--',
                'options' => array(
                    '--' => esc_html__( 'Between', 'table-rate-shipping-rates' ),
                    '>=' => esc_html__( 'More than or equal to', 'table-rate-shipping-rates' ),
                    '>' => esc_html__( 'More than', 'table-rate-shipping-rates' ),
                    '<=' => esc_html__( 'Less than or equal to', 'table-rate-shipping-rates' ),
                    '<' => esc_html__( 'Less than', 'table-rate-shipping-rates' ),
                    '==' => esc_html__( 'Equal to', 'table-rate-shipping-rates' ),
                    '!=' => esc_html__( 'Not equal to', 'table-rate-shipping-rates' ),
                ),
                'width' => '99%',
                'box_width' => '44%',
                'fold_id' => 'order_quantity',
            );
            
            $in_fields[] = array(
                'id' => 'quantity',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '1',
                'placeholder' => esc_html__( '0', 'table-rate-shipping-rates' ),
                'width' => '100%',
                'box_width' => '56%',
                'attributes' => array(
                    'min' => '1',
                    'step' => '1',
                ),
                'fold' => array(
                    'target' => 'order_quantity',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'neq',
                    'clear' => false,
                ),
            );
            
            $in_fields[] = array(
                'id' => 'from_quantity',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '0',
                'placeholder' => esc_html__( '0', 'table-rate-shipping-rates' ),
                'width' => '98%',
                'box_width' => '28%',
                'attributes' => array(
                    'min' => '0',
                    'step' => '1',
                ),
                'fold' => array(
                    'target' => 'order_quantity',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'eq',
                    'clear' => false,
                ),
            );
            
            $in_fields[] = array(
                'id' => 'to_quantity',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '1',
                'placeholder' => esc_html__( '0', 'table-rate-shipping-rates' ),
                'width' => '100%',
                'box_width' => '28%',
                'attributes' => array(
                    'min' => '1',
                    'step' => '1',
                ),
                'fold' => array(
                    'target' => 'order_quantity',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'eq',
                    'clear' => false,
                ),
            );

            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Order();
}
