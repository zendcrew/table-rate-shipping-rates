<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Shipping_Rates_Section' ) ) {

    ReonUtil::recursive_require( dirname( __FILE__ ), array( 'shipping-rates-section.php' ) );

    class WTARS_Shipped_Admin_Shipping_Rates_Section extends WTARS_Shipped_Admin_Section {

        protected function init() {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                add_filter( 'get-option-page-' . $this->get_option_name() . 'section-shipping_rates-fields', array( $this, 'get_settings_fields' ), 20, 2 );
            }

            add_filter( 'get-option-page-' . $this->get_option_name() . 'section-shipping_rates-fields', array( $this, 'get_rules_fields' ), 30, 2 );


            add_filter( 'reon/get-repeater-field-shipping_rates-templates', array( $this, 'get_rule_templates' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-shipping_rates-shipping_rate-fields', array( $this, 'get_rule_fields' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-shipping_rates-shipping_rate-head-fields', array( $this, 'get_rule_head_fields' ), 10, 2 );

            add_filter( 'reon/process-save-options-' . $this->get_option_name(), array( $this, 'process_options' ), 10 );
        }

        public function get_settings_fields( $in_fields, $section_id ) {

            $in_fields[] = array(
                'id' => 'shipping_rates_settings',
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
                                'column_title' => esc_html__( 'Cost Limit Per Rate', 'table-rate-shipping-rates' ),
                                'tooltip' => esc_html__( 'Controls cost limits per every shipping rates', 'table-rate-shipping-rates' ),
                                'default' => 'no',
                                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                                'options' => array(
                                    'no' => esc_html__( 'No limit', 'table-rate-shipping-rates' ),
                                ),
                                'width' => '100%',
                                'fold_id' => 'limit_type',
                            ),
                            array(
                                'id' => 'mode',
                                'type' => 'select2',
                                'column_size' => 3,
                                'column_title' => esc_html__( 'Apply Mode', 'table-rate-shipping-rates' ),
                                'tooltip' => esc_html__( 'Controls shipping rate apply mode', 'table-rate-shipping-rates' ),
                                'default' => 'all',
                                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                                'data' => 'shipped:shipping_rate_modes',
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
                'id' => 'shipping_rates',
                'type' => 'repeater',
                'white_repeater' => false,
                'repeater_size' => 'small',
                'accordions' => true,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => true,
                'last' => true,
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
                    'button_text' => esc_html__( 'New Shipping Rate', 'table-rate-shipping-rates' ),
                ),
            );

            return $in_fields;
        }

        public function get_rule_templates( $in_templates, $repeater_args ) {
            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == $this->get_option_name() ) {

                $in_templates[] = array(
                    'id' => 'shipping_rate',
                    'head' => array(
                        'title' => '',
                        'defaut_title' => esc_html__( 'Shipping Rate', 'table-rate-shipping-rates' ),
                        'title_field' => 'title',
                        'subtitle_field' => 'admin_note',
                    )
                );
            }

            return $in_templates;
        }

        public function get_rule_fields( $in_fields ) {

            $args = array(
                'instance_id' => $this->get_instance_id(),
                'option_name' => $this->get_option_name()
            );

            return apply_filters( 'wtars_shipped_admin/shipping-rates/get-panels', array(), $args );
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
                        'data' => 'shipped:shipping_rate_methods',
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

            if ( isset( $options[ 'shipping_rates' ] ) ) {
                $options[ 'shipping_rates' ] = $this->process_rules( $options[ 'shipping_rates' ] );
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
                'module' => 'shipping-rates',
            );

            if ( isset( $rule[ 'base_costs' ] ) ) {

                $args[ 'sub_module' ] = 'base-cost';

                $rule[ 'base_costs' ] = $this->process_costs( $rule[ 'base_costs' ], $args );
            }

            if ( isset( $rule[ 'extra_costs' ] ) ) {

                $args[ 'sub_module' ] = 'extra-cost';

                $rule[ 'extra_costs' ] = $this->process_costs( $rule[ 'extra_costs' ], $args );
            }

            return $rule;
        }

        private function process_costs( $raw_costs, $args ) {

            $costs = array();


            foreach ( $raw_costs as $key => $raw_cost ) {
                $cost = array();

                $cost[ 'id' ] = $raw_cost[ 'id' ];
                $cost[ 'cost_type' ] = $raw_cost[ 'cost_type' ];

                if ( isset( $raw_cost[ 'add_type' ] ) ) {
                    $cost[ 'add_type' ] = $raw_cost[ 'add_type' ];
                }

                if ( isset( $raw_cost[ 'group_by' ] ) ) {
                    $cost[ 'group_by' ] = $raw_cost[ 'group_by' ];
                }

                $cost[ 'set_conditions' ] = $raw_cost[ 'set_conditions' ];

                $cost[ 'cost' ] = 0.0;

                if ( is_numeric( $raw_cost[ 'cost' ] ) ) {
                    $cost[ 'cost' ] = $raw_cost[ 'cost' ];
                }

                $cost = apply_filters( 'wtars_shipped_admin/process-cost-options', apply_filters( 'wtars_shipped_admin/process-cost-type-' . $raw_cost[ 'cost_type' ] . '-options', $cost, $raw_cost, $args ), $raw_cost, $args );

                $cost[ 'taxable' ] = $raw_cost[ 'taxable' ];

                $costs[ $key ] = $cost;
            }

            return $costs;
        }

    }

}