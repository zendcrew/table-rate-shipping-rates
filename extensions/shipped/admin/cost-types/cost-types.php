<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Cost_Types' ) ) {

    ReonUtil::recursive_require( dirname( __FILE__ ), array( 'cost-types.php' ) );

    class WTARS_Shipped_Admin_Cost_Types {

        private static $cost_types = array();

        public static function get_types( $args ) {

            if ( isset( self::$cost_types[ $args[ 'module' ] ] ) ) {
                return self::$cost_types[ $args[ 'module' ] ];
            }

            $type_groups = apply_filters( 'wtars_shipped_admin/get-cost-type-groups', array(), $args );

            foreach ( $type_groups as $key => $type_group ) {
                self::$cost_types[ $args[ 'module' ] ][ $key ][ 'label' ] = $type_group;
                self::$cost_types[ $args[ 'module' ] ][ $key ][ 'options' ] = apply_filters( 'wtars_shipped_admin/get-cost-group-types-' . $key, array(), $args );
            }

            return self::$cost_types[ $args[ 'module' ] ];
        }

        public static function get_type_fields( $args ) {
            $type_fields = array();

            $target = $args[ 'fold_id' ];


            $amount_type_ids = self::get_type_ids( $args );

            $disabled_list = WTARS_Shipped_Admin_Page::get_disabled_list( array(), $amount_type_ids );

            foreach ( $amount_type_ids as $amount_type_id ) {

                if ( in_array( $amount_type_id, $disabled_list ) ) {
                    continue;
                }

                $type_flds = apply_filters( 'wtars_shipped_admin/get-cost-type-' . $amount_type_id . '-fields', array(), $args );

                foreach ( $type_flds as $type_fld ) {
                    $type_fld[ 'fold' ] = array(
                        'target' => $target,
                        'attribute' => 'value',
                        'value' => $amount_type_id,
                        'oparator' => 'eq',
                        'clear' => false,
                    );

                    $type_fields[] = $type_fld;
                }
            }

            return $type_fields;
        }

        public static function get_other_fields( $in_fields, $args ) {
            return apply_filters( 'wtars_shipped_admin/get-cost-type-fields', $in_fields, $args );
        }

        public static function get_cost_add_methods( $args ) {

            $module_text = '';

            if ( 'shipping-rates' == $args[ 'module' ] ) {
                $module_text = esc_html__( 'costs', 'table-rate-shipping-rates' );
            }

            if ( 'cart-fees' == $args[ 'module' ] ) {
                $module_text = esc_html__( 'fees', 'table-rate-shipping-rates' );
            }

            $options = array(
                'add' => sprintf( esc_html__( 'Add to previous %s', 'table-rate-shipping-rates' ), $module_text )
            );

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $options[ 'prem_1' ] = sprintf( esc_html__( 'Subtract from previous %s (Premium)', 'table-rate-shipping-rates' ), $module_text );
                $options[ 'prem_2' ] = sprintf( esc_html__( 'Override previous %s (Premium)', 'table-rate-shipping-rates' ), $module_text );
            }

            return apply_filters( 'wtars_shipped_admin/get-cost-add-methods', $options, $args );
        }

        public static function get_type_ids( $args ) {

            $amount_type_ids = array();
            foreach ( self::get_types( $args ) as $key => $amount_type ) {

                if ( isset( $amount_type[ 'options' ] ) ) {
                    foreach ( $amount_type[ 'options' ] as $sub_key => $a_type ) {
                        $amount_type_ids[] = $sub_key;
                    }
                } else {
                    $amount_type_ids[] = $key;
                }
            }

            return $amount_type_ids;
        }

        public static function get_cost_box_fields( $in_fields, $args ) {

            foreach ( apply_filters( 'wtars_shipped_admin/get-cost-box-fields', array(), $args ) as $field ) {
                $in_fields[] = $field;
            }

            return $in_fields;
        }

    }

}
