<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Checkout_Messages_Panel_Options' ) ) {

    class WTARS_Shipped_Admin_Checkout_Messages_Panel_Options {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/checkout-messages/get-panel-option-fields', array( $this, 'get_fields' ), 10, 2 );
        }

        public function get_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 4,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__( 'Adds a private note for reference purposes', 'table-rate-shipping-rates' ),
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Admin Note', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'message_type',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Notification Type', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Controls cart notification type', 'table-rate-shipping-rates' ),
                        'default' => 'error',
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => apply_filters( 'wtars_shipped_admin/get-message-types', $this->get_message_type_options() ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'always_show',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Always Show', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Always show message', 'table-rate-shipping-rates' ),
                        'default' => 'yes',
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => $this->get_always_show_options(),
                        'width' => '100%',
                    ),
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 1,
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Message', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'A text to display on the cart / checkout pages. html, shortcodes and {{Parameter ID}} can also by included in the message.', 'table-rate-shipping-rates' ),
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'message',
                        'type' => 'textarea',
                        'sanitize_type' => 'shipped_kses_post',
                        'column_size' => 1,
                        'default' => '',
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

        private function get_message_type_options() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return WTARS_Shipped_Premium_Admin_Checkout_Messages_Panel_Options::get_message_type_options();
            }

            return array(
                'error' => esc_html__( 'Error message', 'table-rate-shipping-rates' ),
                'prem_1' => esc_html__( 'Notice message (Premium)', 'table-rate-shipping-rates' ),
                'prem_2' => esc_html__( 'Success message (Premium)', 'table-rate-shipping-rates' ),
            );
        }

        private function get_always_show_options() {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return WTARS_Shipped_Premium_Admin_Checkout_Messages_Panel_Options::get_always_show_options();
            }

            return array(
                'prem_1' => esc_html__( 'No (Premium)', 'table-rate-shipping-rates' ),
                'yes' => esc_html__( 'Yes', 'table-rate-shipping-rates' ),
            );
        }

    }

    new WTARS_Shipped_Admin_Checkout_Messages_Panel_Options();
}