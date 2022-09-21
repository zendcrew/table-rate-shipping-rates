<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Inc' ) ) {

    class WTARS_Shipped_Inc {

        public static function recursive_require( $dir, $ingore_list = array(), $subdirs = array() ) {

            $this_file = 'shipped-inc.php';

            if ( $dir_handle = opendir( $dir ) ) {
                while ( false !== ($file_path = readdir( $dir_handle )) ) {

                    if ( in_array( $file_path, $subdirs ) ) {
                        self::recursive_require( $dir . '/' . $file_path, $ingore_list, $subdirs );
                    } else {
                        $explode_entry = explode( '.', $file_path );
                        if ( isset( $explode_entry[ 1 ] ) && $explode_entry[ 1 ] == 'php' && !in_array( $file_path, $ingore_list ) && $this_file != $file_path ) {
                            require_once $dir . '/' . $file_path;
                        }
                    }
                }

                closedir( $dir_handle );
            }
        }

    }

}