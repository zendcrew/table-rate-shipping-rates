<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cart_Fees_Panel_Options' ) ) {

    class WTARS_Shipped_Admin_Cart_Fees_Panel_Options {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/cart-fees/get-panel-option-fields', array( $this, 'get_fields' ), 10, 2 );
        }

        public function get_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 4,
                'merge_fields' => false,
                'fields' => array(
                    $this->get_title_field(),
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__( 'Adds a private note for reference purposes', 'table-rate-shipping-rates' ),
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Admin Note', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'fee_method',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Fee Method', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls how the handling fee should be applied', 'table-rate-shipping-rates' ),
                        'default' => 'rate_cost',
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => $this->get_fee_method_options(),
                        'width' => '100%',
                        'fold_id' => 'fee_method',
                    ),
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 3,
                'merge_fields' => false,
                'fields' => array(
                    $this->get_tooltip_field(),
                    array(
                        'id' => 'inclusive_tax',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Tax Calculation', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls handling fee tax calculations', 'table-rate-shipping-rates' ),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__( 'Calculate fee exclusive of taxes', 'table-rate-shipping-rates' ),
                            'yes' => esc_html__( 'Calculate fee inclusive of taxes', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                    ),
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 8,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'shipping_rates',
                        'type' => 'select2',
                        'column_size' => 6,
                        'column_title' => esc_html__( 'Shipping Rates', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls which shipping rates the fee should be part of', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'multiple' => true,
                        'minimum_results_forsearch' => 10,
                        'placeholder' => esc_html__( 'Select shipping rates...', 'table-rate-shipping-rates' ),
                        'data' => 'shipped:shipping_rates',
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'compare',
                        'type' => 'select2',
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Equals To', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls how the shipping rates should be included', 'table-rate-shipping-rates' ),
                        'default' => 'in_list',
                        'options' => array(
                            'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                            'none' => esc_html__( 'Any NOT in the list', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

        private function get_title_field() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return array(
                    'id' => 'title',
                    'type' => 'textbox',
                    'tooltip' => esc_html__( 'Controls handling fee title on cart and checkout pages', 'table-rate-shipping-rates' ),
                    'column_size' => 2,
                    'column_title' => esc_html__( 'Title', 'table-rate-shipping-rates' ),
                    'default' => '',
                    'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                    'width' => '100%',
                );
            } else {

                return array(
                    'id' => 'is_any',
                    'type' => 'textblock',
                    'show_box' => true,
                    'column_size' => 2,
                    'column_title' => esc_html__( 'Title', 'table-rate-shipping-rates' ),
                    'tooltip' => esc_html__( 'Controls handling fee title on cart and checkout pages', 'table-rate-shipping-rates' ),
                    'text' => WTARS_Shipped_Admin_Page::get_premium_messages( 'short_message' ),
                    'width' => '100%',
                    'box_width' => '100%',
                    'fold' => array(
                        'target' => 'fee_method',
                        'attribute' => 'value',
                        'value' => 'rate_cost',
                        'oparator' => 'neq',
                        'clear' => false,
                    ),
                );
            }
        }

        private function get_tooltip_field() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return array(
                    'id' => 'tooltip',
                    'type' => 'textarea',
                    'sanitize_type' => 'shipped_kses_post',
                    'tooltip' => esc_html__( 'Controls handling fee tooltip on cart and checkout pages', 'table-rate-shipping-rates' ),
                    'column_size' => 2,
                    'column_title' => esc_html__( 'Tooltip', 'table-rate-shipping-rates' ),
                    'default' => '',
                    'rows' => 1,
                    'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                    'width' => '100%',
                    'fold' => array(
                        'target' => 'fee_method',
                        'attribute' => 'value',
                        'value' => 'rate_cost',
                        'oparator' => 'neq',
                        'clear' => false,
                    ),
                );
            } else {

                return array(
                    'id' => 'is_any',
                    'type' => 'textblock',
                    'show_box' => true,
                    'column_size' => 2,
                    'column_title' => esc_html__( 'Tooltip', 'table-rate-shipping-rates' ),
                    'tooltip' => esc_html__( 'Controls handling fee tooltip on cart and checkout pages', 'table-rate-shipping-rates' ),
                    'text' => WTARS_Shipped_Admin_Page::get_premium_messages( 'short_message' ),
                    'width' => '100%',
                    'box_width' => '100%',
                    'fold' => array(
                        'target' => 'fee_method',
                        'attribute' => 'value',
                        'value' => 'rate_cost',
                        'oparator' => 'neq',
                        'clear' => false,
                    ),
                );
            }
        }

        private function get_fee_method_options() {
            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return array(
                    'rate_cost' => esc_html__( 'Apply as shipping cost', 'table-rate-shipping-rates' ),
                    'cart_fee' => esc_html__( 'Apply as cart fee', 'table-rate-shipping-rates' ),
                );
            }

            return array(
                'rate_cost' => esc_html__( 'Apply as shipping cost', 'table-rate-shipping-rates' ),
            );
        }

    }

    new WTARS_Shipped_Admin_Cart_Fees_Panel_Options();
}
