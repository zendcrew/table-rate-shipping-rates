<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Condition_Types' ) ) {

    WTARS_Shipped_Inc::recursive_require( dirname( __FILE__ ), array( 'condition-types.php' ) );

    class WTARS_Shipped_Condition_Types {

        public static function validate_conditions( $option_conditions, $data ) {

            // empty conditions will apply in all cases
            if ( !isset( $option_conditions[ 'conditions' ] ) ) {
                return true;
            }

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return WTARS_Shipped_Premium_Condition_Types::validate_conditions( $option_conditions, $data );
            }

            return self::validate_cost_conditions( $option_conditions[ 'conditions' ], $data );
        }

        public static function validate_cost_conditions( $cost_conditions, $data ) {

            // go through each conditions and validate them
            foreach ( $cost_conditions as $cond ) {

                $condition_type = $cond[ 'condition_type' ];

                $condition = array(
                    'condition_type' => $condition_type,
                );

                if ( isset( $cond[ 'condition_type_' . $condition_type ] ) ) {

                    $condition = self::get_condition_args( $condition, $cond[ 'condition_type_' . $condition_type ] );
                }
                
                //TODO: check this later
                if ( isset( $cond[ 'module' ] ) ) {

                    $condition[ 'module' ] = $cond[ 'module' ];
                }

                //TODO: check this later
                if ( isset( $cond[ 'sub_module' ] ) ) {

                    $condition[ 'sub_module' ] = $cond[ 'sub_module' ];
                }

                if ( false == apply_filters( 'wtars_shipped/validate-' . $condition_type . '-condition', $condition, $data ) ) {
                
                    return false;
                }
            }

            return true;
        }

        private static function get_condition_args( $condition, $option_conditions ) {

            foreach ( $option_conditions as $key => $condition_arg ) {

                if ( $key == 'condition_type' ) {
                    continue;
                }

                $condition[ $key ] = $condition_arg;
            }

            return $condition;
        }

    }

}