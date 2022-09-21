<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Customer' ) ) {

    class WTARS_Shipped_Admin_Conditions_Customer {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 130, 2 );
            add_filter( 'wtars_shipped_admin/get-customers-group-conditions', array( $this, 'get_conditions' ), 10, 2 );

            add_filter( 'wtars_shipped_admin/get-users-condition-fields', array( $this, 'get_users_fields' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-logged_in_user-condition-fields', array( $this, 'get_logged_in_user_fields' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'customers' ] = esc_html__( 'Customers', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $in_list[ 'users' ] = esc_html__( 'Customers', 'table-rate-shipping-rates' );
                $in_list[ 'logged_in_user' ] = esc_html__( 'Customer Is Logged In', 'table-rate-shipping-rates' );
            }

            return $in_list;
        }

        public function get_users_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                    'none' => esc_html__( 'None in the list', 'table-rate-shipping-rates' ),
                ),
                'width' => '98%',
                'box_width' => '25%',
            );

            $in_fields[] = array(
                'id' => 'user_emails',
                'type' => 'select2',
                'multiple' => true,
                'minimum_input_length' => 2,
                'placeholder' => esc_html__( 'Search users...', 'table-rate-shipping-rates' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'users',
                    'ajax' => true,
                    'value_col' => 'email',
                    'show_value' => true,
                ),
                'width' => '100%',
                'box_width' => '75%',
            );



            return $in_fields;
        }

        public function get_logged_in_user_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'is_logged_in',
                'type' => 'select2',
                'default' => 'yes',
                'options' => array(
                    'no' => esc_html__( 'No', 'table-rate-shipping-rates' ),
                    'yes' => esc_html__( 'Yes', 'table-rate-shipping-rates' ),
                ),
                'width' => '100%',
                'box_width' => '100%',
            );



            return $in_fields;
        }

    }

    new WTARS_Shipped_Admin_Conditions_Customer();
}