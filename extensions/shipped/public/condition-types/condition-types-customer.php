<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Conditions_Customer' ) ) {

    class WTARS_Shipped_Conditions_Customer {

        public function __construct() {

            add_filter( 'wtars_shipped/validate-users-condition', array( $this, 'validate_users' ), 10, 2 );
            add_filter( 'wtars_shipped/validate-logged_in_user-condition', array( $this, 'validate_logged_in_user' ), 10, 2 );
        }

        public function validate_users( $condition, $data ) {

            if ( !isset( $condition[ 'user_emails' ] ) ) {
                return false;
            }

            $rule_user_emails = $condition[ 'user_emails' ];

            if ( !count( $rule_user_emails ) ) {
                return false;
            }


            $user_email = WTARS_Shipped_Customer_Util::get_user_email( $data );

            if ( $user_email == '' ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            return WTARS_Shipped_Validation_Util::validate_value_list( $user_email, $rule_user_emails, $rule_compare );
        }

        public function validate_logged_in_user( $condition, $data ) {

            $rule_is_logged_in = $condition[ 'is_logged_in' ];

            $is_logged_in = WTARS_Shipped_Customer_Util::get_is_logged_in( $data );

            return WTARS_Shipped_Validation_Util::validate_yes_no( $is_logged_in, $rule_is_logged_in );
        }

    }

    new WTARS_Shipped_Conditions_Customer();
}