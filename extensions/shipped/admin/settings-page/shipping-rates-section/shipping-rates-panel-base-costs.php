<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Shipping_Rates_Panel_Base_Costs' ) ) {

    class WTARS_Shipped_Admin_Shipping_Rates_Panel_Base_Costs {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panels', array( $this, 'get_panel' ), 20, 2 );
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panel-base-costs-fields', array( $this, 'get_panel_fields' ), 10, 2 );

            add_filter( 'reon/get-repeater-field-base_costs-templates', array( $this, 'get_option_templates' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-base_costs-base_cost-fields', array( $this, 'get_cost_fields' ), 10, 2 );
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
                'fields' => apply_filters( 'wtars_shipped_admin/shipping-rates/get-panel-base-costs-fields', array(), $args ),
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
                        'title' => esc_html__( 'Shipping Costs', 'table-rate-shipping-rates' ),
                        'desc' => esc_html__( 'List of shipping costs to apply, empty list will apply zero cost', 'table-rate-shipping-rates' ),
                    )
                ),
            );

            $in_fields[] = array(
                'id' => 'base_costs',
                'field_args' => $args,
                'type' => 'repeater',
                'white_repeater' => true,
                'repeater_size' => 'smaller',
                'collapsible' => false,
                'accordions' => false,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => false,
                'css_class' => 'shipped_calcs',
                'field_css_class' => array( 'shipped_calcs_field' ),
                'width' => '100%',
                'auto_expand' => array(
                    'all_section' => true,
                    'new_section' => true,
                    'default_section' => true,
                    'cloned_section' => true,
                ),
                'sortable' => array(
                    'enabled' => true,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'button_text' => esc_html__( 'Add Cost', 'table-rate-shipping-rates' ),
                ),
            );

            return $in_fields;
        }

        public function get_option_templates( $in_templates, $repeater_args ) {

            $in_templates[] = array(
                'id' => 'base_cost',
                'head' => array(
                    'title' => '',
                    'defaut_title' => esc_html__( 'Shipping Cost', 'table-rate-shipping-rates' ),
                    'title_field' => 'cost_type',
                )
            );

            return $in_templates;
        }

        public function get_cost_fields( $in_fields, $repeater_args ) {

            $section_args = $repeater_args[ 'field_args' ];

            $args = array(
                'instance_id' => $section_args[ 'instance_id' ],
                'module' => 'shipping-rates',
                'sub_module' => 'base-cost',
                'fold_id' => 'cost_type',
                'format_cap_text' => esc_html__( 'Cost', 'table-rate-shipping-rates' ),
                'format_text' => esc_html__( 'cost', 'table-rate-shipping-rates' ),
            );

            $cost_types = WTARS_Shipped_Admin_Cost_Types::get_types( $args );

            $in_fields[] = array(
                'id' => 'id',
                'type' => 'autoid',
                'autoid' => 'shipped',
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 5,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'cost_type',
                        'type' => 'select2',
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Cost Type', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls shipping cost type', 'table-rate-shipping-rates' ),
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-grouped-list',
                        'default' => apply_filters( 'wtars_shipped_admin/get-cost-types-default', 'cart_fixed', $args ),
                        'options' => $cost_types,
                        'attributes' => array(
                            'data-repeater-title' => 'base_costs',
                        ),
                        'width' => '100%',
                        'fold_id' => 'cost_type',
                    ),
                    array(
                        'id' => 'add_type',
                        'type' => 'select2',
                        'column_size' => 2,
                        'tooltip' => esc_html__( 'Controls how the shipping cost should be added to previously calculated costs', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Addition / Subtraction', 'table-rate-shipping-rates' ),
                        'default' => 'add',
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => WTARS_Shipped_Admin_Cost_Types::get_cost_add_methods( $args ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'set_conditions',
                        'type' => 'select2',
                        'column_size' => 1,
                        'tooltip' => esc_html__( 'Determines whether no conditions should be validated before cost calculation', 'table-rate-shipping-rates' ),
                        'column_title' => esc_html__( 'Validate Conditions', 'table-rate-shipping-rates' ),
                        'default' => 'no',
                        'options' => array(
                            'no' => esc_html__( 'No', 'table-rate-shipping-rates' ),
                            'yes' => esc_html__( 'Yes', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                        'fold_id' => 'set_conditions',
                    ),
                ),
            );


            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 12,
                'merge_fields' => false,
                'fields' => $this->get_cost_box_fields( array(), $args ),
            );


            foreach ( WTARS_Shipped_Admin_Cost_Types::get_type_fields( $args ) as $type_field ) {
                $in_fields[] = $type_field;
            }

            $in_fields = WTARS_Shipped_Admin_Cost_Types::get_other_fields( $in_fields, $args );

            return $in_fields;
        }

        private function get_cost_box_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'cost',
                'type' => 'textbox',
                'input_type' => 'number',
                'tooltip' => esc_html__( 'Controls the cost to apply, based on cost type', 'table-rate-shipping-rates' ),
                'column_size' => 2,
                'column_title' => esc_html__( 'Cost', 'table-rate-shipping-rates' ),
                'default' => '0.00',
                'placeholder' => esc_html__( '0.00', 'table-rate-shipping-rates' ),
                'width' => '100%',
                'attributes' => array(
                    'min' => '0',
                    'step' => '0.01',
                ),
            );

            $in_fields = WTARS_Shipped_Admin_Cost_Types::get_cost_box_fields( $in_fields, $args );

            $in_fields[] = array(
                'id' => 'taxable',
                'type' => 'select2',
                'column_size' => 4,
                'column_title' => esc_html__( 'Tax Class', 'table-rate-shipping-rates' ),
                'tooltip' => esc_html__( 'Controls cost tax class', 'table-rate-shipping-rates' ),
                'default' => '--0',
                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                'data' => 'shipped:tax_options',
                'width' => '100%',
            );


            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Shipping_Rates_Panel_Base_Costs();
}