<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped' ) ) {
    
    require_once dirname( __FILE__ ) . '/shipped-locale.php';
    require_once dirname( __FILE__ ) . '/shipped-currency.php';
    require_once dirname( __FILE__ ) . '/utils/shipped-inc.php';
    require_once dirname( __FILE__ ) . '/utils/shipped-utils.php';
    require_once dirname( __FILE__ ) . '/shipped-cart.php';
    require_once dirname( __FILE__ ) . '/shipped-package.php';
    require_once dirname( __FILE__ ) . '/shipped-cache.php';
    require_once dirname( __FILE__ ) . '/shipped-weight-calculator.php';

    require_once dirname( __FILE__ ) . '/modules/shipping-rates/shipping-rates.php';
    require_once dirname( __FILE__ ) . '/modules/cart-fees/cart-fees.php';
    require_once dirname( __FILE__ ) . '/modules/checkout-messages/checkout-messages.php';


    require_once dirname( __FILE__ ) . '/condition-types/condition-types.php';
    require_once dirname( __FILE__ ) . '/cost-types/cost-types.php';
    require_once dirname( __FILE__ ) . '/total-types/total-types.php';

    class WTARS_Shipped {

        private $shipping_rates;
        private $cart_fees;
        private $messages;
        private static $wtars_shipped;

        public function __construct() {

            new WTARS_Shipped_Shipping_Rates_Engine();
            new WTARS_Shipped_Cart_Fees_Engine();
            new WTARS_Shipped_Checkout_Messages_Engine();

            $this->shipping_rates = new WTARS_Shipped_Shipping_Rates();
            $this->cart_fees = new WTARS_Shipped_Cart_Fees();
            $this->messages = new WTARS_Shipped_Checkout_Messages();

            add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'before_calculate_shipping' ), 10 );
            add_filter( 'wtars_shipped/calculate-shipping', array( $this, 'calculate_shipping' ), 10, 3 );
            add_filter( 'woocommerce_package_rates', array( $this, 'get_package_rates' ), 10, 2 );
            add_filter( 'woocommerce_shipping_packages', array( $this, 'get_shipping_packages' ), 10 );
            add_filter( 'woocommerce_after_shipping_rate', array( $this, 'do_after_shipping_rate' ), 10, 2 );
            add_filter( 'woocommerce_shipping_chosen_method', array( $this, 'get_shipping_chosen_method' ), 1, 3 );

            add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'before_checkout_contents' ), 10 );
            add_action( 'woocommerce_after_calculate_totals', array( $this, 'before_cart_contents' ), 10 );
        }

        public function before_calculate_shipping( $packages ) {

            //prepares for any possible calculations
            $pckgs = $this->shipping_rates->before_calculate( $packages );

            return $pckgs;
        }

        public function calculate_shipping( $package, $instance_id ) {

            ob_start(); // prevents any possible error
            
            //process all data 
            $data = $this->process_data( $package, $instance_id );

            $rates = $this->shipping_rates->get_calculated_rates( $data, $package, $instance_id );

            ob_clean(); // resume normal buffer

            return $rates;
        }

        public function get_package_rates( $rates, $package ) {

            $instance_ids = $this->shipping_rates->get_instance_ids_from_rates( $rates );

            foreach ( $instance_ids as $instance_id ) {

                //maybe hide methods
                $rates = $this->shipping_rates->maybe_hide_methods( $rates, $instance_id );
            }

            return $rates;
        }

        public function get_shipping_packages( $packages ) {

            $keys = array_keys( $packages );

            foreach ( $keys as $key ) {

                $instance_ids = $this->shipping_rates->get_instance_ids_from_rates( $packages[ $key ][ 'rates' ] );

                foreach ( $instance_ids as $instance_id ) {

                    //process all data 
                    $data = $this->process_data( $packages[ $key ], $instance_id );

                    //maybe hide methods
                    $packages[ $key ][ 'rates' ] = $this->shipping_rates->maybe_hide_methods( $packages[ $key ][ 'rates' ], $instance_id );

                    // apply this fees
                    $packages[ $key ] = $this->cart_fees->process_fees( $packages[ $key ], $data, $instance_id );

                    //prepare defualt rates
                    $this->shipping_rates->prepare_rates_for_display( $data, $instance_id );
                }
            }

            return $packages;
        }

        public function do_after_shipping_rate( $shipping_rate, $index ) {

            $info = $this->shipping_rates->get_rate_info( $shipping_rate );

            if ( !count( $info ) ) {
                return;
            }

            if ( isset( $info[ 'tooltip' ] ) ) {
                include 'views/tooltip.php';
            }

            if ( isset( $info[ 'desc' ] ) ) {
                include 'views/desc.php';
            }
        }

        public function get_shipping_chosen_method( $default, $rates, $chosen_method ) {

            //get default rate
            return $this->shipping_rates->get_default_rate( $default, $rates );
        }

        public function before_checkout_contents() {

            $packages = WC()->shipping->get_packages();

            $keys = array_keys( $packages );

            foreach ( $keys as $key ) {

                $instance_ids = $this->shipping_rates->get_instance_ids_from_rates( $packages[ $key ][ 'rates' ] );

                foreach ( $instance_ids as $instance_id ) {

                    //process all data 
                    $data = $this->process_data( $packages[ $key ], $instance_id );

                    //process process message
                    $this->messages->process_messages( $data, $instance_id );
                }
            }
        }

        public function before_cart_contents( $cart ) {

            if ( is_checkout() ) {
                return;
            }

            if ( !is_cart() ) {
                return;
            }

            $packages = WC()->shipping->get_packages();

            $keys = array_keys( $packages );

            foreach ( $keys as $key ) {

                $instance_ids = $this->shipping_rates->get_instance_ids_from_rates( $packages[ $key ][ 'rates' ] );

                foreach ( $instance_ids as $instance_id ) {

                    //process all data 
                    $data = $this->process_data( $packages[ $key ], $instance_id );

                    //process process message
                    $this->messages->process_messages( $data, $instance_id );
                }
            }
        }

        public static function get_option( $option_key, $instance_id, $default ) {

            if ( isset( self::$wtars_shipped[ $instance_id ][ $option_key ] ) ) {
                return self::$wtars_shipped[ $instance_id ][ $option_key ];
            }

            self::$wtars_shipped[ $instance_id ] = get_option( WTARS_SHIPPED_OPTION_NAME . '_' . $instance_id, array() );

            if ( isset( self::$wtars_shipped[ $instance_id ][ $option_key ] ) ) {
                return self::$wtars_shipped[ $instance_id ][ $option_key ];
            }

            return $default;
        }

        public static function get_rate_client_id( $instance_id, $rate_id ) {

            //get shipping rate id used on cart and checkout page
            return WTARS_SHIPPED_METHOD_ID . ':' . $instance_id . '_' . $rate_id;
        }

        public static function process_data( $package, $instance_id ) {

            // Get cached data
            $data = apply_filters( 'wtars_shipped/cache-data', array(
                'wc' => array(
                    'instance_id' => $instance_id,
                    'reload' => false,
                ) ) );

            if ( false == $data[ 'wc' ][ 'reload' ] ) {
                return self::clean_data( $data );
            }

            // Pre-process all data
            $data[ 'wc' ][ 'package' ] = WTARS_Shipped_Package::get_data( $package );
            $data[ 'wc' ][ 'cart' ] = WTARS_Shipped_Cart::get_data();

            // Process all data
            $data = apply_filters( 'wtars_shipped/process-data', $data );



            // Return processed data
            return self::clean_data( $data );
        }

        private static function clean_data( $data ) {

            //remove unwanted data
            unset( $data[ 'wc' ] );
            return $data;
        }

    }

    new WTARS_Shipped();
}