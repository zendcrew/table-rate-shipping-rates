<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Data_List' ) ) {

    ReonUtil::recursive_require( dirname( __FILE__ ), array( 'data-list.php' ) );

    class WTARS_Shipped_Admin_Data_List {

        public function __construct() {
            add_filter( 'reon/get-data-list', array( $this, 'get_data_list' ), 10, 2 );
        }

        public function get_data_list( $result, $data_args ) {

            $db_source = explode( ':', $data_args[ 'source' ] );
            $data_args[ 'source' ] = '';
            if ( count( $db_source ) >= 2 && $db_source[ 0 ] == 'shipped' ) {
                if ( count( $db_source ) > 2 ) {
                    $n_src = array();
                    for ( $i = 2; $i < count( $db_source ); $i++ ) {
                        $n_src[] = $db_source[ $i ];
                    }
                    $data_args[ 'source' ] = implode( ':', $n_src );
                }

                return apply_filters( 'wtars_shipped_admin/get-data-list-' . $db_source[ 1 ], $result, $data_args );
            }


            return $result;
        }

    }

    new WTARS_Shipped_Admin_Data_List();
}