<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Condition_Types' ) ) {

    ReonUtil::recursive_require( dirname( __FILE__ ), array( 'condition-types.php' ) );

    class WTARS_Shipped_Admin_Condition_Types {

        public static function get_groups( $args ) {
            return apply_filters( 'wtars_shipped_admin/get-condition-groups', array(), $args );
        }

        public static function get_conditions( $group_id, $args ) {

            $in_list = apply_filters( 'wtars_shipped_admin/get-' . $group_id . '-group-conditions', array(), $args );

            return $in_list;
        }

        public static function get_condition_fields( $condition_id, $args ) {

            $args[ 'condition_id' ] = $condition_id;

            return apply_filters( 'wtars_shipped_admin/get-' . $condition_id . '-condition-fields', array(), $args );
        }

    }

}


