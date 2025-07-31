<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Volume' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Volume {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 80, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-volumes', array( $this, 'get_types' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            /* translators: 1: cost text */
            $in_groups[ 'volumes' ] = sprintf( esc_html__( '%s Per Volume', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }

        public function get_types( $in_types, $args ) {

            $volume_unit = get_option( 'woocommerce_dimension_unit' );

            /* translators: 1: cost text 2: volume unit */
            $in_types[ 'prem_20' ] = sprintf( esc_html__( '%1$s per volume (cubic %2$s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $volume_unit );
            /* translators: 1: cost text 2: volume unit */
            $in_types[ 'prem_21' ] = sprintf( esc_html__( '%1$s x volume (cubic %2$s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $volume_unit );

            return $in_types;
        }
        
    }

    new WTARS_Shipped_Admin_Cost_Type_Volume();
}
