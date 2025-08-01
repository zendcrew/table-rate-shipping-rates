<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Checkout_Messages_Section' ) ) {

    ReonUtil::recursive_require( dirname( __FILE__ ), array( 'checkout-messages-section.php' ) );

    class WTARS_Shipped_Admin_Checkout_Messages_Section extends WTARS_Shipped_Admin_Section {

        protected function init() {

            add_filter( 'get-option-page-' . $this->get_option_name() . 'section-checkout_messages-fields', array( $this, 'get_fields' ), 10, 2 );

            add_filter( 'reon/get-repeater-field-checkout_messages-templates', array( $this, 'get_rule_templates' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-checkout_messages-checkout_message-fields', array( $this, 'get_rule_fields' ), 10, 2 );
            add_filter( 'roen/get-repeater-template-checkout_messages-checkout_message-head-fields', array( $this, 'get_rule_head_fields' ), 10, 2 );

            add_filter( 'reon/process-save-options-' . $this->get_option_name(), array( $this, 'process_options' ), 10 );
        }

        public function get_fields( $in_fields, $section_id ) {

            $in_fields[] = array(
                'id' => 'checkout_messages_settings',
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
                                'id' => 'mode',
                                'type' => 'select2',
                                'column_size' => 3,
                                'column_title' => esc_html__( 'Apply Mode', 'table-rate-shipping-rates' ),
                                'tooltip' => esc_html__( 'Controls cart message apply mode', 'table-rate-shipping-rates' ),
                                'default' => 'all',
                                'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                                'data' => 'shipped:checkout_messages_modes',
                                'width' => '100%',
                            ),
                        ),
                    ),
                ),
            );


            $max_sections = 1;
            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $max_sections = 99999;
            }

            $in_fields[] = array(
                'id' => 'checkout_messages',
                'type' => 'repeater',
                'white_repeater' => false,
                'repeater_size' => 'small',
                'accordions' => true,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => true,
                'width' => '100%',
                'max_sections' => $max_sections,
                'max_sections_msg' => esc_html__( 'Please upgrade to premium version in order to add more messages', 'table-rate-shipping-rates' ),
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
                    'button_text' => esc_html__( 'New Cart Notification', 'table-rate-shipping-rates' ),
                ),
            );

            return $in_fields;
        }

        public function get_rule_templates( $in_templates, $repeater_args ) {
            if ( $repeater_args[ 'screen' ] == 'option-page' && $repeater_args[ 'option_name' ] == $this->get_option_name() ) {

                $in_templates[] = array(
                    'id' => 'checkout_message',
                    'head' => array(
                        'title' => '',
                        'defaut_title' => esc_html__( 'Cart Notification', 'table-rate-shipping-rates' ),
                        'defaut_subtitle' => esc_html__( 'Cart Notification', 'table-rate-shipping-rates' ),
                        'title_field' => 'admin_note',
                        'subtitle_field' => 'message_type',
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

            return apply_filters( 'wtars_shipped_admin/checkout-messages/get-panels', array(), $args );
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
                        'data' => 'shipped:checkout_messages_methods',
                        'width' => '265px',
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

            if ( isset( $options[ 'checkout_messages' ] ) ) {
                $options[ 'checkout_messages' ] = $this->process_rules( $options[ 'checkout_messages' ] );
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

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $chck_msg_sec = new WTARS_Shipped_Premium_Admin_Checkout_Messages_Section( $this->get_option_name(), $this->get_instance_id() );

                $rule = $chck_msg_sec->process_rule( $rule );
            }

            return $rule;
        }

    }

}