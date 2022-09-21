<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Conditions_Shipping_Address' ) ) {

    class WTARS_Shipped_Conditions_Shipping_Address {

        public function __construct() {

            add_filter( 'wtars_shipped/validate-shipping_countries-condition', array( $this, 'validate_countries' ), 10, 2 );
            add_filter( 'wtars_shipped/validate-shipping_states-condition', array( $this, 'validate_states' ), 10, 2 );
            add_filter( 'wtars_shipped/validate-shipping_postcodes-condition', array( $this, 'validate_postcodes' ), 10, 2 );
        }

        public function validate_countries( $condition, $data ) {

            $rule_countries = $condition[ 'countries' ];

            $rule_compare = $condition[ 'compare' ];

            if ( !is_array( $rule_countries ) ) {
                return false;
            }

            $country = $data[ 'wc' ][ 'package' ][ 'destination' ][ 'country' ];

            if ( empty( $country ) ) {
                return false;
            }

            return WTARS_Shipped_Validation_Util::validate_value_list( $country, $rule_countries, $rule_compare );
        }

        public function validate_states( $condition, $data ) {
            
            $rule_states = $condition[ 'states' ];

            $rule_compare = $condition[ 'compare' ];

            if ( !is_array( $rule_states ) ) {
                return false;
            }

            $country = $data[ 'wc' ][ 'package' ][ 'destination' ][ 'country' ];
            
            $dest_state = $data[ 'wc' ][ 'package' ][ 'destination' ][ 'state' ];
                        
            $state = $country . ':' . $dest_state;

            return WTARS_Shipped_Validation_Util::validate_value_list( $state, $rule_states, $rule_compare );
            
        }

        public function validate_postcodes( $condition, $data ) {
            
            $rule_postcode = $condition[ 'postcode' ];

            if ( empty( $rule_postcode ) ) {
                return false;
            }

            $rule_compare = $condition[ 'compare' ];

            $postcode = $data[ 'wc' ][ 'package' ][ 'destination' ]['postcode'];

            return WTARS_Shipped_Validation_Util::validate_match_value( $rule_compare, $postcode, $rule_postcode );
            
        }

    }

    new WTARS_Shipped_Conditions_Shipping_Address();
}
