<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}


if ( !class_exists( 'WTARS_Shipped_Admin_Logic_Types_Conditions' ) ) {

    class WTARS_Shipped_Admin_Logic_Types_Conditions {

        public function __construct() {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                add_filter( 'wtars_shipped_admin/shipping-rates/get-panel-conditions-fields', array( $this, 'get_rate_conditions_title' ), 70, 2 );
                add_filter( 'wtars_shipped_admin/cart-fees/get-panel-conditions-fields', array( $this, 'get_rate_conditions_title' ), 70, 2 );
                add_filter( 'wtars_shipped_admin/checkout-messages/get-panel-conditions-fields', array( $this, 'get_rate_conditions_title' ), 70, 2 );
                
                add_filter( 'wtars_shipped_admin/get-method-conditions-fields', array( $this, 'get_method_conditions_fields' ), 70, 2 );
                
            }

            add_filter( 'wtars_shipped_admin/get-cost-type-fields', array( $this, 'get_cost_conditions_title' ), 70, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-type-fields', array( $this, 'get_condition_fields' ), 71, 2 );
            add_filter( 'wtars_shipped_admin/shipping-rates/get-panel-conditions-fields', array( $this, 'get_condition_fields' ), 71, 2 );
            add_filter( 'wtars_shipped_admin/cart-fees/get-panel-conditions-fields', array( $this, 'get_condition_fields' ), 71, 2 );
            add_filter( 'wtars_shipped_admin/checkout-messages/get-panel-conditions-fields', array( $this, 'get_condition_fields' ), 71, 2 );

            add_filter( 'reon/get-simple-repeater-field-shipped-conditions-templates', array( $this, 'get_condition_templates' ), 10, 2 );
            add_filter( 'roen/get-simple-repeater-template-shipped-conditions-condition-fields', array( $this, 'get_condition_template_fields' ), 10, 2 );

            add_filter( 'wtars_shipped_admin/process-cost-options', array( $this, 'process_options' ), 71, 3 );
        }

        public function get_rate_conditions_title( $in_fields, $args ) {

            $module_text = esc_html__( 'Shipping', 'table-rate-shipping-rates' );

            $module_tool_text = esc_html__( 'shipping rate', 'table-rate-shipping-rates' );

            if ( 'cart-fees' == $args[ 'module' ] ) {

                $module_text = esc_html__( 'Fee', 'table-rate-shipping-rates' );

                $module_tool_text = esc_html__( 'handling fee', 'table-rate-shipping-rates' );
            }
            
            if ( 'checkout-messages' == $args[ 'module' ] ) {

                $module_text = esc_html__( 'Message', 'table-rate-shipping-rates' );

                $module_tool_text = esc_html__( 'message', 'table-rate-shipping-rates' );
            }

            $in_fields[] = array(
                'id' => 'match_mode',
                'type' => 'select2',
                'full_width' => true,
                'center_head' => true,
                /* translators: 1: module name */
                'title' => sprintf( esc_html__( '%s Conditions', 'table-rate-shipping-rates' ), $module_text ),
                /* translators: 1: module name 2: module name */
                'desc' => sprintf( esc_html__( 'List of conditions in which this %1$s should apply, empty conditions will apply this %2$s in any scenario', 'table-rate-shipping-rates' ), $module_tool_text, $module_tool_text ),
                'default' => 'match_all',
                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                'options' => array(
                    'match_all' => esc_html__( 'All conditions should match', 'table-rate-shipping-rates' ),
                    'prem_1' => esc_html__( 'At least one condition should match (Premium)', 'table-rate-shipping-rates' ),
                ),
                'width' => '320px',
            );

            return $in_fields;
        }

        public static function get_cost_conditions_title( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Conditions', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'List of conditions in which this shipping cost should apply, all specified conditions must match', 'table-rate-shipping-rates' ),
                'field_css_class' => array( 'shipped_logic_title' ),
                'last' => true,
                'fold' => array(
                    'target' => 'set_conditions',
                    'attribute' => 'value',
                    'value' => array( 'yes' ),
                    'oparator' => 'eq',
                    'clear' => false,
                ),
            );


            return $in_fields;
        }

        public function get_method_conditions_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Method Conditions', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'List of conditions in which this shipping method should apply, empty conditions will apply this shipping method in any scenario', 'table-rate-shipping-rates' ),
            );

            $in_fields[] = array(
                'id' => 'is_any',
                'type' => 'textblock',
                'show_box' => false,
                'full_width' => true,
                'center_head' => true,
                'text' => WTARS_Shipped_Admin_Page::get_premium_messages(),
                'width' => '100%',
                'box_width' => '100%',
            );

            return $in_fields;
        }

        public static function get_condition_fields( $in_fields, $args ) {

            $field = array(
                'id' => 'conditions',
                'type' => 'simple-repeater',
                'filter_id' => 'shipped-conditions',
                'new_field_args' => $args,
                'white_repeater' => ('method' == $args[ 'sub_module' ]) ? true : false,
                'repeater_size' => 'smaller',
                'buttons_sep' => false,
                'buttons_box_width' => '65px',
                'width' => '100%',
                'css_class' => array( 'shipped_conditions' ),
                'sortable' => array(
                    'enabled' => true,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'show_list' => false,
                    'button_text' => esc_html__( 'Add Condition', 'table-rate-shipping-rates' ),
                ),
            );

            if ( 'base-cost' == $args[ 'sub_module' ] || 'extra-cost' == $args[ 'sub_module' ] ) {

                $field[ 'fold' ] = array(
                    'target' => 'set_conditions',
                    'attribute' => 'value',
                    'value' => array( 'yes' ),
                    'oparator' => 'eq',
                    'clear' => false,
                );
            }

            $in_fields[] = $field;

            return $in_fields;
        }

        public function get_condition_templates( $in_templates, $repeater_args ) {

            $in_templates[] = array(
                'id' => 'condition',
            );

            return $in_templates;
        }

        public function get_condition_template_fields( $in_fields, $repeater_args ) {

            $args = $repeater_args[ 'field_args' ];

            $list = array();

            $groups = WTARS_Shipped_Admin_Condition_Types::get_groups( $args );

            foreach ( $groups as $key => $group_label ) {
                $list[ $key ][ 'label' ] = $group_label;
                $list[ $key ][ 'options' ] = WTARS_Shipped_Admin_Condition_Types::get_conditions( $key, $args );
            }


            $in_fields[] = array(
                'id' => 'condition_type',
                'type' => 'select2',
                'default' => '',
                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-grouped-list',
                'options' => $list,
                'width' => '98%',
                'box_width' => '33%',
                'dyn_switcher_id' => 'condition_type',
            );


            $conds = array();
            foreach ( $list as $grp ) {
                if ( count( $grp[ 'options' ] ) > 0 ) {
                    $conds = array_merge( $conds, array_keys( $grp[ 'options' ] ) );
                }
            }

            $disabled_list = WTARS_Shipped_Admin_Page::get_disabled_list( array(), $conds );

            foreach ( $conds as $cond ) {
                if ( in_array( $cond, $disabled_list ) ) {
                    continue;
                }

                $in_fields[] = array(
                    'id' => 'condition_type_' . $cond,
                    'type' => 'group-field',
                    'dyn_switcher_target' => 'condition_type',
                    'dyn_switcher_target_value' => $cond,
                    'fluid-group' => true,
                    'width' => '67%',
                    'css_class' => array( 'rn-last' ),
                    'last' => true,
                    'fields' => WTARS_Shipped_Admin_Condition_Types::get_condition_fields( $cond, $args ),
                );
            }

            return $in_fields;
        }

        public function process_options( $cost, $raw_cost, $args ) {

            if ( $raw_cost[ 'set_conditions' ] == 'yes' && isset( $raw_cost[ 'conditions' ] ) ) {
                $cost[ 'conditions' ] = $raw_cost[ 'conditions' ];
            }

            return $cost;
        }

    }

    new WTARS_Shipped_Admin_Logic_Types_Conditions();
}