<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Order_Totals' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_Order_Totals {

        private $prefix = 'order_total-';

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 70, 2 );
            add_filter( 'wtars_shipped_admin/get-order_totals-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            foreach ( $this->get_options() as $option ) {

                add_filter( 'wtars_shipped_admin/get-' . $this->prefix . $option[ 'option_id' ] . '-condition-fields', array( $this, 'get_totals_fields' ), 10, 2 );
            }

            $in_groups[ 'order_totals' ] = esc_html__( 'Cart &amp; Package Totals', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            foreach ( $this->get_options() as $option ) {

                $in_list[ $this->prefix . $option[ 'option_id' ] ] = sprintf( esc_html__( 'Totals - %s', 'table-rate-shipping-rates' ), $this->get_title( $option ) );
            }

            return $in_list;
        }

        public function get_totals_fields( $in_fields, $args ) {

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
                'width' => '98%',
                'box_width' => '44%',
                'fold_id' => 'order_totals',
            );

            $in_fields[] = array(
                'id' => 'totals',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '0.01',
                'placeholder' => esc_html__( '0.00', 'table-rate-shipping-rates' ),
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
                'width' => '100%',
                'box_width' => '56%',
                'fold' => array(
                    'target' => 'order_totals',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'neq',
                    'clear' => false,
                ),
            );

            $in_fields[] = array(
                'id' => 'from_totals',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '0',
                'placeholder' => esc_html__( '0.00', 'table-rate-shipping-rates' ),
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
                'width' => '98%',
                'box_width' => '28%',
                'fold' => array(
                    'target' => 'order_totals',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'eq',
                    'clear' => false,
                ),
            );

            $in_fields[] = array(
                'id' => 'to_totals',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '0.01',
                'placeholder' => esc_html__( '0.00', 'table-rate-shipping-rates' ),
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
                'width' => '100%',
                'box_width' => '28%',
                'fold' => array(
                    'target' => 'order_totals',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'eq',
                    'clear' => false,
                ),
            );

            return $in_fields;
        }

        public function get_options() {

            global $wtars_shipped;

            $options = array();

            if ( isset( $wtars_shipped[ 'checkout_totals' ] ) ) {
                $options = $wtars_shipped[ 'checkout_totals' ];
            } else {
                $options = self::get_default();
            }

            return $options;
        }

        private function get_title( $option ) {
            if ( !empty( $option[ 'title' ] ) ) {
                return $option[ 'title' ];
            }

            return $this->get_default_title( $option[ 'option_id' ] );
        }

        private function get_default_title( $option_id ) {

            foreach ( $this->get_default() as $option ) {

                if ( $option_id == $option[ 'option_id' ] ) {
                    return $option[ 'admin_note' ];
                }
            }
            return esc_html__( 'Order Totals', 'table-rate-shipping-rates' );
        }

        private function get_default() {

            return array(
                array(
                    'title' => esc_html__( 'Order Totals', 'table-rate-shipping-rates' ),
                    'option_id' => '2234343',
                ),
            );
        }

    }

    new WTARS_Shipped_Admin_Conditions_Order_Totals();
}