<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_DateTime' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Conditions_DateTime {

        public function __construct() {

            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 150, 2 );
            add_filter( 'wtars_shipped_admin/get-datetimes-group-conditions', array( $this, 'get_conditions' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'datetimes' ] = esc_html__( 'Dates &amp; Times', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $in_list[ 'prem_1' ] = esc_html__( 'Date (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_2' ] = esc_html__( 'Time (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_3' ] = esc_html__( 'Date &amp; Time (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_4' ] = esc_html__( 'Days Of Week (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_5' ] = esc_html__( 'Days Of Month (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_6' ] = esc_html__( 'Months Of Year (Premium)', 'table-rate-shipping-rates' );
            }

            return $in_list;
        }

    }

    new WTARS_Shipped_Admin_Conditions_DateTime();
}
