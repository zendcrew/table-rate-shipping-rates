<?php

/*
 * Plugin Name: Shipped - WooCommerce Table Rate Shipping
 * Plugin URI: https://wordpress.org/plugins/table-rate-shipping-rates
 * Description: Create multiple shipping rates & handling fees based on product rules and cart conditions
 * Version: 1.3.2
 * Author: zendcrew
 * Author URI: https://codecanyon.net/user/zendcrew/portfolio?ref=zendcrew
 * Text Domain: table-rate-shipping-rates
 * Domain Path: /languages/
 * Requires at least: 5.8
 * Requires PHP: 5.6
 * WC requires at least: 5.6
 * 
 * Tested up to: 6.7
 * WC tested up to: 9.7
 * 
 * Requires Plugins: woocommerce
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

    define( 'WTARS_SHIPPED_VERSION', '1.3.2' );
}

if ( !class_exists( 'WTARS_Shipped_Main' ) ) {

    class WTARS_Shipped_Main {

        public function __construct() {

            add_action( 'plugins_loaded', array( $this, 'init' ), 1 );

            add_action( 'before_woocommerce_init', array( $this, 'before_woocommerce_init' ) );

            add_action( 'init', array( $this, 'load_textdomain' ) );
        }
        
        public function load_textdomain() {

            load_plugin_textdomain( 'table-rate-shipping-rates', false, dirname( plugin_basename( WTARS_SHIPPED_FILE ) ) . '/languages/' );
        }

        public function init() {

            if ( function_exists( 'WC' ) ) { // Check if WooCommerce is active
                
                require_once 'extensions/extensions.php';
            } else {

                add_action( 'admin_notices', array( $this, 'missing_notice' ) );
            }
        }

        public function before_woocommerce_init() {

            // Check for HPOS
            if ( !class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {

                return;
            }

            // Adds support for HPOS
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WTARS_SHIPPED_FILE, true );
        }

        public function missing_notice() {

            echo '<div class="error"><p><strong>' . esc_html__( 'WooCommerce Table Rate Shipping requires WooCommerce to be installed and activated.', 'table-rate-shipping-rates' ) . '</strong></p></div>';
        }

    }

    new WTARS_Shipped_Main();
}

