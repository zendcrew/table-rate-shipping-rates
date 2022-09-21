<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once 'class.reon.util.php';
require_once 'class.reon.core.api.php';
require_once 'class.reon.core.ajax.php';
require_once 'class.reon.core.ui.php';
require_once 'class.reon.core.ui.optionpage.php';
require_once 'class.reon.core.ui.optionpage-wc.php';
require_once 'class.reon.core.ui.metabox.php';
require_once 'class.reon.core.ui.metabox-wc.php';

if ( !class_exists( 'ReonCore' ) ) {

    class ReonCore {

        public static function init() {
            do_action( 'reon/before_init' );
            ReonUI::init();
            ReonAjax::init();
            ReonOptionPage::init();
            ReonOptionPageWC::init();
            ReonMetaBox::init();
            ReonWCMetaBox::init();
            do_action( 'reon/init' );
        }

        public static function init_options( $args = array() ) {
            self::load_options( $args, true );
        }

        public static function load_options( $option_args, $cache_options = false, $default = array() ) {
            $suffix = $option_args[ 'instance_id' ];
            if ( $option_args[ 'instance_id' ] != '' & $option_args[ 'database' ] != 'custom_db' ) {
                $suffix = '_' . $option_args[ 'instance_id' ];
            }

            $opt = $default;
            if ( $option_args[ 'database' ] == 'custom_db' ) {
                $opt = apply_filters( 'reon/get-' . $option_args[ 'option_name' ] . '-options', stripslashes_deep( $opt ), $option_args[ 'instance_id' ] );
            } else if ( $option_args[ 'database' ] == 'theme_mod' ) {
                $opt = get_theme_mod( $option_args[ 'option_name' ] . $suffix, $default );
            } else {
                $opt = get_option( $option_args[ 'option_name' ] . $suffix, $default );
            }

            if ( !is_array( $opt ) ) {
                $opt = array();
            }

            $opt = apply_filters( 'reon/process-get-options-' . $option_args[ 'option_name' ], stripslashes_deep( $opt ), $option_args[ 'instance_id' ] );
            if ( $cache_options == true ) {
                $GLOBALS[ $option_args[ 'option_name' ] ] = $opt;
            } else {
                return $opt;
            }
        }

        public static function save_options( $option_args, $option = array(), $autoload = false, $field_ids = array() ) {
            $opt = self::load_options( $option_args );

            foreach ( $field_ids as $value ) {
                if ( isset( $opt[ $value ] ) ) {
                    unset( $opt[ $value ] );
                }
            }
            foreach ( $option as $key => $value ) {
                $opt[ $key ] = $value;
            }

            self::save_options_data( $opt, $option_args );
        }

        public static function reset_options( $option_args, $field_ids = array() ) {
            $option = self::load_options( $option_args );

            $opt = array();

            foreach ( $option as $field_key => $field_value ) {
                $found = false;
                foreach ( $field_ids as $value ) {
                    if ( $field_key == $value ) {
                        $found = true;
                    }
                }
                if ( $found == true ) {
                    continue;
                }
                $opt[ $field_key ] = $field_value;
            }

            self::save_options_data( $opt, $option_args, true );
        }

        private static function save_options_data( $options, $option_args, $is_reset = false ) {
            $opt = apply_filters( 'reon/process-save-options-' . $option_args[ 'option_name' ], $options, $is_reset, $option_args[ 'instance_id' ] );



            $suffix = $option_args[ 'instance_id' ];
            if ( $option_args[ 'instance_id' ] != '' & $option_args[ 'database' ] != 'custom_db' ) {
                $suffix = '_' . $option_args[ 'instance_id' ];
            }

            if ( $option_args[ 'database' ] == 'custom_db' ) {
                do_action( 'reon/save-' . $option_args[ 'option_name' ] . '-options', $opt, $option_args[ 'instance_id' ] );
            } else if ( $option_args[ 'database' ] == 'theme_mod' ) {
                set_theme_mod( $option_args[ 'option_name' ] . $suffix, $opt );
            } else {
                update_option( $option_args[ 'option_name' ] . $suffix, $opt );
            }

            if ( isset( $GLOBALS[ $option_args[ 'option_name' ] . $suffix ] ) ) {
                $GLOBALS[ $option_args[ 'option_name' ] . $suffix ] = apply_filters( 'reon/process-get-options-' . $option_args[ 'option_name' ], stripslashes_deep( $opt ), $option_args[ 'instance_id' ] );
            }
        }

        public static function process_default_option_page_fields( $fields ) {
            $result = array();
            foreach ( $fields as $field ) {
                $fld = self::process_default_field( $field );
                $result[] = $fld;
            }
            return $result;
        }

        public static function process_default_post_metabox_fields( $fields ) {
            $result = array();
            foreach ( $fields as $field ) {
                $fld = self::process_default_field( $field );
                $result[] = $fld;
            }
            return $result;
        }

        public static function process_default_post_metabox_wc_fields( $fields ) {
            $result = array();
            foreach ( $fields as $field ) {
                $fld = self::process_default_field( $field );
                $result[] = $fld;
            }
            return $result;
        }

        public static function process_default_field( $field ) {
            $result = $field;
            if ( !isset( $result[ 'id' ] ) ) {
                $result[ 'id' ] = '';
            }

            if ( !isset( $result[ 'full_width' ] ) ) {
                $result[ 'full_width' ] = false;
            }

            if ( !isset( $result[ 'center_head' ] ) ) {
                $result[ 'center_head' ] = false;
            }

            if ( !isset( $result[ 'type' ] ) ) {
                $result[ 'type' ] = 'textbox';
            }

            if ( !isset( $result[ 'sanitize_type' ] ) ) {
                $result[ 'sanitize_type' ] = $result[ 'type' ];
            }

            if ( !isset( $result[ 'title' ] ) ) {
                $result[ 'title' ] = '';
            }
            if ( !isset( $result[ 'desc' ] ) ) {
                $result[ 'desc' ] = '';
            }


            if ( !isset( $result[ 'tooltip' ] ) ) {
                $result[ 'tooltip' ] = '';
            }
            if ( !isset( $result[ 'label' ] ) ) {
                $result[ 'label' ] = array();
            }


            if ( $result[ 'type' ] == 'simple-repeater' ) {
                if ( !isset( $result[ 'left_width' ] ) ) {
                    $result[ 'left_width' ] = '100%';
                }

                if ( !isset( $result[ 'right_width' ] ) ) {
                    $result[ 'right_width' ] = '0%';
                }
            }

            if ( $result[ 'type' ] == 'select2' ) {
                if ( isset( $result[ 'data' ] ) && !is_array( $result[ 'data' ] ) ) {
                    $ar = array(
                        'source' => $result[ 'data' ],
                        'ajax' => false
                    );
                    $result[ 'data' ] = $ar;
                }

                if ( isset( $result[ 'ajax_data' ] ) && !is_array( $result[ 'ajax_data' ] ) ) {
                    $ar = array(
                        'source' => $result[ 'ajax_data' ],
                        'ajax' => true
                    );

                    $result[ 'data' ] = $ar;
                }
            }
            if ( $result[ 'type' ] == 'button-set' ) {
                if ( isset( $result[ 'data' ] ) && !is_array( $result[ 'data' ] ) ) {
                    $ar = array(
                        'source' => $result[ 'data' ],
                    );
                    $result[ 'data' ] = $ar;
                }
            }

            if ( $result[ 'type' ] == 'autoid' || $result[ 'type' ] == 'hidden' ) {
                $result[ 'is_hidden' ] = true;
            } else {
                $result[ 'is_hidden' ] = false;
            }
            return $result;
        }

    }

}

