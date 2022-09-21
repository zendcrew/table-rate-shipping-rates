<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Conditions_Package_Items' ) ) {

    class WTARS_Shipped_Conditions_Package_Items {

        public function __construct() {

            add_filter( 'wtars_shipped/validate-package_ids-condition', array( $this, 'validate_product_ids' ), 10, 2 );
            add_filter( 'wtars_shipped/validate-package_cats-condition', array( $this, 'validate_categories' ), 10, 2 );
            add_filter( 'wtars_shipped/validate-package_tags-condition', array( $this, 'validate_tags' ), 10, 2 );
            add_filter( 'wtars_shipped/validate-package_sh_classes-condition', array( $this, 'validate_shipping_classes' ), 10, 2 );
        }

        public function validate_product_ids( $condition, $data ) {

            if ( !isset( $condition[ 'product_slugs' ] ) ) {
                return false;
            }

            $rule_product_ids = WTARS_Shipped_Util::get_product_ids_by_slugs( $condition[ 'product_slugs' ] );

            if ( !count( $rule_product_ids ) ) {
                return false;
            }

            $product_ids = WTARS_Shipped_Order_Util::get_package_product_ids( $data );

            $rule_compare = $condition[ 'compare' ];

            return WTARS_Shipped_Validation_Util::validate_list_list( $product_ids, $rule_product_ids, $rule_compare );
        }

        public function validate_categories( $condition, $data ) {

            if ( !isset( $condition[ 'category_slugs' ] ) ) {
                return false;
            }

            $rule_category_ids = WTARS_Shipped_Util::get_product_term_ids_by_slugs( $condition[ 'category_slugs' ], 'product_cat' );

            if ( !count( $rule_category_ids ) ) {
                return false;
            }

            $category_ids = WTARS_Shipped_Order_Util::get_package_category_ids( $data );

            $rule_compare = $condition[ 'compare' ];

            return WTARS_Shipped_Validation_Util::validate_list_list( $category_ids, $rule_category_ids, $rule_compare );
        }

        public function validate_tags( $condition, $data ) {

            if ( !isset( $condition[ 'tag_slugs' ] ) ) {
                return false;
            }

            $rule_tag_ids = WTARS_Shipped_Util::get_product_term_ids_by_slugs( $condition[ 'tag_slugs' ], 'product_tag' );

            if ( !count( $rule_tag_ids ) ) {
                return false;
            }

            $tag_ids = WTARS_Shipped_Order_Util::get_package_tag_ids( $data );

            $rule_compare = $condition[ 'compare' ];

            return WTARS_Shipped_Validation_Util::validate_list_list( $tag_ids, $rule_tag_ids, $rule_compare );
        }

        public function validate_shipping_classes( $condition, $data ) {

            if ( !isset( $condition[ 'shipping_classes' ] ) ) {
                return false;
            }

            $rule_shipping_classes = WTARS_Shipped_Util::get_product_term_ids_by_slugs( $condition[ 'shipping_classes' ], 'product_shipping_class' );

            if ( !count( $rule_shipping_classes ) ) {
                return false;
            }

            $shipping_classes = WTARS_Shipped_Order_Util::get_package_shipping_classes( $data );

            $rule_compare = $condition[ 'compare' ];

            return WTARS_Shipped_Validation_Util::validate_list_list( $shipping_classes, $rule_shipping_classes, $rule_compare );
        }

    }

    new WTARS_Shipped_Conditions_Package_Items();
}