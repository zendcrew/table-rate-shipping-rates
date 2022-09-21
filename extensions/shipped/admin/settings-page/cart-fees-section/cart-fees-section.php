<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cart_Fees_Section' ) ) {

    ReonUtil::recursive_require( dirname( __FILE__ ), array( 'cart-fees-section.php' ) );

    class WTARS_Shipped_Admin_Cart_Fees_Section extends WTARS_Shipped_Admin_Section {

        protected function init() {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                add_filter( 'get-option-page-' . $this->get_option_name() . 'section-cart_fees-fields', array( $this, 'get_settings_fields' ), 20, 2 );
            }

            add_filter( 'get-option-page-' . $this->get_option_name() . 'section-cart_fees-fields', array( $this, 'get_rules_fields' ), 30, 2 );


            add_filter( 'reon/get-repeater-field-cart_fees-templates', array( $this, 'get_rule_templates' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-cart_fees-cart_fee-fields', array( $this, 'get_rule_fields' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-cart_fees-cart_fee-head-fields', array( $this, 'get_rule_head_fields' ), 10, 2 );

            add_filter( 'reon/process-save-options-' . $this->get_option_name(), array( $this, 'process_options' ), 10 );
        }

        public function get_settings_fields( $in_fields, $section_id ) {

            $currency = get_woocommerce_currency_symbol( get_woocommerce_currency() );

            $in_fields[] = array(
                'id' => 'cart_fees_settings',
                'type' => 'panel',
                'last' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'field_css_class' => array( 'wtars_shipped_rules_apply_mode' ),
                'fields' => array(
                    array(
                        'id' => 'shipped_any_id',
                        'type' => 'columns-field',
                        'columns' => 8,
                        'merge_fields' => false,
                        'fields' => array(
                            array(
                                'id' => 'limit_type',
                                'type' => 'select2',
                                'column_size' => 2,
                                'column_title' => esc_html__( 'Cost Limit', 'table-rate-shipping-rates' ),
                                'tooltip' => esc_html__( 'Controls handling fees limits', 'table-rate-shipping-rates' ),
                                'default' => 'no',
                                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                                'options' => array(
                                    'no' => esc_html__( 'No limit', 'table-rate-shipping-rates' ),
                                    'prem_1' => str_replace( '[0]', $currency, esc_html__( 'Fixed ([0]) (Premium)', 'table-rate-shipping-rates' ) ),
                                    'prem_2' => esc_html__( 'Percentage (%) (Premium)', 'table-rate-shipping-rates' ),
                                ),
                                'width' => '100%',
                                'fold_id' => 'limit_type',
                            ),
                            array(
                                'id' => 'mode',
                                'type' => 'select2',
                                'column_size' => 3,
                                'column_title' => esc_html__( 'Apply Mode', 'table-rate-shipping-rates' ),
                                'tooltip' => esc_html__( 'Controls handling fees apply mode', 'table-rate-shipping-rates' ),
                                'default' => 'all',
                                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                                'data' => 'shipped:cart_fee_modes',
                                'width' => '100%',
                            ),
                        ),
                    ),
                ),
            );




            return $in_fields;
        }

        public function get_rules_fields( $in_fields, $section_id ) {

            $max_sections = 1;
            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $max_sections = 99999;
            }

            $in_fields[] = array(
                'id' => 'cart_fees',
                'type' => 'repeater',
                'white_repeater' => false,
                'repeater_size' => 'small',
                'accordions' => true,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => true,
                'width' => '100%',
                'max_sections' => $max_sections,
                'max_sections_msg' => esc_html__( 'Please upgrade to premium version in order to add more rates', 'table-rate-shipping-rates' ),
                'field_css_class' => array( 'wtars_shipped_rules' ),
                'css_class' => 'wtars_shipped_extension_rules',
                'auto_expand' => array(
                    'new_section' => true,
                    'cloned_section' => true,
                ),
                'sortable' => array(
                    'enabled' => true,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'show_list' => false,
                    'button_text' => esc_html__( 'New Handling Fee', 'table-rate-shipping-rates' ),
                ),
            );

            return $in_fields;
        }

        public function get_rule_templates( $in_templates, $repeater_args ) {
            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == $this->get_option_name() ) {

                if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                    $in_templates[] = array(
                        'id' => 'cart_fee',
                        'head' => array(
                            'title' => '',
                            'defaut_title' => esc_html__( 'Handling Fee', 'table-rate-shipping-rates' ),
                            'title_field' => 'title',
                            'subtitle_field' => 'admin_note',
                        )
                    );
                } else {

                    $in_templates[] = array(
                        'id' => 'cart_fee',
                        'head' => array(
                            'title' => '',
                            'defaut_title' => esc_html__( 'Handling Fee', 'table-rate-shipping-rates' ),
                            'title_field' => 'admin_note',
                        )
                    );
                }
            }

            return $in_templates;
        }

        public function get_rule_fields( $in_fields ) {

            $args = array(
                'instance_id' => $this->get_instance_id(),
                'option_name' => $this->get_option_name()
            );

            return apply_filters( 'wtars_shipped_admin/cart-fees/get-panels', array(), $args );
        }

        public function get_rule_head_fields( $in_fields, $repeater_args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'group-field',
                'position' => 'right',
                'width' => '100%',
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'apply_mode',
                        'type' => 'select2',
                        'default' => 'with_others',
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'data' => 'shipped:cart_fee_methods',
                        'width' => '255px',
                    ),
                    array(
                        'id' => 'enable',
                        'type' => 'select2',
                        'default' => 'yes',
                        'options' => array(
                            'yes' => esc_html__( 'Enable', 'table-rate-shipping-rates' ),
                            'no' => esc_html__( 'Disable', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '95px',
                    ),
                ),
            );



            return $in_fields;
        }

        public function process_options( $options ) {

            if ( isset( $options[ 'cart_fees' ] ) ) {
                $options[ 'cart_fees' ] = $this->process_rules( $options[ 'cart_fees' ] );
            }

            return $options;
        }

        private function process_rules( $rules ) {

            foreach ( $rules as $key => $rule ) {

                $rules[ $key ] = $this->process_rule( $rule );
            }

            return $rules;
        }

        private function process_rule( $rule ) {

            $args = array(
                'instance_id' => $this->get_instance_id(),
                'option_name' => $this->get_option_name(),
                'module' => 'cart-fees',
            );

            if ( isset( $rule[ 'base_costs' ] ) ) {

                $args[ 'sub_module' ] = 'base-cost';

                $rule[ 'base_costs' ] = $this->process_fees( $rule[ 'base_costs' ], $args );
            }

            if ( isset( $rule[ 'extra_costs' ] ) ) {

                $args[ 'sub_module' ] = 'extra-cost';

                $rule[ 'extra_costs' ] = $this->process_fees( $rule[ 'extra_costs' ], $args );
            }

            return $rule;
        }

        private function process_fees( $raw_fees, $args ) {

            $fees = array();

            foreach ( $raw_fees as $key => $raw_fee ) {
                $fee = array();

                $fee[ 'id' ] = $raw_fee[ 'id' ];
                $fee[ 'cost_type' ] = $raw_fee[ 'cost_type' ];

                if ( isset( $raw_fee[ 'add_type' ] ) ) {
                    $fee[ 'add_type' ] = $raw_fee[ 'add_type' ];
                }

                if ( isset( $raw_fee[ 'group_by' ] ) ) {
                    $fee[ 'group_by' ] = $raw_fee[ 'group_by' ];
                }

                $fee[ 'set_conditions' ] = $raw_fee[ 'set_conditions' ];

                $fee[ 'cost' ] = 0.0;

                if ( is_numeric( $raw_fee[ 'cost' ] ) ) {
                    $fee[ 'cost' ] = $raw_fee[ 'cost' ];
                }

                $fee = apply_filters( 'wtars_shipped_admin/process-cost-options', apply_filters( 'wtars_shipped_admin/process-cost-type-' . $raw_fee[ 'cost_type' ] . '-options', $fee, $raw_fee, $args ), $raw_fee, $args );

                $fee[ 'taxable' ] = $raw_fee[ 'taxable' ];

                $fees[ $key ] = $fee;
            }

            return $fees;
        }

    }

}