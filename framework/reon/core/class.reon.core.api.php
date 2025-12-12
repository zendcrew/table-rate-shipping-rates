<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'ReonApi' ) ) {

    class ReonApi {

        private static $data_list = array();
        private static $user_can;

        public static function map_data_list( $list_object, $data_args ) {

            $list = array();

            if ( is_array( $list_object ) ) {

                $list = $list_object;
            } else if ( $list_object != '' ) {

                $list[] = $list_object;
            }

            $data_args[ 'items' ] = $list;

            $result = array();

            if ( !count( $list ) ) {

                return $result;
            }

            $db_list = self::get_data_list( $data_args );

            foreach ( $list as $key ) {

                if ( isset( $db_list[ $key ] ) ) {

                    $result[ $key ] = $db_list[ $key ];
                }
            }

            return $result;
        }

        public static function get_data_list( $data_args ) {

            if ( !self::user_can_list() ) {

                return false;
            }

            $data_key = md5( wp_json_encode( $data_args ) );

            if ( isset( self::$data_list[ $data_key ] ) ) {

                return self::$data_list[ $data_key ];
            }

            $result = array();

            if ( isset( $data_args[ 'source' ] ) ) {

                $args = array();

                if ( isset( $data_args[ 'search_term' ] ) ) {

                    $args[ 'search_term' ] = $data_args[ 'search_term' ];
                }

                if ( isset( $data_args[ 'items' ] ) ) {

                    $args[ 'items' ] = $data_args[ 'items' ];
                }

                if ( isset( $data_args[ 'value_col' ] ) ) {

                    $args[ 'value_col' ] = $data_args[ 'value_col' ];
                }

                if ( isset( $data_args[ 'value_col_pre' ] ) ) {

                    $args[ 'value_col_pre' ] = $data_args[ 'value_col_pre' ];
                }

                if ( isset( $data_args[ 'show_value' ] ) ) {

                    $args[ 'show_value' ] = $data_args[ 'show_value' ];
                }

                if ( isset( $data_args[ 'pagesize' ] ) ) {

                    $args[ 'pagesize' ] = $data_args[ 'pagesize' ];
                }


                if ( $data_args[ 'source' ] == 'posttypes' ) {

                    $result = self::get_post_types( $args );
                } else if ( $data_args[ 'source' ] == 'taxonomies' ) {

                    $result = self::get_taxonomies( $args );
                } else if ( $data_args[ 'source' ] == 'pages' ) {

                    $args[ 'post_type' ] = 'page';
                    $result = self::get_posts( $args );
                } else if ( $data_args[ 'source' ] == 'posts' ) {

                    $args[ 'post_type' ] = 'post';
                    $result = self::get_posts( $args );
                } else if ( $data_args[ 'source' ] == 'categories' ) {

                    $args[ 'taxonomy' ] = 'category';
                    $result = self::get_categories( $args );
                } else if ( $data_args[ 'source' ] == 'tags' ) {

                    $args[ 'taxonomy' ] = 'post_tag';
                    $result = self::get_tags( $args );
                } else if ( $data_args[ 'source' ] == 'roles' || $data_args[ 'source' ] == 'wp_roles' ) {

                    $result = self::get_roles( $args );
                } else if ( $data_args[ 'source' ] == 'wp_caps' ) {

                    $result = self::get_caps( $args );
                } else if ( $data_args[ 'source' ] == 'users' ) {

                    $result = self::get_users( $args );
                } else if ( $data_args[ 'source' ] == 'wc:product' || $data_args[ 'source' ] == 'wc:products' || $data_args[ 'source' ] == 'products' ) {

                    $result = self::get_wc_products( $args );
                } else if ( $data_args[ 'source' ] == 'wc:product_type' || $data_args[ 'source' ] == 'wc:product_types' ) {

                    $result = self::get_wc_product_types( $args );
                } else if ( $data_args[ 'source' ] == 'wc:product_variation' || $data_args[ 'source' ] == 'wc:product_variations' || $data_args[ 'source' ] == 'product_variations' ) {

                    $result = self::get_wc_product_variations( $args );
                } else if ( $data_args[ 'source' ] == 'wc:attributes' || $data_args[ 'source' ] == 'wc:attribute' ) {

                    $result = self::get_wc_attributes( $args );
                } else if ( $data_args[ 'source' ] == 'wc:countries' || $data_args[ 'source' ] == 'wc:country' ) {

                    $result = self::get_wc_countries( $args );
                } else if ( $data_args[ 'source' ] == 'wc:continents' || $data_args[ 'source' ] == 'wc:continents' ) {

                    $result = self::get_wc_continents( $args );
                } else if ( $data_args[ 'source' ] == 'wc:states' || $data_args[ 'source' ] == 'wc:state' ) {

                    $result = self::get_wc_states( $args );
                } else if ( $data_args[ 'source' ] == 'wc:payment_methods' ) {

                    $result = self::get_wc_payment_methods( $args );
                } else if ( $data_args[ 'source' ] == 'wc:shipping_methods' ) {

                    $result = self::get_wc_shipping_methods( $args );
                } else if ( $data_args[ 'source' ] == 'wc:shipping_classes' ) {

                    $result = self::get_wc_shipping_classes( $args );
                } else if ( $data_args[ 'source' ] == 'wc:shipping_zones' ) {

                    $result = self::get_wc_shipping_zones( $args );
                } else if ( $data_args[ 'source' ] == 'wc:zones_shipping' ) { //TODO: this should be deprecated
                    $result = self::get_wc_zones_shipping_methods( $args );
                } else if ( $data_args[ 'source' ] == 'wc:shipping-rates' ) {

                    $result = self::get_wc_zones_shipping_methods( $args );
                } else if ( $data_args[ 'source' ] == 'wc:tax_classes' ) {

                    $result = self::get_wc_tax_classes( $args );
                } else {

                    $op = explode( ':', $data_args[ 'source' ] );

                    if ( is_array( $op ) && count( $op ) > 1 ) {

                        if ( $op[ 0 ] == 'posts' || $op[ 0 ] == 'pages' ) {

                            $args[ 'post_type' ] = $op[ 1 ];
                            $result = self::get_posts( $args );
                        }

                        if ( $op[ 0 ] == 'terms' ) {

                            $args[ 'taxonomy' ] = $op[ 1 ];
                            $result = self::get_terms( $args );
                        }

                        if ( $op[ 0 ] == 'categories' ) {

                            $args[ 'taxonomy' ] = $op[ 1 ];
                            $result = self::get_categories( $args );
                        }

                        if ( $op[ 0 ] == 'tags' ) {

                            $args[ 'taxonomy' ] = $op[ 1 ];
                            $result = self::get_tags( $args );
                        }

                        if ( $op[ 0 ] == 'users' ) {

                            $args[ 'role' ] = $op[ 1 ];

                            $result = self::get_users( $args );
                        }
                    }
                }
            }

            if ( count( $result ) ) {

                self::$data_list[ $data_key ] = $result;
            }

            return apply_filters( 'reon/get-data-list', $result, $data_args );
        }

        public static function get_posts( $args ) {

            $result = array();

            $pagesize = 25;

            $selected_items = array();

            if ( isset( $args[ 'items' ] ) && is_array( $args[ 'items' ] ) ) {

                $selected_items = $args[ 'items' ];
            }

            if ( isset( $args[ 'pagesize' ] ) ) {

                $pagesize = $args[ 'pagesize' ];
            }

            if ( count( $selected_items ) ) {

                $pagesize = count( $selected_items );
            }

            $value_col = 'id';

            if ( isset( $args[ 'value_col' ] ) ) {

                $value_col = $args[ 'value_col' ];
            }

            $search_term = '';

            if ( isset( $args[ 'search_term' ] ) ) {

                $search_term = $args[ 'search_term' ];
            }

            $post_args = array(
                'post_type' => $args[ 'post_type' ],
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
                'posts_per_page' => $pagesize,
            );

            if ( 'slug' == $value_col && count( $selected_items ) ) {

                $post_args[ 'post_name__in' ] = $selected_items;
            } else if ( count( $selected_items ) ) {

                $post_args[ 'post__in' ] = $selected_items;
            } else if ( !empty( $search_term ) ) {

                $post_args[ 's' ] = $search_term;
            }

            foreach ( get_posts( $post_args ) as $post_data ) {

                $post_label = $post_data->post_title;
                $post_id = $post_data->ID;
                $post_slug = $post_data->post_name;

                $col_pref = '';

                if ( isset( $args[ 'value_col_pre' ] ) ) {

                    $col_pref = $args[ 'value_col_pre' ];
                }

                if ( $value_col == 'slug' ) {

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $col_pref . $post_slug . ')' : '';
                    $result[ $post_slug ] = $post_label . $vl;
                } else {

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $col_pref . $post_id . ')' : '';
                    $result[ $post_id ] = $post_label . $vl;
                }
            }

            return $result;
        }

        public static function get_terms( $args ) {

            $result = array();

            try {

                $rn_terms = get_terms( array(
                    'taxonomy' => $args[ 'taxonomy' ],
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'number' => isset( $args[ 'pagesize' ] ) ? $args[ 'pagesize' ] : false,
                    'search' => isset( $args[ 'search_term' ] ) ? $args[ 'search_term' ] : '',
                        ) );

                foreach ( $rn_terms as $rn_term ) {

                    $formatted_name = '';

                    if ( $rn_term->parent ) {

                        $ancestors = array_reverse( get_ancestors( $rn_term->term_id, $args[ 'taxonomy' ] ) );

                        foreach ( $ancestors as $ancestor ) {

                            $ancestor_term = get_term( $ancestor, $args[ 'taxonomy' ] );

                            if ( $ancestor_term ) {

                                $formatted_name .= $ancestor_term->name . ' → ';
                            }
                        }
                    }

                    $formatted_name .= $rn_term->name;

                    if ( isset( $args[ 'value_col' ] ) && $args[ 'value_col' ] == 'slug' ) {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rn_term->slug . ')' : '';
                        $result[ $rn_term->slug ] = $formatted_name . $vl;
                    } else {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rn_term->term_id . ')' : '';
                        $result[ $rn_term->term_id ] = $formatted_name . $vl;
                    }
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_categories( $args ) {

            return self::get_terms( $args );
        }

        public static function get_tags( $args ) {

            return self::get_terms( $args );
        }

        public static function get_post_types( $args ) {

            $result = array();

            try {

                foreach ( get_post_types( array( 'public' => true, ), 'objects' ) as $key => $rn_post_type ) {

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rn_post_type->name . ')' : '';
                    $result[ $key ] = $rn_post_type->label . $vl;
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_taxonomies( $args ) {

            $result = array();

            foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $key => $taxonomy_data ) {

                $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $taxonomy_data->name . ')' : '';
                $result[ $key ] = $taxonomy_data->label . $vl;
            }


            return $result;
        }

        public static function get_users( $args ) {

            $result = array();

            if ( !current_user_can( 'list_users' ) ) {

                return false;
            }

            $user_q = array(
                'orderby' => 'user_login',
                'order' => 'ASC',
                'fields' => array( 'id', 'user_login', 'user_email', 'display_name' ),
            );

            if ( isset( $args[ 'role' ] ) ) {

                $user_q[ 'role' ] = $args[ 'role' ];
            }

            if ( isset( $args[ 'pagesize' ] ) ) {

                $user_q[ 'number' ] = $args[ 'pagesize' ];
            }

            if ( isset( $args[ 'search_term' ] ) ) {

                $user_q[ 'search' ] = '*' . $args[ 'search_term' ] . '*';
                $user_q[ 'search_columns' ] = array( 'user_login', 'user_email', 'display_name' );
            }

            if ( isset( $args[ 'value_col' ] ) ) {

                if ( $args[ 'value_col' ] == 'displayname_id' || $args[ 'value_col' ] == 'displayname_login' || $args[ 'value_col' ] == 'displayname_email' ) {

                    $user_q[ 'orderby' ] = 'display_name';
                }

                if ( $args[ 'value_col' ] == 'email' ) {

                    $user_q[ 'orderby' ] = 'user_email';
                }
            }


            $users = (new WP_User_Query( $user_q ) )->get_results();

            foreach ( $users as $user ) {

                $display_text = $user->user_login;
                $opt_value = $user->id;

                if ( isset( $args[ 'value_col' ] ) ) {

                    if ( $args[ 'value_col' ] == 'displayname_id' || $args[ 'value_col' ] == 'displayname_login' || $args[ 'value_col' ] == 'displayname_email' ) {

                        $display_text = $user->display_name;
                        $opt_value = $user->id;
                    }

                    if ( $args[ 'value_col' ] == 'login' || $args[ 'value_col' ] == 'displayname_login' ) {

                        $opt_value = $user->user_login;
                    }

                    if ( $args[ 'value_col' ] == 'email' || $args[ 'value_col' ] == 'displayname_email' ) {

                        $opt_value = $user->user_email;
                    }
                }

                if ( isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true ) {

                    $result[ $opt_value ] = $display_text . ' (' . $opt_value . ')';
                } else {

                    $result[ $opt_value ] = $display_text;
                }
            }

            return $result;
        }

        public static function get_roles( $args ) {

            $result = array();

            try {

                foreach ( wp_roles()->roles as $key => $role ) {

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $key . ')' : '';
                    $result[ $key ] = $role[ 'name' ] . $vl;
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_caps( $args ) {

            $result = array();

            try {

                foreach ( wp_roles()->roles as $dkey => $role ) {

                    foreach ( $role[ 'capabilities' ] as $key => $is_enabled ) {

                        if ( $is_enabled == true ) {

                            $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $key . ')' : '';

                            $result[ $key ] = $key . $vl;
                        }
                    }
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_countries( $args ) {

            $result = array();

            try {

                $wc_Countries = new WC_Countries();

                foreach ( $wc_Countries->get_countries() as $key => $country ) {

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $key . ')' : '';

                    $result[ $key ] = $country . $vl;
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_continents( $args ) {

            $result = array();

            try {
                $wc_Countries = new WC_Countries();

                foreach ( $wc_Countries->get_continents() as $key => $continent ) {

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $key . ')' : '';

                    $result[ $key ] = $continent[ 'name' ] . $vl;
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_states( $args ) {

            $result = array();

            try {

                $wc_Countries = new WC_Countries();
                $countries = $wc_Countries->get_countries();

                foreach ( $wc_Countries->get_states() as $p_key => $p_states ) {

                    foreach ( $p_states as $key => $states ) {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $key . ')' : '';
                        $cntry = '';

                        if ( isset( $countries[ $p_key ] ) ) {

                            $cntry = ', ' . $countries[ $p_key ];
                        }

                        $result[ $p_key . ':' . $key ] = $states . $cntry . $vl;
                    }
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_products( $args ) {

            $in_args = $args;
            $in_args[ 'post_type' ] = 'product';

            return self::get_posts( $in_args );
        }

        public static function get_wc_product_types( $args ) {

            try {

                return wc_get_product_types();
            }
            catch ( Exception $ex ) {
                
            }

            return array();
        }

        public static function get_wc_attributes( $args ) {

            $result = array();

            $taxonomies = array();

            foreach ( wc_get_attribute_taxonomy_labels() as $key => $label ) {

                $taxonomy = wc_attribute_taxonomy_name( $key );

                $taxonomies[ $taxonomy ] = $label;
            }

            foreach ( $taxonomies as $attribute_taxonomy => $attribute_label ) {

                $rn_terms = get_terms( array(
                    'taxonomy' => $attribute_taxonomy,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'number' => isset( $args[ 'pagesize' ] ) ? $args[ 'pagesize' ] : false,
                    'search' => isset( $args[ 'search_term' ] ) ? $args[ 'search_term' ] : '',
                        ) );

                foreach ( $rn_terms as $rn_term ) {

                    $attribute_id = $rn_term->term_id;

                    if ( isset( $args[ 'value_col' ] ) && $args[ 'value_col' ] == 'slug' ) {

                        $attribute_id = $rn_term->slug . ':' . $attribute_taxonomy;
                    }

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $attribute_id . ')' : '';
                    $result[ $attribute_id ] = $attribute_label . ': ' . $rn_term->name . $vl;
                }
            }

            return $result;
        }

        public static function get_wc_product_variations( $args ) {

            $in_args = $args;
            $in_args[ 'post_type' ] = 'product_variation';

            return self::get_posts( $in_args );
        }

        public static function get_wc_payment_methods( $args ) {

            $result = array();

            try {

                foreach ( WC()->payment_gateways->payment_gateways() as $key => $method ) {

                    if ( $method->enabled == 'yes' ) {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $key . ')' : '';

                        $result[ $key ] = $method->title . $vl;
                    }
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_shipping_methods( $args ) {

            $result = array();

            try {

                foreach ( WC()->shipping()->get_shipping_methods() as $key => $method ) {

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $key . ')' : '';

                    $result[ $key ] = $method->get_method_title() . $vl;
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_shipping_classes( $args ) {

            $result = array();

            try {

                foreach ( WC()->shipping()->get_shipping_classes() as $method ) {

                    if ( isset( $args[ 'value_col' ] ) && $args[ 'value_col' ] == 'slug' ) {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $method->slug . ')' : '';

                        $result[ $method->slug ] = $method->name . $vl;
                    } else {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $method->term_id . ')' : '';

                        $result[ $method->term_id ] = $method->name . $vl;
                    }
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_shipping_zones( $args ) {

            foreach ( WC_Shipping_Zones::get_zones( 'admin' ) as $zone ) {

                $result[ $zone[ 'zone_id' ] ] = $zone[ 'zone_name' ];
            }

            return $result;
        }

        public static function get_wc_zones_shipping_methods( $args ) {

            $result = array();

            foreach ( WC_Shipping_Zones::get_zones( 'admin' ) as $zone_key => $shipping_zone ) {

                foreach ( $shipping_zone[ 'shipping_methods' ] as $key => $shipping_method ) {

                    $result = self::process_wc_shipping_rates( $result, $shipping_method, $shipping_zone[ 'zone_name' ], $args );
                }
            }


            $zero_zone = WC_Shipping_Zones::get_zone( 0 );

            if ( count( $zero_zone->get_shipping_methods() ) ) {

                foreach ( $zero_zone->get_shipping_methods() as $key => $shipping_method ) {

                    $result = self::process_wc_shipping_rates( $result, $shipping_method, '', $args );
                }
            }

            return $result;
        }

        public static function get_wc_tax_classes( $args ) {

            $result = array();

            try {

                $tax_object = new WC_Tax();
                $tax_classes = $tax_object->get_tax_classes();
                $tax_classes_slug = $tax_object->get_tax_class_slugs();

                for ( $i = 0; $i < count( $tax_classes ); $i++ ) {
                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $tax_classes_slug[ $i ] . ')' : '';
                    $result[ $tax_classes_slug[ $i ] ] = $tax_classes[ $i ] . $vl;
                }
            }
            catch ( Exception $ex ) {
                
            }

            return $result;
        }

        private static function process_wc_shipping_rates( $result, $shipping_method, $zone_title, $args ) {

            $label_prefix = '';

            if ( !empty( $zone_title ) ) {

                $label_prefix = $zone_title . ' → ';
            }

            $data = apply_filters( 'reon/get-data-list-shipping-rates',
                    array(
                        'id' => $shipping_method->id,
                        'instance_id' => $shipping_method->get_instance_id(),
                        'title' => $shipping_method->get_title(),
                        'rates' => array()
                    )
            );

            if ( !isset( $data[ 'rates' ] ) || !count( $data[ 'rates' ] ) ) {

                $rate_args = array(
                    'id' => $data[ 'id' ],
                    'instance_id' => $data[ 'instance_id' ],
                    'suffix' => '',
                );

                $rate_id = apply_filters( 'reon/get-data-list-shipping-rate-id', $shipping_method->get_rate_id(), $rate_args );

                $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rate_id . ')' : '';

                $result[ $rate_id ] = $label_prefix . $data[ 'title' ] . $vl;

                return $result;
            }

            foreach ( $data[ 'rates' ] as $suffix => $rate_label ) {

                $rate_args = array(
                    'id' => $data[ 'id' ],
                    'instance_id' => $data[ 'instance_id' ],
                    'suffix' => $suffix,
                );

                $rate_id = apply_filters( 'reon/get-data-list-shipping-rate-id', $shipping_method->get_rate_id( $suffix ), $rate_args );

                $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rate_id . ')' : '';

                $result[ $rate_id ] = $label_prefix . $rate_label . $vl;
            }

            return $result;
        }

        private static function user_can_list() {

            if ( is_null( self::$user_can ) ) {

                self::$user_can = self::get_user_can_list();
            }

            return self::$user_can;
        }

        private static function get_user_can_list() {

            $capabilities = ReonOptionPage::get_user_capabilities();

            if ( !$capabilities ) {

                return current_user_can( 'manage_options' );
            }

            foreach ( $capabilities as $capability ) {

                if ( current_user_can( $capability ) ) {

                    return true;
                }
            }

            return false;
        }

    }

}