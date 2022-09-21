<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//WTARS_Shipped
if ( !class_exists( 'WTARS_Shipped_Extension' ) ) {
    require_once 'shipped/shipped-extensions.php';
    new WTARS_Shipped_Extension();
}
