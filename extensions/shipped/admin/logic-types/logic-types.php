<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

ReonUtil::recursive_require( dirname( __FILE__ ), array( 'logic-types.php' ) );
