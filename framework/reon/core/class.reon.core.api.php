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
                } else if ( $data_args[ 'source' ] == 'wc:zones_shipping' ) {
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

            try {

                global $wpdb;

                $search_term = isset( $args[ 'search_term' ] ) ? $args[ 'search_term' ] : '';

                $sql_select = "SELECT posts.id, posts.post_title, posts.post_name FROM {$wpdb->posts} AS posts"
                        . " WHERE (posts.post_type='" . esc_sql( $args[ 'post_type' ] ) . "')"
                        . " AND (posts.post_status='publish')";

                if ( count( $selected_items ) && $value_col == 'slug' ) {

                    $sql_filter = " AND (posts.post_name IN('" . implode( "','", array_map( 'esc_sql', $selected_items ) ) . "'))";
                } else if ( count( $selected_items ) ) {

                    $sql_filter = " AND (posts.id IN(" . implode( ",", array_map( 'esc_sql', $selected_items ) ) . "))";
                } else if ( !empty( $search_term ) && $value_col == 'slug' ) {

                    $sql_filter = " AND (posts.post_title LIKE '%" . esc_sql( $search_term ) . "%' OR posts.post_name LIKE '%" . esc_sql( $search_term ) . "%')";
                } else if ( !empty( $search_term ) ) {

                    $sql_filter = " AND (posts.post_title LIKE '%" . esc_sql( $search_term ) . "%')";
                } else {

                    $sql_filter = "";
                }

                $sql_order_limit = "  ORDER BY posts.post_title ASC LIMIT " . esc_sql( $pagesize );

                $sql = $sql_select . $sql_filter . $sql_order_limit;

                $rows = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching


                foreach ( $rows as $row ) {
                    $col_pref = '';

                    if ( isset( $args[ 'value_col_pre' ] ) ) {
                        $col_pref = $args[ 'value_col_pre' ];
                    }

                    if ( $value_col == 'slug' ) {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $col_pref . $row[ 'post_name' ] . ')' : '';
                        $result[ $row[ 'post_name' ] ] = $row[ 'post_title' ] . $vl;
                    } else {

                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $col_pref . $row[ 'id' ] . ')' : '';
                        $result[ $row[ 'id' ] ] = $row[ 'post_title' ] . $vl;
                    }
                }
            } catch ( Exception $ex ) {
                
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
                    'number' => isset( $data_args[ 'pagesize' ] ) ? data_args[ 'pagesize' ] : false,
                    'search' => isset( $data_args[ 'search_term' ] ) ? data_args[ 'search_term' ] : '',
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
            } catch ( Exception $ex ) {
                
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
                $rn_post_types = get_post_types( array(
                    'public' => true,
                        ), 'objects' );

                foreach ( $rn_post_types as $key => $rn_post_type ) {
                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rn_post_type->name . ')' : '';
                    $result[ $key ] = $rn_post_type->label . $vl;
                }
            } catch ( Exception $ex ) {
                
            }
            return $result;
        }

        public static function get_taxonomies( $args ) {

            $result = array();

            try {
                $rn_taxonomies = get_taxonomies( array(
                    'public' => true,
                        ), 'objects' );

                foreach ( $rn_taxonomies as $key => $rn_taxonomy ) {
                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rn_taxonomy->name . ')' : '';
                    $result[ $key ] = $rn_taxonomy->label . $vl;
                }
            } catch ( Exception $ex ) {
                
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
            } catch ( Exception $ex ) {
                
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
            } catch ( Exception $ex ) {
                
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
            } catch ( Exception $ex ) {
                
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
            } catch ( Exception $ex ) {
                
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
            } catch ( Exception $ex ) {
                
            }
            return $result;
        }

        public static function get_wc_products( $args ) {

            $in_args = $args;
            $in_args[ 'post_type' ] = 'product';
            return self::get_posts( $in_args );
        }

        public static function get_wc_product_types( $args ) {

            $result = array();

            try {
                $result = wc_get_product_types();
            } catch ( Exception $ex ) {
                
            }
            return $result;
        }

        public static function get_wc_attributes( $args ) {

            global $wpdb;

            $result = array();

            try {

                $attr_taxs = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name != '' ORDER BY attribute_name ASC;" );// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

                if ( is_array( $attr_taxs ) ) {
                    foreach ( $attr_taxs as $attr_tax ) {

                        $rn_terms = get_terms( array(
                            'taxonomy' => 'pa_' . $attr_tax->attribute_name,
                            'orderby' => 'name',
                            'order' => 'ASC',
                            'hide_empty' => false,
                            'number' => isset( $args[ 'pagesize' ] ) ? $args[ 'pagesize' ] : false,
                            'search' => isset( $args[ 'search_term' ] ) ? $args[ 'search_term' ] : '',
                                ) );
                        foreach ( $rn_terms as $rn_term ) {

                            if ( isset( $args[ 'value_col' ] ) && $args[ 'value_col' ] == 'slug' ) {

                                $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rn_term->slug . ')' : '';
                                $result[ $rn_term->slug . ':pa_' . $attr_tax->attribute_name ] = $attr_tax->attribute_label . ': ' . $rn_term->name . $vl;
                            } else {
                                $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $rn_term->term_id . ')' : '';
                                $result[ $rn_term->term_id ] = $attr_tax->attribute_label . ': ' . $rn_term->name . $vl;
                            }
                        }
                    }
                }
            } catch ( Exception $ex ) {
                
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
            } catch ( Exception $ex ) {
                
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
            } catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_shipping_classes( $args ) {

            $result = array();

            try {
                foreach ( WC()->shipping()->get_shipping_classes() as $key => $method ) {
                    if ( isset( $args[ 'value_col' ] ) && $args[ 'value_col' ] == 'slug' ) {
                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $method->slug . ')' : '';
                        $result[ $method->slug ] = $method->name . $vl;
                    } else {
                        $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $method->term_id . ')' : '';
                        $result[ $method->term_id ] = $method->name . $vl;
                    }
                }
            } catch ( Exception $ex ) {
                
            }

            return $result;
        }

        public static function get_wc_shipping_zones( $args ) {

            WC_Shipping_Zones::get_zones();

            foreach ( WC_Shipping_Zones::get_zones() as $zone ) {

                $result[ $zone[ 'zone_id' ] ] = $zone[ 'zone_name' ];
            }

            return $result;
        }

        public static function get_wc_zones_shipping_methods( $args ) {

            $result = array();

            $shipping_zones = WC_Shipping_Zones::get_zones();

            foreach ( $shipping_zones as $key => $shipping_zone ) {

                foreach ( $shipping_zone[ 'shipping_methods' ] as $key => $shipping_method ) {

                    $instance_id = $shipping_method->get_instance_id();

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $instance_id . ')' : '';

                    $result[ $instance_id ] = $shipping_zone[ 'zone_name' ] . ' → ' . $shipping_method->get_title() . $vl;
                }
            }


            $zero_zone = WC_Shipping_Zones::get_zone_by();

            if ( count( $zero_zone->get_shipping_methods() ) ) {

                foreach ( $zero_zone->get_shipping_methods() as $key => $shipping_method ) {

                    $instance_id = $shipping_method->get_instance_id();

                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $instance_id . ')' : '';

                    $result[ $instance_id ] = $shipping_method->get_title() . $vl;
                }
            }

            return $result;
        }

        public static function get_wc_tax_classes( $args ) {
            global $wpdb;
            $result = array();

            try {
                $tax_object = new WC_Tax();
                $tax_classes = $tax_object->get_tax_classes();
                $tax_classes_slug = $tax_object->get_tax_class_slugs();

                for ( $i = 0; $i < count( $tax_classes ); $i++ ) {
                    $vl = (isset( $args[ 'show_value' ] ) && $args[ 'show_value' ] == true) ? ' (' . $tax_classes_slug[ $i ] . ')' : '';
                    $result[ $tax_classes_slug[ $i ] ] = $tax_classes[ $i ] . $vl;
                }
            } catch ( Exception $ex ) {
                
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