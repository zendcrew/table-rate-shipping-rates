<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Util' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    WTARS_Shipped_Inc::recursive_require( dirname( __FILE__ ), array( 'shipped-utils.php' ) );

    class WTARS_Shipped_Util {

        public static function round_num( $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP ): float {
            if ( !is_numeric( $val ) ) {
                $val = floatval( $val );
            }
            return round( $val, $precision, $mode );
        }

        public static function get_product_ids_by_slugs( $slugs ) {

            $product_ids = array();


            $sql_hash = 'shp_product_ids' . md5( wp_json_encode( $slugs ) );

            $t_product_ids = get_transient( $sql_hash );

            if ( $t_product_ids ) {
                return $t_product_ids;
            }

            try {
                global $wpdb;

                $sql = "SELECT id FROM " . $wpdb->posts
                        . " WHERE (post_type='product')"
                        . " AND post_name IN('" . implode( "','", array_map( 'esc_sql', $slugs ) ) . "')";


                $results = $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

                foreach ( $results as $row ) {
                    $product_ids[] = $row[ 'id' ];
                }

                set_transient( $sql_hash, $product_ids, MINUTE_IN_SECONDS + 30 );
            } catch ( Exception $ex ) {
                return $product_ids;
            }


            return $product_ids;
        }

        public static function get_product_term_ids_by_slugs( $slugs, $taxonomy ) {
            $term_ids = array();

            $sql_hash = 'shp_' . $taxonomy . '_terms_ids' . md5( wp_json_encode( $slugs ) );

            $t_term_ids = get_transient( $sql_hash );

            if ( $t_term_ids ) {
                return $t_term_ids;
            }

            try {
                global $wpdb;

                $sql = "SELECT terms.term_id FROM {$wpdb->prefix}terms AS terms"
                        . " LEFT JOIN {$wpdb->prefix}term_taxonomy AS term_taxonomy ON terms.term_id=term_taxonomy.term_id"
                        . " WHERE (term_taxonomy.taxonomy=%s)"
                        . " AND slug IN('" . implode( "','", array_map( 'esc_sql', $slugs ) ) . "')";

                $results = $wpdb->get_results( $wpdb->prepare( $sql, $taxonomy ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

                foreach ( $results as $row ) {
                    $term_ids[] = $row[ 'term_id' ];
                }

                set_transient( $sql_hash, $term_ids, MINUTE_IN_SECONDS + 30 );
            } catch ( Exception $ex ) {
                return $term_ids;
            }
            return $term_ids;
        }

    }

}