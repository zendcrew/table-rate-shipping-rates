<?php

if ( !defined( 'ABSPATH' ) ) {

    exit;
}

if ( !class_exists( 'WTARS_Shipped_Currency' ) ) {

    class WTARS_Shipped_Currency {

        private static $instance;
        private $converter_obj;
        private $converted_amounts;
        private $reverted_amounts;

        public static function get_instance(): self {

            if ( is_null( self::$instance ) ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public function __construct() {

            $this->converted_amounts = array();
            $this->reverted_amounts = array();
        }

        public function convert_amounts( $amounts, $amount_id ) {

            if ( !is_array( $amounts ) ) {

                return $this->convert_amount( $amounts, $amount_id );
            }

            foreach ( $amounts as $key => $amount ) {

                $amount_sub_id = $amount_id . '-' . $key;

                if ( is_array( $amount ) ) {

                    $amounts[ $key ] = $this->convert_amounts( $amount, $amount_sub_id );

                    continue;
                }

                $amounts[ $key ] = $this->convert_amount( $amount, $amount_sub_id );
            }

            return $amounts;
        }

        public function revert_amounts( $amounts, $amount_id ) {

            if ( !is_array( $amounts ) ) {

                return $this->revert_amount( $amounts, $amount_id );
            }

            foreach ( $amounts as $key => $amount ) {

                $amount_sub_id = $amount_id . '-' . $key;

                if ( is_array( $amount ) ) {

                    $amounts[ $key ] = $this->revert_amounts( $amount, $amount_sub_id );

                    continue;
                }

                $amounts[ $key ] = $this->revert_amount( $amount, $amount_sub_id );
            }

            return $amounts;
        }

        public function convert_amount( $amount, $amount_id ) {

            if ( !$this->can_convert_amount( $amount_id ) ) {

                return $amount;
            }

            $amount_key = ( string ) $amount;

            if ( isset( $this->converted_amounts[ $amount_key ] ) ) {

                return $this->converted_amounts[ $amount_key ];
            }

            $this->converted_amounts[ $amount_key ] = $amount;

            $converter = $this->get_converter();

            if ( !$converter ) {

                return $this->converted_amounts[ $amount_key ];
            }

            if ( method_exists( $converter, 'convert_amount' ) ) {

                $this->converted_amounts[ $amount_key ] = $converter->convert_amount( $amount );
            }

            return $this->converted_amounts[ $amount_key ];
        }

        public function revert_amount( $amount, $amount_id ) {

            if ( !$this->can_revert_amount( $amount_id ) ) {

                return $amount;
            }

            $amount_key = ( string ) $amount;

            if ( isset( $this->reverted_amounts[ $amount_key ] ) ) {

                return $this->reverted_amounts[ $amount_key ];
            }

            $this->reverted_amounts[ $amount_key ] = $amount;

            $converter = $this->get_converter();

            if ( !$converter ) {

                return $this->reverted_amounts[ $amount_key ];
            }

            if ( method_exists( $converter, 'revert_amount' ) ) {

                $this->reverted_amounts[ $amount_key ] = $converter->revert_amount( $amount );
            }

            return $this->reverted_amounts[ $amount_key ];
        }

        private function can_convert_amount( $amount_id ) {

            $converter = $this->get_converter();

            if ( !$converter ) {

                return false;
            }

            if ( method_exists( $converter, 'can_convert_amount' ) ) {

                return $converter->can_convert_amount( $amount_id );
            }

            return false;
        }

        private function can_revert_amount( $amount_id ) {

            $converter = $this->get_converter();

            if ( !$converter ) {

                return false;
            }

            if ( method_exists( $converter, 'can_revert_amount' ) ) {

                return $converter->can_revert_amount( $amount_id );
            }

            return false;
        }

        private function get_converter() {

            if ( !is_null( $this->converter_obj ) ) {

                return $this->converter_obj;
            }

            $this->converter_obj = false;

            $converters = $this->register_converters();

            if ( !is_array( $converters ) ) {

                return $this->converter_obj;
            }

            foreach ( $converters as $converter ) {

                if ( !class_exists( $converter ) ) {

                    continue;
                }

                if ( method_exists( $converter, 'get_instance' ) ) {

                    $this->converter_obj = $converter::get_instance();
                } else {

                    $this->converter_obj = new $converter();
                }

                return $this->converter_obj;
            }

            return $this->converter_obj;
        }

        private function register_converters() {

            $converters = array();

            //TODO: Remove this block later
            if ( !defined( 'WTARS_SHIPPED_DEV_CURRENCY' ) ) {

                return $converters;
            }
            //TODO: End of block

            if ( class_exists( 'WTARS_Shipped_CURCY' ) ) {

                $converters[ 'curcy' ] = 'WTARS_Shipped_CURCY';
            }

            if ( class_exists( 'WTARS_Shipped_WOOCS' ) ) {

                $converters[ 'woocs' ] = 'WTARS_Shipped_WOOCS';
            }

            if ( class_exists( 'WTARS_Shipped_YayCurrency' ) ) {

                $converters[ 'yaycurrency' ] = 'WTARS_Shipped_YayCurrency';
            }

            return apply_filters( 'wtars_shipped/register-currency-converters', $converters );
        }

    }

}

