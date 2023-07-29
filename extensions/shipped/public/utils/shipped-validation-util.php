<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Validation_Util' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Validation_Util {

        public static function validate_yes_no( $value, $rule_yes_no ) {
            $yes_value = 'no';
            if ( $value == true ) {
                $yes_value = 'yes';
            }
            return ($yes_value == $rule_yes_no);
        }

        public static function validate_value_list( $value, $rule_list, $validate_type ) {
            
            $found = in_array( $value, $rule_list );

            $is_equals = ('in_list' == $validate_type );

            return ($found == $is_equals);
        }

        public static function validate_match_value( $validate_type, $value, $rule_value ) {
            $found = false;
            foreach ( explode( ',', $rule_value ) as $rule_val ) {
                $t_value = trim( $rule_val );
                if ( $t_value == $value ) {
                    $found = true;
                    break;
                }

                if ( self::ends_with( '*', $t_value ) == true ) {

                    if ( self::starts_with( preg_replace( '/\*/', '', $t_value ), $value ) == true ) {
                        $found = true;
                        break;
                    }
                }

                if ( self::starts_with( '*', $t_value ) == true ) {

                    if ( self::ends_with( preg_replace( '/\*/', '', $t_value ), $value ) == true ) {
                        $found = true;
                        break;
                    }
                }
                if ( self::contains( '-', $t_value ) == true ) {
                    $t_value = preg_replace( '/ /', '', $t_value );
                    $t_value = preg_replace( '/\[/', '', $t_value );
                    $t_value = preg_replace( '/\]/', '', $t_value );
                    $t_arr = explode( '-', $t_value );

                    if ( count( $t_arr ) == 2 ) {
                        if ( !is_numeric( $value ) ) {
                            continue;
                        }
                        if ( $value >= $t_arr[ 0 ] && $value <= $t_arr[ 1 ] ) {
                            $found = true;
                            break;
                        }
                    }
                }
            }

            if ( $validate_type == 'match' ) {
                return $found;
            }
            return !$found;
        }

        public static function validate_value( $validate_type, $value, $rule_value = '', $rule_yes_no = 'no' ) {

            if ( $validate_type == 'empty' ) {
                $is_empty = false;
                if ( $value == '' ) {
                    $is_empty = true;
                }
                return self::validate_yes_no( $is_empty, $rule_yes_no );
            }

            if ( $validate_type == 'contains' ) {
                return self::contains( $rule_value, $value );
            }

            if ( $validate_type == 'not_contains' ) {
                return !(self::contains( $rule_value, $value ));
            }

            if ( $validate_type == 'begins' ) {
                return self::starts_with( $rule_value, $value );
            }

            if ( $validate_type == 'ends' ) {
                return self::ends_with( $rule_value, $value );
            }

            if ( $validate_type == '>=' ) {
                if ( $value === '' ) {
                    return false;
                }
                return ($value >= $rule_value);
            }
            if ( $validate_type == '>' ) {
                if ( $value === '' ) {
                    return false;
                }
                return ($value > $rule_value);
            }
            if ( $validate_type == '<=' ) {
                if ( $value === '' ) {
                    return false;
                }
                return ($value <= $rule_value);
            }
            if ( $validate_type == '<' ) {
                if ( $value === '' ) {
                    return false;
                }
                return ($value < $rule_value);
            }
            if ( $validate_type == '==' ) {
                return ($value == $rule_value);
            }
            if ( $validate_type == '!=' ) {
                return ($value != $rule_value);
            }

            if ( $validate_type == 'checked' ) {
                $is_empty = false;
                if ( $value == 'yes' ) {
                    $is_empty = true;
                }
                return self::validate_yes_no( $is_empty, $rule_yes_no );
            }


            return false;
        }

        public static function validate_list_list( $list, $rule_list, $validate_type ) {
            if ( $validate_type == 'in_list' || $validate_type == 'none' ) {
                return self::validate_list_in_list( $list, $rule_list, $validate_type );
            }

            if ( $validate_type == 'in_all_list' ) {
                return self::validate_list_all_in_list( $list, $rule_list );
            }

            if ( $validate_type == 'in_list_only' ) {
                return self::validate_list_only_in_list( $list, $rule_list );
            }

            if ( $validate_type == 'in_all_list_only' ) {
                $all_list = self::validate_list_all_in_list( $list, $rule_list );
                $only_list = self::validate_list_only_in_list( $list, $rule_list );
                return ($all_list == true && $only_list == true);
            }


            return false;
        }

        private static function validate_list_in_list( $list, $rule_list, $validate_type ) {
            
            $found = false;

            $is_equals = ('in_list' == $validate_type );

            foreach ( $list as $lst ) {

                if ( true == $found ) {

                    break;
                }

                if ( self::validate_value_list( $lst, $rule_list, 'in_list' ) ) {

                    $found = true;
                }
            }

            return ($found == $is_equals);
        }

        private static function validate_list_all_in_list( $list, $rule_list ) {
            
            $found_count = 0;

            $rule_list_count = count( $rule_list );

            foreach ( $list as $lst ) {

                if ( self::validate_value_list( $lst, $rule_list, 'in_list' ) ) {

                    $found_count++;
                }
            }

            return ($found_count == $rule_list_count);
        }

        private static function validate_list_only_in_list( $list, $rule_list ) {
            
            $found_count = 0;

            $list_count = count( $list );

            foreach ( $rule_list as $lst ) {

                if ( self::validate_value_list( $lst, $list, 'in_list' ) ) {

                    $found_count++;
                }
            }

            return ($found_count == $list_count);
        }

        private static function starts_with( $rule_value, $value ) {
            return (strpos( strtolower( $value ), strtolower( $rule_value ) ) === 0);
        }

        private static function ends_with( $rule_value, $value ) {
            $haystack = strtolower( $value );
            $ending = strtolower( $rule_value );
            return (strpos( $haystack, $ending, strlen( $haystack ) - strlen( $ending ) ) !== false);
        }

        private static function contains( $rule_value, $value ) {
            return (preg_match( "/{$rule_value}/i", $value ));
        }

    }

}