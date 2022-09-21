<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_GeoLocations' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_GeoLocations {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 10, 2 );

            add_filter( 'wtars_shipped_admin/get-geo_locations-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'geo_locations' ] = esc_html__( 'GeoIP Locations', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public static function get_conditions( $in_list, $args ) {
            $in_list[ 'prem_1' ] = esc_html__( 'GeoIP Continents (Premium)', 'table-rate-shipping-rates' );
            $in_list[ 'prem_2' ] = esc_html__( 'GeoIP Countries (Premium)', 'table-rate-shipping-rates' );

            return $in_list;
        }

    }

    new WTARS_Shipped_Admin_Conditions_GeoLocations();
}