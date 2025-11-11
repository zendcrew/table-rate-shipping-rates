<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'ReonOptionPage' ) ) {

    class ReonOptionPage {

        public $admin_bar_page;
        private static $option_page;
        private static $user_capabilities;

        public function __construct( $args = array() ) {

            if ( isset( $args[ 'admin_bar_page' ] ) ) {
                $this->admin_bar_page = $args[ 'admin_bar_page' ];
            }
        }

        public static function init() {
            add_action( 'after_setup_theme', array( new self(), 'init_options' ) );
            add_action( "admin_menu", array( new self(), 'init_menu' ), 100 );          
            ReonAjax::add_action( 'rn_save_option', array( new self(), 'save_option' ) ); //export_option
            ReonAjax::add_action( 'rn_reset_option', array( new self(), 'reset_option' ) );
            ReonAjax::add_action( 'rn_import_option', array( new self(), 'import_option' ) );
            ReonAjax::add_action( 'rn_export_option', array( new self(), 'export_option' ) );
        }

        public function init_options() {
            $options = array();
            foreach ( self::get_pages() as $page ) {
                if ( $page[ 'auto_load' ] == true ) {
                    $options[ 'option_name' ] = $page[ 'option_name' ];
                    $options[ 'database' ] = $page[ 'database' ];
                    $options[ 'instance_id' ] = '';
                    ReonCore::init_options( $options, true );
                }
            }
        }

        public function init_menu() {
            foreach ( self::get_pages( false ) as $pg ) {
                $option_page = $pg;
                if ( isset( $option_page[ 'menu' ][ 'parent' ] ) && $option_page[ 'menu' ][ 'parent' ] == '' ) {
                    $option_page = self::get_page( $pg[ 'option_name' ], true );
                } else if ( !isset( $option_page[ 'menu' ][ 'parent' ] ) ) {
                    $option_page = self::get_page( $pg[ 'option_name' ], true );
                }
                $args = array();
                if ( $option_page[ 'menu' ][ 'enable' ] != true ) {
                    continue;
                }
                $option_page_menu = $option_page[ 'menu' ];
                $args[ 'page_title' ] = $option_page_menu[ 'page_title' ];
                $args[ 'menu_title' ] = $option_page_menu[ 'title' ];
                $args[ 'capability' ] = $option_page[ 'user_capability' ];
                $args[ 'menu_slug' ] = $option_page[ 'slug' ];
                $args[ 'menu_icon' ] = $option_page_menu[ 'icon' ];
                $args[ 'menu_parent' ] = $option_page_menu[ 'parent' ];
                $args[ 'menu_priority' ] = $option_page_menu[ 'priority' ];


                $option_page_import_export = $option_page[ 'import_export' ];
                $args[ 'show_import_export' ] = $option_page_import_export[ 'enable' ];
                $args[ 'import_export_title' ] = $option_page_import_export[ 'title' ];


                $option_page_admin_bar = $option_page[ 'admin_bar' ];
                $args[ 'admin_bar' ][ 'enable' ] = $option_page_admin_bar[ 'enable' ];
                $args[ 'admin_bar_title' ] = $option_page_admin_bar[ 'title' ];
                $args[ 'admin_bar_icon' ] = $option_page_admin_bar[ 'icon' ];
                $args[ 'admin_bar_priority' ] = $option_page_admin_bar[ 'priority' ];

                $args[ 'page_links' ] = $option_page[ 'page_links' ];

                foreach ( $option_page[ 'sections' ] as $section ) {
                    $args[ 'sections' ][] = $section;
                }

                self::create_menu( $args );
            }
        }

        private static function create_menu( $args ) {
            $rn_page_title = $args[ 'page_title' ] ? $args[ 'page_title' ] : 'Reon Option';
            $rn_menu_title = $args[ 'menu_title' ] ? $args[ 'menu_title' ] : 'Reon Option';
            $rn_capability = $args[ 'capability' ] ? $args[ 'capability' ] : 'manage_options';
            $rn_menu_slug = $args[ 'menu_slug' ] ? $args[ 'menu_slug' ] : 'reon';
            if ( $args[ 'menu_parent' ] == '' && (count( $args[ 'sections' ] ) > 1 || $args[ 'show_import_export' ] == true) ) {
                $rn_icon_url = $args[ 'menu_icon' ] ? $args[ 'menu_icon' ] : 'dashicons dashicons-admin-generic';
                $rn_position = $args[ 'menu_priority' ] ? $args[ 'menu_priority' ] : null;

                add_menu_page( $rn_page_title, $rn_menu_title, $rn_capability, $rn_menu_slug, array( new self(), 'render' ), $rn_icon_url, $rn_position );
                $tab = 1;
                foreach ( $args[ 'sections' ] as $section ) {
                    if ( $section[ 'subsection' ] != true )
                        add_submenu_page( $rn_menu_slug, $rn_page_title, $section[ 'title' ], $rn_capability, 'admin.php?page=' . $rn_menu_slug . '&tab=' . $tab );
                    $tab++;
                }
                if ( $args[ 'show_import_export' ] == true ) {
                    add_submenu_page( $rn_menu_slug, $args[ 'import_export_title' ], $args[ 'import_export_title' ], $rn_capability, 'admin.php?page=' . $rn_menu_slug . '&tab=' . $tab );
                    $tab++;
                }
            } else if ( $args[ 'menu_parent' ] == '' && count( $args[ 'sections' ] ) == 1 && $args[ 'show_import_export' ] != true ) {
                $rn_icon_url = $args[ 'menu_icon' ] ? $args[ 'menu_icon' ] : 'dashicons dashicons-admin-generic';
                $rn_position = $args[ 'menu_priority' ] ? $args[ 'menu_priority' ] : null;
                add_menu_page( $rn_page_title, $rn_menu_title, $rn_capability, $rn_menu_slug, array( new self(), 'render' ), $rn_icon_url, $rn_position );
            } else {
                $rn_parent_slug = $args[ 'menu_parent' ];
                $rn_position = $args[ 'menu_priority' ] ? $args[ 'menu_priority' ] : null;
                add_submenu_page( $rn_parent_slug, $rn_page_title, $rn_menu_title, $rn_capability, $rn_menu_slug, array( new self(), 'render' ), $rn_position );
            }


            if ( $args[ 'admin_bar' ][ 'enable' ] == true && current_user_can( $args[ 'capability' ] ) == true ) {
                $args[ 'admin_bar_url_base' ] = admin_url() . $args[ 'menu_parent' ] . '?page=' . $rn_menu_slug . '&tab=';
                if ( $args[ 'menu_parent' ] == '' ) {
                    $args[ 'admin_bar_url_base' ] = admin_url() . 'admin.php?page=' . $rn_menu_slug . '&tab=';
                }
                $bar_args = array(
                    'admin_bar_page' => $args
                );

                add_action( 'admin_bar_menu', array( new self( $bar_args ), 'creat_admin_bar_menu' ), $args[ 'admin_bar_priority' ] );
            }
        }

        public function creat_admin_bar_menu( $wp_admin_bar ) {
            $tab = 1;
            $bar_id = "rn_bar" . $this->admin_bar_page[ 'menu_slug' ] != '' ? $this->admin_bar_page[ 'menu_slug' ] : 'reon';
            $icon = ($this->admin_bar_page[ 'admin_bar_icon' ] != '') ? $this->admin_bar_page[ 'admin_bar_icon' ] : 'dashicons-admin-generic';
            $main = array(
                'id' => $bar_id,
                'title' => '<span class="ab-icon ' . $icon . '"></span>' . ($this->admin_bar_page[ 'admin_bar_title' ] ? $this->admin_bar_page[ 'admin_bar_title' ] : 'Option Page'),
                'href' => $this->admin_bar_page[ 'admin_bar_url_base' ] . $tab
            );
            $wp_admin_bar->add_node( $main );

            foreach ( $this->admin_bar_page[ 'sections' ] as $section ) {
                if ( $section[ 'subsection' ] != true ) {
                    $args = array(
                        'id' => $bar_id . 'tab' . $tab,
                        'parent' => $bar_id,
                        'title' => $section[ 'title' ],
                        'href' => $this->admin_bar_page[ 'admin_bar_url_base' ] . $tab,
                    );
                    $wp_admin_bar->add_node( $args );
                }
                $tab++;
            }


            if ( $this->admin_bar_page[ 'show_import_export' ] == true ) {

                $args = array(
                    'id' => $bar_id . 'tab' . $tab,
                    'parent' => $bar_id,
                    'title' => $this->admin_bar_page[ 'import_export_title' ],
                    'href' => $this->admin_bar_page[ 'admin_bar_url_base' ] . $tab,
                );
                $wp_admin_bar->add_node( $args );
            }




            $external = $bar_id . '-external';
            $wp_admin_bar->add_group( array( 'id' => $external, 'parent' => $bar_id, 'meta' => array( 'class' => 'ab-sub-secondary' ) ) );


            foreach ( $this->admin_bar_page[ 'page_links' ] as $page_link ) {
                if ( $page_link[ 'show_in' ] == 'both' || $page_link[ 'show_in' ] == 'admin_bar' ) {
                    $args = array(
                        'id' => $bar_id . $page_link[ 'id' ],
                        'parent' => $external,
                        'title' => $page_link[ 'title' ],
                        'href' => $page_link[ 'href' ],
                        'meta' => array( 'target' => $page_link[ 'target' ] ? $page_link[ 'target' ] : '' )
                    );
                    $wp_admin_bar->add_node( $args );
                }
            }
        }

        public static function render() {
            
            $page_slug = '';

            if ( isset( $_GET[ 'page' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
               
                $page_slug = sanitize_key( wp_unslash( $_GET[ 'page' ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            }

            $page = self::get_page_by_slug( $page_slug, true, true );

            $tabs_key = $page[ 'tabs_key' ];

            if ( isset( $_GET[ $tabs_key ] ) && $page_slug != 'wc-settings' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
               
                $page[ 'tab' ] = sanitize_key( wp_unslash( $_GET[ $tabs_key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            }


            $active_section_data = self::get_active_section_data_by_tab( $page[ 'sections' ], $page[ 'tab' ] );

            if ( isset( $active_section_data[ 'group' ] ) ) {

                $page[ 'group' ] = $active_section_data[ 'group' ];
            }

            if ( isset( $active_section_data[ 'header_title' ] ) ) {

                $page[ 'header_title' ] = $active_section_data[ 'header_title' ];
            }

            self::render_page( $page );
        }

        public static function render_page( $page ) {
            
            $page[ 'nonce_path' ] = basename( __FILE__ );

            if ( get_option( 'rn_' . $page[ 'option_name' ] . '_export_id', '' ) == '' ) {
                update_option( 'rn_' . $page[ 'option_name' ] . '_export_id', ReonUtil::random_char( 20, '0123456789abcdefghijklmnopqrstwxyz' ), true );
            }
            
            if ( !isset( $page[ 'header_title' ] ) ) {

                $active_section_data = self::get_active_section_data_by_tab( $page[ 'sections' ], $page[ 'tab' ] );

                if ( isset( $active_section_data[ 'header_title' ] ) ) {

                    $page[ 'header_title' ] = $active_section_data[ 'header_title' ];
                }
            }

            $instance_id_param = '';
            
            if ( isset( $page[ 'instance_id' ] ) && $page[ 'instance_id' ] != '' ) {
                
                $instance_id_param = '&instance_id=' . $page[ 'instance_id' ];
            }

            $page[ 'export_url' ] = admin_url( 'admin-ajax.php?rn-admin-options=1&action=rn_export_option&optn=' . $page[ 'option_name' ] . $instance_id_param . '&secret=' . get_option( 'rn_' . $page[ 'option_name' ] . '_export_id', '' ) );

            do_action( 'reon/render-option-page', $page );
        }

        public static function save_option() {
            
            $output = array();
            $instance_id = '';
            $opt_name = '';
            $sec_id = '';

            if ( isset( $_POST[ 'reon_instance_id' ] ) ) {

                $instance_id = sanitize_key( wp_unslash( $_POST[ 'reon_instance_id' ] ) );
            }

            if ( isset( $_POST[ 'option_name' ] ) ) {

                $opt_name = sanitize_key( wp_unslash( $_POST[ 'option_name' ] ) );
            }

            if ( isset( $_POST[ 'section_id' ] ) ) {

                $sec_id = sanitize_key( wp_unslash( $_POST[ 'section_id' ] ) );
            }


            do_action( 'reon/before-save-' . $opt_name . '-options', $instance_id );
            
            $options = array();

            if ( isset( $_POST[ 'options' ][ $opt_name ] ) ) {

                $options = self::sanitize_options( $opt_name, $sec_id, $_POST[ 'options' ][ $opt_name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.NonceVerification.Missing
            }

            $option_args = self::get_page_option_args( $opt_name, $instance_id );

            $field_ids = self::get_page_field_ids( $opt_name, $sec_id );


            $nonce_id = 'option_page_' . $opt_name;
            $is_valid_nonce = ( isset( $_POST[ $nonce_id ] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce_id ] ) ), basename( __FILE__ ) ) ) ? true : false;
            $user_capability = self::get_user_capability( $opt_name );
                        
            if ( $is_valid_nonce && current_user_can( $user_capability ) ) {

                ReonCore::save_options( $option_args, $options, false, $field_ids );
                
                $output[ 'status' ] = 200;
                $output[ 'status_message' ] = wp_kses( $option_args[ 'ajax' ][ 'save_msg' ], ReonUtil::get_allow_html() );
            } else {
                
                $output[ 'status' ] = 400;
                $output[ 'status_message' ] = wp_kses( $option_args[ 'ajax' ][ 'save_error_msg' ], ReonUtil::get_allow_html() );
            }

            ReonAjax::render_json( $output );
        }

        public static function reset_option() {
            
            $output = array();
            $instance_id = '';
            $option_name = '';
            $section_id = '';

            if ( isset( $_POST[ 'reon_instance_id' ] ) ) {
                
                $instance_id = sanitize_key( wp_unslash( $_POST[ 'reon_instance_id' ]) );
            }
            
            if ( isset( $_POST[ 'option_name' ] ) ) {
                
                $option_name = sanitize_key( wp_unslash( $_POST[ 'option_name' ]) );
            }
            
            if ( isset( $_POST[ 'section_id' ] ) ) {
                
                $section_id = sanitize_key( wp_unslash( $_POST[ 'section_id' ]) );
            }

            
            $field_ids = self::get_page_field_ids( $option_name, $section_id );
                        
            $option_args = self::get_page_option_args( $option_name, $instance_id );

            $nonce_id = 'option_page_' . $option_name;

            $is_valid_nonce = ( isset( $_POST[ $nonce_id ] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce_id ] ) ), basename( __FILE__ ) ) ) ? true : false;
            $user_capability = self::get_user_capability( $option_name );
            
            if ( $is_valid_nonce && current_user_can( $user_capability ) ) {
               
                ReonCore::reset_options( $option_args, $field_ids );
                
                $output[ 'status' ] = 200;
                $output[ 'status_message' ] = wp_kses( $option_args[ 'ajax' ][ 'reset_msg' ], ReonUtil::get_allow_html() );
            } else {
                
                $output[ 'status' ] = 400;
                $output[ 'status_message' ] = wp_kses( $option_args[ 'ajax' ][ 'reset_error_msg' ], ReonUtil::get_allow_html() );
            }

            ReonAjax::render_json( $output );
        }

        public static function import_option() {

            $output = array( 'status' => 200 );
            $instance_id = '';
            $opt_name='';
            
            if ( isset( $_POST[ 'reon_instance_id' ] ) ) {

                $instance_id = sanitize_key( wp_unslash( $_POST[ 'reon_instance_id' ] ) );
            }
            
            if ( isset( $_POST[ 'option_name' ] ) ) {

                $opt_name = sanitize_key( wp_unslash( $_POST[ 'option_name' ] ) );
            }

            do_action( 'reon/before-import-' . $opt_name . '-options', $instance_id );  

            $nonce_id = 'option_page_' . $opt_name;
            
            $is_valid_nonce = ( isset( $_POST[ $nonce_id ] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $nonce_id ] ) ), basename( __FILE__ ) ) ) ? true : false;
            $user_capability = self::get_user_capability( $opt_name );

            if ( $is_valid_nonce && current_user_can( $user_capability ) ) {
                
                $options = self::sanitize_options( $opt_name, '', $_POST[ 'import_data' ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                
                $option_args = self::get_page_option_args( $opt_name, $instance_id );
                $opt = ReonCore::load_options( $option_args, false, array() );

                foreach ( $options as $key => $value ) {
                
                    $opt[ $key ] = $value;
                }

                ReonCore::save_options( $option_args, $opt, false );
                
                $output[ 'status' ] = 200;
                $output[ 'status_message' ] = wp_kses( $option_args[ 'ajax' ][ 'reset_msg' ], ReonUtil::get_allow_html() );
            } else {
                
                $output[ 'status' ] = 400;
                $output[ 'status_message' ] = wp_kses( $option_args[ 'ajax' ][ 'reset_error_msg' ], ReonUtil::get_allow_html() );
            }

            ReonAjax::render_json( $output );
        }

        public static function export_option() {
            
            $output = array();

            $instance_id = '';
            $option_name='';
            $rn_secret='';
            
            if ( isset( $_GET[ 'instance_id' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                
                $instance_id = sanitize_key( wp_unslash( $_GET[ 'instance_id' ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            }
            
            if ( isset( $_GET[ 'optn' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                
                $option_name = sanitize_key( wp_unslash( $_GET[ 'optn' ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            }
            
            if ( isset( $_GET[ 'secret' ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                
                $rn_secret = sanitize_key( wp_unslash( $_GET[ 'secret' ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            }

            
            $db_secret = get_option( 'rn_' . $option_name . '_export_id', '' );

            if ( $db_secret == $rn_secret ) {
                
                $option_args = self::get_page_option_args( $option_name, $instance_id );
                $output = ReonCore::load_options( $option_args, false );
            }

            ReonAjax::render_json( $output );
        }

        public static function get_page( $option_name = '', $process_sections = false, $process_fields = false ) {
            if ( $option_name == '' ) {
                return array();
            }
            foreach ( self::get_pages() as $pg ) {
                if ( $pg[ 'option_name' ] == $option_name ) {
                    $page = apply_filters( 'reon/process-option-page-' . $pg[ 'option_name' ], $pg );

                    if ( $process_sections == true ) {
                        $page[ 'sections' ] = self::get_page_sections( $page[ 'option_name' ], $page[ 'sections' ], $process_fields );
                    }
                    return $page;
                }
            }
            return array();
        }

        public static function get_page_by_slug( $slug = '', $process_sections = false, $process_fields = false ) {
            if ( $slug == '' ) {
                return array();
            }
            foreach ( self::get_pages() as $pg ) {
                if ( isset( $pg[ 'slug' ] ) && $pg[ 'slug' ] == $slug ) {
                    $page = apply_filters( 'reon/process-option-page-' . $pg[ 'option_name' ], $pg );
                    if ( $process_sections == true ) {
                        $page[ 'sections' ] = self::get_page_sections( $page[ 'option_name' ], $page[ 'sections' ], $process_fields );
                    }
                    return $page;
                }
            }
            return array();
        }

        public static function get_page_option_args( $option_name = '', $instance_id = '' ) {
          
            $page = self::get_page( $option_name );
            
            if ( count( $page ) > 0 ) {
            
                return array(
                    'option_name' => $page[ 'option_name' ],
                    'database' => $page[ 'database' ],
                    'ajax' => $page[ 'ajax' ],
                    'instance_id' => $instance_id,
                );
            } else {
                
                return array();
            }
        }

        public static function get_pages( $process_sections = false, $process_fields = false ) {
            
            $pages = array();

            if ( !is_array( self::$option_page ) ) {
            
                return $pages;
            }

            foreach ( self::$option_page as $page ) {
             
                $page = apply_filters( 'reon/process-option-page-' . $page[ 'option_name' ] . '-args', $page );
                
                if ( $process_sections == true ) {
                
                    $page[ 'sections' ] = self::get_page_sections( $page[ 'option_name' ], $page[ 'sections' ], $process_fields );
                }
                
                $pages[] = $page;
            }
            
            return $pages;
        }

        public static function set_page( $args = array() ) {
   
            $option = self::default_optionpage();
            
            foreach ( $args as $key => $value ) {
            
                $option[ $key ] = $value;
            }
            
            self::$option_page[] = $option;
        }

        public static function set_section( $option_name = '', $args = array() ) {
            $section = self::default_section();
            foreach ( $args as $key => $value ) {
                $section[ $key ] = $value;
            }
            for ( $i = 0; $i < count( self::$option_page ); $i++ ) {
                if ( self::$option_page[ $i ][ 'option_name' ] == $option_name ) {
                    self::$option_page[ $i ][ 'sections' ][] = $section;
                }
            }
        }

        public static function get_page_field_ids( $option_name = '', $section_id = '' ) {
            $page = self::get_page( $option_name, true, true );
            $field_ids = array();
            if ( count( $page ) > 0 ) {

                foreach ( $page[ 'sections' ] as $section ) {
                    if ( $section[ 'id' ] == $section_id || $section_id == '' ) {
                        foreach ( $section[ 'fields' ] as $field ) {
                            if ( isset( $field[ 'merge_fields' ] ) && $field[ 'merge_fields' ] == false ) {
                                foreach ( ReonUtil::get_controls_field_ids( $field[ 'fields' ] ) as $fld_id ) {
                                    $field_ids[] = $fld_id;
                                }
                            } else {
                                $field_ids[] = $field[ 'id' ];
                            }
                        }
                    }
                }
            }
            return apply_filters( 'reon/get-page-field-ids', $field_ids );
        }
        
        public static function get_user_capability( $option_name ) {
           
            $capabilities = self::get_user_capabilities();

            if ( !$capabilities ) {

                return 'manage_options';
            }

            if ( !isset( $capabilities[ $option_name ] ) ) {

                return 'manage_options';
            }

            return $capabilities[ $option_name ];
        }

        public static function get_user_capabilities(){
            
            if ( is_null( self::$user_capabilities ) ) {

                self::$user_capabilities = self::load_user_capabilities();
            }

            return self::$user_capabilities;
        }
        
        private static function sanitize_options( $option_name = '', $section_id = '', $options = array() ) {

            $sanitize_mode = self::get_sanitize_mode( $option_name );

            if ( 'recursive' == $sanitize_mode ) {

                return self::get_recursive_sanitised_options( 'reon/sanitize-' . $option_name, $options );
            }

            $sanitise_filter = 'reon/sanitize-' . $option_name . '-options';

            if ( has_filter( $sanitise_filter ) && 'filter' == $sanitize_mode ) {

                return apply_filters( $sanitise_filter, $options );
            }

            $field_types = self::get_field_types( $option_name, $section_id );
            $opt = array();
            foreach ( $field_types as $field_type ) {
                if ( isset( $options[ $field_type[ 'id' ] ] ) ) {
                    $opt[ $field_type[ 'id' ] ] = apply_filters( 'reon/sanitize-' . $field_type[ 'type' ], $options[ $field_type[ 'id' ] ], $options[ $field_type[ 'id' ] ], $field_type[ 'id' ], $field_type[ 'children' ] );
                }
            }

            return $opt;
        }

        private static function get_field_types( $option_name = '', $section_id = '' ) {
            $page = self::get_page( $option_name, true, true );

            $field_args = array(
                'screen' => 'option-page',
                'option_name' => $option_name
            );

            $field_types = array();
            if ( count( $page ) > 0 ) {
                foreach ( $page[ 'sections' ] as $section ) {
                    if ( $section[ 'id' ] == $section_id || $section_id == '' ) {
                        foreach ( $section[ 'fields' ] as $field ) {

                            if ( isset( $field[ 'merge_fields' ] ) && $field[ 'merge_fields' ] == false && isset( $field[ 'fields' ] ) ) {
                                foreach ( ReonUtil::get_controls_field_types( $field[ 'fields' ], $field_args ) as $field_type ) {
                                    $field_types[] = array(
                                        'id' => $field_type[ 'id' ],
                                        'type' => $field_type[ 'type' ],
                                        'screen' => $field_type[ 'screen' ],
                                        'option_name' => $field_type[ 'option_name' ],
                                        'children' => $field_type[ 'children' ]
                                    );
                                }
                            } else {
                                $field_types[] = array(
                                    'id' => $field[ 'id' ],
                                    'type' => $field[ 'type' ],
                                    'screen' => 'option-page',
                                    'option_name' => $option_name,
                                    'children' => apply_filters( 'reon/get-' . $field[ 'type' ] . '-children-types', array(), $field, $field_args )
                                );
                            }
                        }
                    }
                }
            }
            return $field_types;
        }

        private static function get_recursive_sanitised_options( $in_key, $options ) {

            $opt = array();

            foreach ( $options as $option_key => $option ) {

                $filter_key = $in_key . '-' . $option_key;

                if ( is_numeric( $option_key ) ) {

                    $filter_key = $in_key;
                }

                if ( has_filter( $filter_key ) ) {

                    $opt[ sanitize_key( $option_key ) ] = apply_filters( $filter_key, $option );

                    continue;
                }

                if ( is_array( $option ) ) {

                    $opt[ sanitize_key( $option_key ) ] = self::get_recursive_sanitised_options( $filter_key, $option );

                    continue;
                }

                if ( $option == '<=' || $option == '<' || $option == '>=' || $option == '>=' || $option == '==' || $option == '!=' ) {

                    $opt[ sanitize_key( $option_key ) ] = $option;

                    continue;
                }

                $opt[ sanitize_key( $option_key ) ] = sanitize_text_field( $option );
            }

            return $opt;
        }

        private static function get_sanitize_mode( $option_name ) {

            $page = self::get_page( $option_name, false, false );

            if ( isset( $page[ 'sanitize_mode' ] ) ) {

                return $page[ 'sanitize_mode' ];
            }

            return 'default';
        }

        private static function get_page_sections( $option_name, $in_sections = array(), $process_fields = false ) {

            $sections = apply_filters( 'reon/get-option-page-' . $option_name . '-sections', $in_sections );

            $d_section = self::default_section();
            foreach ( $sections as $key => $section ) {
                foreach ( $d_section as $d_key => $d_sec ) {
                    if ( !isset( $section[ $d_key ] ) ) {
                        $section[ $d_key ] = $d_sec;
                    }
                }
                $sect = apply_filters( 'reon/process-' . $option_name . '-section', $section );
                if ( $process_fields == true ) {
                    $sect[ 'fields' ] = self::process_section_fields( $option_name, $sect[ 'id' ], $sect[ 'fields' ] );
                }
                $sections[ $key ] = $sect;
            }

            return $sections;
        }

        private static function process_section_fields( $option_name, $section_id, $in_fields ) {
            $fields = apply_filters( 'get-option-page-' . $option_name . 'section-' . $section_id . '-fields', $in_fields, $section_id );
            return ReonCore::process_default_option_page_fields( $fields );
        }
        
        public static function load_user_capabilities(){
            
            $capabilities = array();

            if ( !is_array( self::$option_page ) ) {
            
                return $capabilities;
            }

            foreach ( self::$option_page as $in_page ) {
             
                $page = apply_filters( 'reon/process-option-page-' . $in_page[ 'option_name' ] . '-args', $in_page );

                if ( !isset( $page[ 'option_name' ] ) ) {

                    continue;
                }

                $option_name = $page[ 'option_name' ];
                $capability = 'manage_options';

                if ( empty( $option_name ) ) {

                    continue;
                }

                if ( !isset( $page[ 'user_capability' ] ) || empty( $page[ 'user_capability' ] ) ) {

                    $capabilities[ $option_name ] = $capability;

                    continue;
                }

                $capabilities[ $option_name ] = $page[ 'user_capability' ];
            }
            
            return $capabilities;
        }

        private static function default_optionpage() {
            $defuault_args = array(
                'option_name' => 'reon',
                'database' => 'theme_mod',
                'sanitize_mode' => 'default', // default, recursive, filter
                'slug' => 'reon',
                'url_base' => 'themes.php',
                'use_slug_url' => true,
                'auto_load' => true,
                'tabs_key' => 'tab',
                'tab' => 1,
                'user_capability' => 'manage_options',
                'display' => array(
                    'enabled' => true,
                    'image' => '',
                    'name' => 'Reon Framework',
                    'version' => '',
                    'sub_version' => '',
                    'styles' => array(
                        'bg_image' => '',
                        'bg_color' => '',
                        'color' => '',
                    ),
                ),
                'ajax' => array(
                    'save_msg' => 'Done!!',
                    'save_error_msg' => 'Unable to save your settings',
                    'reset_msg' => 'Done!!',
                    'reset_error_msg' => 'Unable to reset your settings',
                    'reset_prompt_msg' =>"All settings will be removed, Press 'OK' to continue",
                    'reset_prompt_section_msg' =>"Settings in this section will be removed, Press 'OK' to continue",
                    'nonce_error_msg' => 'invalid nonce'
                ),
                'menu' => array(
                    'enable' => true,
                    'title' => 'Reon Framework',
                    'icon' => 'dashicons-admin-generic',
                    'priority' => null,
                    'parent' => 'themes.php',
                    'page_title' => '',
                ),
                'admin_bar' => array(
                    'enable' => false,
                    'title' => 'Reon Framework',
                    'icon' => '',
                    'priority' => 0,
                ),
                'import_export' => array(
                    'enable' => true,
                    'min_height' => '565px',
                    'title' => 'Import / Export',
                    'import' => array(
                        'title' => 'Import Settings',
                        'desc' => 'Here you can import new settings. Simply paste the settings url or data on the field below.',
                        'url_button_text' => 'Import from url',
                        'url_textbox_desc' => "Paste the url to another site's settings below and click the 'Import Now' button.",
                        'url_textbox_hint' => "Paste the url to another site's settings here...",
                        'data_button_text' => 'Import Data',
                        'data_textbox_desc' => "Paste your backup settings below and click the 'Import Now' button.",
                        'data_textbox_hint' => 'Paste your backup settings here...',
                        'import_button_text' => 'Import Now',
                        'warn_text' => 'Warning! This will override all existing settings. proceed with caution!',
                    ),
                    'export' => array(
                        'title' => 'Export Settings',
                        'desc' => 'Here you can backup your current settings. You can later use it to restore your settings.',
                        'download_button_text' => 'Download Data',
                        'url_button_text' => 'Export url',
                        'url_textbox_desc' => 'Copy the url below, use it to transfer the settings from this site.',
                        'data_button_text' => 'Export Data',
                        'data_textbox_desc' => 'Copy the data below, use it as your backup.',
                    ),
                ),
                'header_buttons' => array(),
                'footer_buttons' => array(),
                'page_links' => array(),
                'social_links' => array(),
                'sections' => array(),
            );
            return $defuault_args;
        }

        private static function default_section() {
            $args = array(
                'id' => '',
                'title' => '',
                'group' => 0,
                'subsection' => false,
                'fields' => array()
            );
            return $args;
        }

        private static function get_active_section_data_by_tab( $sections, $tab ) {

            $cnt = 1;

            foreach ( $sections as $section ) {

                if ( $cnt == $tab ) {

                    $active_group_data = array(
                        'group' => $section[ 'group' ],
                    );

                    if ( isset( $section[ 'header_title' ] ) && !empty( $section[ 'header_title' ] ) ) {

                        $active_group_data[ 'header_title' ] = $section[ 'header_title' ];
                    }

                    return $active_group_data;
                }

                $cnt++;
            }
        }

    }

}

