<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Package_Weight' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_Package_Weight {

        private $prefix = 'package_weight-';

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 80, 2 );
            add_filter( 'wtars_shipped_admin/get-package_weight-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $unit = get_option( 'woocommerce_weight_unit' );

            foreach ( $this->get_options() as $option ) {

                add_filter( 'wtars_shipped_admin/get-' . $this->prefix . $option[ 'option_id' ] . '-condition-fields', array( $this, 'get_weight_fields' ), 10, 2 );
            }

            /* translators: 1: weight unit */
            $in_groups[ 'package_weight' ] = sprintf( esc_html__( 'Package Weight (%s)', 'table-rate-shipping-rates' ), $unit );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {
            
            $unit = get_option( 'woocommerce_weight_unit' );

            foreach ( $this->get_options() as $option ) {

                /* translators: 1: weight calculator name 2: weight unit */
                $in_list[ $this->prefix . $option[ 'option_id' ] ] = sprintf( esc_html__( 'Package Weight - %1$s (%2$s)', 'table-rate-shipping-rates' ), $this->get_title( $option ), $unit );
            }

            return $in_list;
        }

        public function get_weight_fields( $in_fields, $args ) {

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
                'fold_id' => 'order_weight',
            );

            $in_fields[] = array(
                'id' => 'weight',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '0.5',
                'placeholder' => esc_html__( '0.00', 'table-rate-shipping-rates' ),
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
                'width' => '100%',
                'box_width' => '56%',
                'fold' => array(
                    'target' => 'order_weight',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'neq',
                    'clear' => false,
                ),
            );

            $in_fields[] = array(
                'id' => 'from_weight',
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
                    'target' => 'order_weight',
                    'attribute' => 'value',
                    'value' => array( '--' ),
                    'oparator' => 'eq',
                    'clear' => false,
                ),
            );

            $in_fields[] = array(
                'id' => 'to_weight',
                'type' => 'textbox',
                'input_type' => 'number',
                'default' => '0.5',
                'placeholder' => esc_html__( '0.00', 'table-rate-shipping-rates' ),
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
                'width' => '100%',
                'box_width' => '28%',
                'fold' => array(
                    'target' => 'order_weight',
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

            if ( isset( $wtars_shipped[ 'weight_calcs' ] ) ) {
                $options = $wtars_shipped[ 'weight_calcs' ];
            } else {
                $options = self::get_default();
            }

            return $options;
        }

        private function get_title( $option ) {
            if ( !empty( $option[ 'admin_note' ] ) ) {
                return $option[ 'admin_note' ];
            }

            return $this->get_default_title( $option[ 'option_id' ] );
        }

        private function get_default_title( $option_id ) {

            foreach ( $this->get_default() as $option ) {

                if ( $option_id == $option[ 'option_id' ] ) {
                    return $option[ 'admin_note' ];
                }
            }
            return esc_html__( 'Weight', 'table-rate-shipping-rates' );
        }

        private function get_default() {

            return array(
                array(
                    'admin_note' => esc_html__( 'Weight', 'table-rate-shipping-rates' ),
                    'option_id' => '2234343',
                ),
            );
        }

    }

    new WTARS_Shipped_Admin_Conditions_Package_Weight();
}