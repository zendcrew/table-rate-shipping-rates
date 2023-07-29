<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Type_Surface_Area' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Cost_Type_Surface_Area {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-cost-type-groups', array( $this, 'get_groups' ), 70, 2 );
            add_filter( 'wtars_shipped_admin/get-cost-group-types-surface_areas', array( $this, 'get_types' ), 10, 2 );
        }
        
        public function get_groups( $in_groups, $args ) {

            /* translators: 1: cost text */
            $in_groups[ 'surface_areas' ] = sprintf( esc_html__( '%s Per Surface Area', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ] );

            return $in_groups;
        }
        
        public function get_types( $in_types, $args ) {

            $surface_area_unit = get_option( 'woocommerce_dimension_unit' );

            /* translators: 1: cost text 2: surface area unit */
            $in_types[ 'prem_18' ] = sprintf( esc_html__( '%1$s per surface area (square %2$s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $surface_area_unit );
            /* translators: 1: cost text 2: surface area unit */
            $in_types[ 'prem_19' ] = sprintf( esc_html__( '%1$s x surface area (square %2$s) (Premium)', 'table-rate-shipping-rates' ), $args[ 'format_cap_text' ], $surface_area_unit );

            return $in_types;
        }
    }
    
    new WTARS_Shipped_Admin_Cost_Type_Surface_Area();
}