<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Page' ) ) {

    require_once (dirname( __FILE__ ) . '/abstract-section.php');
    require_once (dirname( __FILE__ ) . '/shipping-rates-section/shipping-rates-section.php');
    require_once (dirname( __FILE__ ) . '/cart-fees-section/cart-fees-section.php');
    require_once (dirname( __FILE__ ) . '/checkout-messages-section/checkout-messages-section.php');
    require_once (dirname( __FILE__ ) . '/settings-section/settings-section.php');

    class WTARS_Shipped_Admin_Page {

        public function __construct() {

            //Initialize page
            $option_name = $this->get_option_name();
            $instance_id = $this->get_instance_id();
            $this->init_page( $option_name );
            $this->init_data_store( $instance_id );

            add_filter( 'wtars_shipped_admin/get-disabled-list', array( $this, 'get_disabled_list' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-disabled-grouped-list', array( $this, 'get_grouped_disabled_list' ), 10, 2 );

            //Initialize Sections
            new WTARS_Shipped_Admin_Shipping_Rates_Section( $option_name, $instance_id );
            new WTARS_Shipped_Admin_Cart_Fees_Section( $option_name, $instance_id );
            new WTARS_Shipped_Admin_Checkout_Messages_Section( $option_name, $instance_id );
            new WTARS_Shipped_Admin_Settings_Section( $option_name, $instance_id );

            //Configure section menus
            add_filter( 'reon/get-option-page-' . $option_name . '-sections', array( $this, 'config_sections' ), 10 );
            add_action( 'reon/before-save-' . $option_name . '-options', array( $this, 'init_data_store' ), 1 );
            add_action( 'reon/before-import-' . $option_name . '-options', array( $this, 'init_data_store' ), 1 );

            // Reon framework custom sanitizer for shipped
            add_filter( 'reon/sanitize-shipped_kses_post', array( $this, 'sanitize_shipped_kses_post_box' ), 1, 4 );

            add_filter( 'plugin_action_links_' . plugin_basename( WTARS_SHIPPED_FILE ), array( $this, 'get_plugin_links' ), 10, 1 );
        }

        public function init_page( $option_name ) {
            /* translators: 1: plugin version */
            $version_text = sprintf( esc_html__( 'Lite v%s', 'table-rate-shipping-rates' ), WTARS_SHIPPED_VERSION );

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                /* translators: 1: plugin version */
                $version_text = sprintf( esc_html__( 'Premium v%s', 'table-rate-shipping-rates' ), WTARS_SHIPPED_VERSION );
            }

            $args = array(
                'option_name' => $option_name,
                'database' => 'option',
                'url_base' => 'admin.php?page=wc-settings&tab=shipping',
                'tabs_key' => 'tb',
                'use_slug_url' => false,
                'default_min_height' => '700px',
                'auto_load' => false,
                'page_id' => 'wtars_shipped_page',
                'enable_section_title' => true,
                'width' => 'auto',
                'aside_width' => '210px',
                'user_capability' => 'manage_woocommerce',
                'display' => array(
                    'enabled' => true,
                    'image' => WTARS_SHIPPED_ASSETS_URL . 'images/aside_logo.png',
                    'title' => esc_html__( 'Shipped', 'table-rate-shipping-rates' ),
                    'version' => $version_text,
                    'sub_title' => esc_html__( 'Table Rate Shipping & Fees', 'table-rate-shipping-rates' ),
                    'styles' => array(
                        'bg_image' => WTARS_SHIPPED_ASSETS_URL . 'images/aside_bg.png',
                        'bg_color' => '#0073aa',
                        'color' => '#fff',
                    ),
                ),
                'ajax' => array(
                    'save_msg' => esc_html__( 'Done!!', 'table-rate-shipping-rates' ),
                    'save_error_msg' => esc_html__( 'Unable to save your settings', 'table-rate-shipping-rates' ),
                    'reset_msg' => esc_html__( 'Done!!', 'table-rate-shipping-rates' ),
                    'reset_error_msg' => esc_html__( 'Unable to reset reset your settings', 'table-rate-shipping-rates' ),
                    'nonce_error_msg' => esc_html__( 'invalid nonce', 'table-rate-shipping-rates' ),
                ),
                'wc_menu' => array(
                    'enable' => false,
                ),
                'import_export' => array(
                    'enable' => true,
                    'min_height' => '565px',
                    'title' => esc_html__( 'Import / Export', 'table-rate-shipping-rates' ),
                    'header_title' => esc_html__( 'Import / Export', 'table-rate-shipping-rates' ),
                    'import' => array(
                        'title' => esc_html__( 'Import Settings', 'table-rate-shipping-rates' ),
                        'desc' => esc_html__( 'Here you can import new settings. Simply paste the settings url or data on the field below.', 'table-rate-shipping-rates' ),
                        'url_button_text' => esc_html__( 'Import from url', 'table-rate-shipping-rates' ),
                        'url_textbox_desc' => esc_html__( "Paste the url to another site's settings below and click the 'Import Now' button.", 'table-rate-shipping-rates' ),
                        'url_textbox_hint' => esc_html__( "Paste the url to another site's settings here...", 'table-rate-shipping-rates' ),
                        'data_button_text' => esc_html__( 'Import Data', 'table-rate-shipping-rates' ),
                        'data_textbox_desc' => esc_html__( "Paste your backup settings below and click the 'Import Now' button.", 'table-rate-shipping-rates' ),
                        'data_textbox_hint' => esc_html__( 'Paste your backup settings here...', 'table-rate-shipping-rates' ),
                        'import_button_text' => esc_html__( 'Import Now', 'table-rate-shipping-rates' ),
                        'warn_text' => esc_html__( 'Warning! This will override all existing settings. proceed with caution!', 'table-rate-shipping-rates' ),
                    ),
                    'export' => array(
                        'title' => esc_html__( 'Export Settings', 'table-rate-shipping-rates' ),
                        'desc' => esc_html__( 'Here you can backup your current settings. You can later use it to restore your settings.', 'table-rate-shipping-rates' ),
                        'download_button_text' => esc_html__( 'Download Data', 'table-rate-shipping-rates' ),
                        'url_button_text' => esc_html__( 'Export url', 'table-rate-shipping-rates' ),
                        'url_textbox_desc' => esc_html__( 'Copy the url below, use it to transfer the settings from this site.', 'table-rate-shipping-rates' ),
                        'data_button_text' => esc_html__( 'Export Data', 'table-rate-shipping-rates' ),
                        'data_textbox_desc' => esc_html__( 'Copy the data below, use it as your backup.', 'table-rate-shipping-rates' ),
                    ),
                ),
                'header_buttons' => array(
                    'reset_all_text' => esc_html__( 'Reset All', 'table-rate-shipping-rates' ),
                    'reset_section_text' => esc_html__( 'Reset Section', 'table-rate-shipping-rates' ),
                    'save_section_text' => esc_html__( 'Save Section', 'table-rate-shipping-rates' ),
                ),
                'footer_buttons' => array(
                    'reset_all_text' => esc_html__( 'Reset All', 'table-rate-shipping-rates' ),
                    'reset_section_text' => esc_html__( 'Reset Section', 'table-rate-shipping-rates' ),
                    'save_section_text' => esc_html__( 'Save Section', 'table-rate-shipping-rates' ),
                ),
                'page_links' => $this->get_page_links(),
                'social_links' => $this->get_social_links(),
            );
            Reon::set_wc_option_page( $args );
        }

        public function config_sections( $in_sections ) {

            $in_sections[] = array(
                'title' => esc_html__( 'Method Settings', 'table-rate-shipping-rates' ),
                'header_title' => esc_html__( 'Method Settings', 'table-rate-shipping-rates' ),
                'id' => 'settings',
                'group' => 4,
            );

            $in_sections[] = array(
                'title' => esc_html__( 'Shipping Rates', 'table-rate-shipping-rates' ),
                'header_title' => esc_html__( 'Shipping Rates', 'table-rate-shipping-rates' ),
                'id' => 'shipping_rates',
                'group' => 1,
            );

            $in_sections[] = array(
                'title' => esc_html__( 'Handling Fees', 'table-rate-shipping-rates' ),
                'header_title' => esc_html__( 'Handling Fees', 'table-rate-shipping-rates' ),
                'id' => 'cart_fees',
                'group' => 2,
            );

            $in_sections[] = array(
                'title' => esc_html__( 'Cart Notifications', 'table-rate-shipping-rates' ),
                'header_title' => esc_html__( 'Cart Notifications', 'table-rate-shipping-rates' ),
                'id' => 'checkout_messages',
                'group' => 3,
            );

            return $in_sections;
        }

        public function get_option_name() {

            return WTARS_SHIPPED_OPTION_NAME;
        }

        public function get_instance_id() {

            if ( isset( $_GET[ 'instance_id' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

                $instance_id = sanitize_key( $_GET[ 'instance_id' ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

                if ( is_numeric( $instance_id ) ) {

                    return (( int ) $instance_id);
                }
            }

            return 0;
        }

        public static function get_premium_messages( $message_id = '' ) {

            $premium_url = esc_url( "https://codecanyon.net/item/woocommerce-table-rate-shipping/39691473?ref=zendcrew" );
            $message = esc_html__( 'This feature is available on premium version', 'table-rate-shipping-rates' );
            $link_text = esc_html__( 'Premium Feature', 'table-rate-shipping-rates' );

            switch ( $message_id ) {
                case 'short_message':
                    $message = esc_html__( 'Available on premium version', 'table-rate-shipping-rates' );
                    return '<a href="' . $premium_url . '" target="_blank">' . $link_text . '</a>';

                default:

                    return '<a href="' . $premium_url . '" target="_blank">' . $link_text . '</a> - ' . $message;
            }
        }

        public function get_disabled_list_keys( $options ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return array();
            }

            $d_list = array();
            $prem_max = count( $options );


            for ( $i = 1; $i <= $prem_max; $i++ ) {

                $d_key = 'prem_' . $i;

                if ( is_array( $d_key, $options ) ) {

                    $d_list[] = $d_key;
                }
            }

            return $d_list;
        }

        public static function get_disabled_list( $list, $options ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return array();
            }

            $d_list = array();
            $prem_max = count( $options );

            for ( $i = 1; $i <= $prem_max; $i++ ) {

                $d_key = 'prem_' . $i;

                if ( isset( $options[ $d_key ] ) ) {

                    $d_list[] = $d_key;
                }
            }

            return $d_list;
        }

        public function get_grouped_disabled_list( $list, $grouped_options ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                return array();
            }

            $options = array();

            foreach ( $grouped_options as $grouped_option ) {

                if ( !isset( $grouped_option[ 'options' ] ) ) {

                    continue;
                }

                foreach ( $grouped_option[ 'options' ] as $key => $option ) {

                    $options[ $key ] = $option;
                }
            }

            return self::get_disabled_list( $list, $options );
        }

        public function sanitize_shipped_kses_post_box( $sanitized_option, $raw_option, $field_id, $children ) {

            return wp_kses_post( $raw_option );
        }

        public function init_data_store( $instance_id ) {

            global $wtars_shipped;

            $wtars_shipped = get_option( $this->get_option_name() . '_' . $instance_id, array() );
        }

        public function get_plugin_links( $links ) {

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                unset( $links[ 'deactivate' ] );

                $add_on_text = esc_html__( 'Shipped Premium', 'table-rate-shipping-rates' );

                /* translators: 1: plugin name */
                $required_text = sprintf( esc_html__( 'Required by %s', 'table-rate-shipping-rates' ), $add_on_text );

                $no_deactivate_tag = '<span style="color: #313639">' . $required_text . '</span>';

                array_unshift( $links, $no_deactivate_tag );

                return $links;
            }

            $doc_link = '<a href="' . esc_url( 'https://support.zendcrew.cc/portal/en/kb/woocommerce-table-rate-shipping' ) . '" target="_blank">' . esc_html__( 'Documentation', 'table-rate-shipping-rates' ) . '</a>';

            array_unshift( $links, $doc_link );

            $settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping' );

            $settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'table-rate-shipping-rates' ) . '</a>';

            array_unshift( $links, $settings_link );

            return $links;
        }

        private function get_page_links() {

            $page_links = array(
                array(
                    'id' => 'wtars_documentation',
                    'title' => esc_html__( 'Documentation', 'table-rate-shipping-rates' ),
                    'icon' => 'fa fa-file-text',
                    'href' => esc_url( 'https://support.zendcrew.cc/portal/en/kb/woocommerce-table-rate-shipping' ),
                    'target' => '_blank',
                    'show_in' => 'both'
                ),
            );

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $page_links[] = array(
                    'id' => 'wtars_help',
                    'title' => esc_html__( 'Help', 'table-rate-shipping-rates' ),
                    'icon' => 'fa fa-question-circle',
                    'href' => esc_url( 'https://support.zendcrew.cc/portal/en/newticket' ),
                    'target' => '_blank',
                    'show_in' => 'both'
                );
            } else {

                $page_links[] = array(
                    'id' => 'wtars_get_premium',
                    'title' => esc_html__( 'Premium Version', 'table-rate-shipping-rates' ),
                    'icon' => 'fa fa-file-text-o',
                    'href' => esc_url( 'https://codecanyon.net/item/woocommerce-table-rate-shipping/39691473?ref=zendcrew' ),
                    'target' => '_blank',
                    'show_in' => 'both'
                );
            }

            return $page_links;
        }

        private function get_social_links() {

            return array(
                array(
                    'id' => 'wtars_facebook',
                    'title' => esc_html__( 'Facebook', 'table-rate-shipping-rates' ),
                    'icon' => 'fa fa-facebook',
                    'href' => esc_url( 'http://www.facebook.com/zendcrew' ),
                    'target' => '_blank',
                ),
                array(
                    'id' => 'wtars_linkedin',
                    'title' => esc_html__( 'LinkedIn', 'table-rate-shipping-rates' ),
                    'icon' => 'fa fa-linkedin',
                    'href' => esc_url( 'https://www.linkedin.com/company/zendcrew' ),
                    'target' => '_blank',
                ),
                array(
                    'id' => 'wtars_stack_overflow',
                    'title' => esc_html__( 'Stack Overflow', 'table-rate-shipping-rates' ),
                    'icon' => 'fa fa-stack-overflow',
                    'href' => esc_url( 'https://stackoverflow.com/users/8692713/zendcrew' ),
                    'target' => '_blank',
                ),
                array(
                    'id' => 'wtars_instagram',
                    'title' => esc_html__( 'Instagram', 'table-rate-shipping-rates' ),
                    'icon' => 'fa fa-instagram',
                    'href' => esc_url( 'https://www.instagram.com/zendcrew/' ),
                    'target' => '_blank',
                ),
            );
        }

    }

    new WTARS_Shipped_Admin_Page();
}






