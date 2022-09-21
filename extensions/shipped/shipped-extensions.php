<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Extension' ) ) {

    class WTARS_Shipped_Extension {

        public function __construct() {

            if ( !defined( 'WTARS_SHIPPED_ASSETS_URL' ) ) {
                define( 'WTARS_SHIPPED_ASSETS_URL', plugins_url( 'assets/', __FILE__ ) );
            }

            if ( is_admin() ) {
                add_action( 'reon/init', array( $this, 'load_admin_page' ) );
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 20 );
            } else {
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ), 20 );
            }

            add_action( 'woocommerce_shipping_init', array( $this, 'init_shipping_method' ) );
            add_filter( 'woocommerce_shipping_methods', array( $this, 'add_shipping_method' ) );
            add_action( 'init', array( $this, 'init_public' ) );
        }

        public function load_admin_page() {

            require_once dirname( __FILE__ ) . '/admin/admin.php';
        }

        public function init_shipping_method() {

            require_once dirname( __FILE__ ) . '/shipping-method.php';
        }

        public function add_shipping_method( $methods ) {
            
            $methods[ 'wtars_shipped' ] = 'WTARS_Shipped_Shipping_Method';
            
            return $methods;
        }

        public function init_public() {

            require_once dirname( __FILE__ ) . '/compatibility/compatibility.php';
            require_once dirname( __FILE__ ) . '/public/shipped.php';
        }

        public function enqueue_admin_scripts() {

            wp_enqueue_style( 'wtars_shipped_admin_css', WTARS_SHIPPED_ASSETS_URL . 'admin-styles.css', array(), '1.0', 'all' );

            WTARS_Shipped_Admin_Notices::get_instance()->enqueue_scripts();
        }

        public function enqueue_public_scripts() {

            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( 'tipTip', WTARS_SHIPPED_ASSETS_URL . 'tipTip.min.css', array(), '1.0', 'all' );
            wp_enqueue_style( 'shipped_public_css', WTARS_SHIPPED_ASSETS_URL . 'public-styles.min.css', array(), '1.0', 'all' );

            wp_enqueue_script( 'jquery-tipTip', WTARS_SHIPPED_ASSETS_URL . 'jquery.tipTip.min.js', array( 'jquery' ), '1.0', true );
            wp_enqueue_script( 'shipped_public_scripts_js', WTARS_SHIPPED_ASSETS_URL . 'public-scripts.min.js', array( 'jquery' ), '1.0', true );

            $cart_triggers = array( 'input[name^="payment_method"]' );

            if ( has_filter( 'wtars_shipped/get-cart-update-triggers' ) ) {
                
                $cart_triggers = apply_filters( 'wtars_shipped/get-cart-update-triggers', $cart_triggers );
            }

            $script_params = array(
                'update_triggers' => implode( ",", $cart_triggers ),
            );

            wp_localize_script( 'shipped_public_scripts_js', 'wtars_shipped', $script_params );
        }

    }

}




