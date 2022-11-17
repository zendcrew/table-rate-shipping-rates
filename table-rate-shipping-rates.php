<?php

/*
 * Plugin Name: Shipped - WooCommerce Table Rate Shipping
 * Plugin URI: https://wordpress.org/plugins/table-rate-shipping-rates
 * Description: Create multiple shipping rates & handling fees based on product rules and cart conditions
 * Version: 1.0.1
 * Author: zendcrew
 * Author URI: https://codecanyon.net/user/zendcrew/portfolio?ref=zendcrew
 * Text Domain: table-rate-shipping-rates
 * Domain Path: /languages/
 * Requires at least: 5.8
 * Tested up to: 6.1.1
 * Requires PHP: 5.6
 * 
 * WC requires at least: 5.6
 * WC tested up to: 7.1
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( is_admin() ) {

    require_once (dirname( __FILE__ ) . '/framework/reon_loader.php');
}

if ( !defined( 'WTARS_SHIPPED_FILE' ) ) {

    define( 'WTARS_SHIPPED_FILE', __FILE__ );
}

if ( !defined( 'WTARS_SHIPPED_METHOD_ID' ) ) {
    define( 'WTARS_SHIPPED_METHOD_ID', 'wtars_shipped' );
}

if ( !defined( 'WTARS_SHIPPED_OPTION_NAME' ) ) {
    define( 'WTARS_SHIPPED_OPTION_NAME', 'wtars_shipped' );
}

if ( !defined( 'WTARS_SHIPPED_VERSION' ) ) {
    define( 'WTARS_SHIPPED_VERSION', '1.0' );
}

if ( !class_exists( 'WTARS_Shipped_Main' ) ) {

    class WTARS_Shipped_Main {

        public function __construct() {

            add_action( 'plugins_loaded', array( $this, 'init' ), 1 );
            
            load_plugin_textdomain( 'table-rate-shipping-rates', false, dirname( plugin_basename( WTARS_SHIPPED_FILE ) ) . '/languages/' );
        }

        public function init() {

            if ( function_exists( 'WC' ) ) { // Check if WooCommerce is active

                require_once 'extensions/extensions.php';
            } else {

                add_action( 'admin_notices', array( $this, 'missing_notice' ) );
            }
        }

        public function missing_notice() {

            echo '<div class="error"><p><strong>' .  esc_html__( 'WooCommerce Table Rate Shipping requires WooCommerce to be installed and activated.', 'table-rate-shipping-rates' ) . '</strong></p></div>';
        }

    }

    new WTARS_Shipped_Main();
}

