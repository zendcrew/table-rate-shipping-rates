<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Conditions_Package_Items' ) ) {

    class WTARS_Shipped_Admin_Conditions_Package_Items {

        public function __construct() {
            
            add_filter( 'wtars_shipped_admin/get-condition-groups', array( $this, 'get_groups' ), 90, 2 );
            add_filter( 'wtars_shipped_admin/get-package_items-group-conditions', array( $this, 'get_conditions' ), 10, 2 );

            add_filter( 'wtars_shipped_admin/get-package_ids-condition-fields', array( $this, 'get_products_fields' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-package_cats-condition-fields', array( $this, 'get_categories_fields' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-package_tags-condition-fields', array( $this, 'get_tags_fields' ), 10, 2 );
            add_filter( 'wtars_shipped_admin/get-package_sh_classes-condition-fields', array( $this, 'get_shipping_classes_fields' ), 10, 2 );
        }

        public function get_groups( $in_groups, $args ) {

            $in_groups[ 'package_items' ] = esc_html__( 'Items In Package', 'table-rate-shipping-rates' );

            return $in_groups;
        }

        public function get_conditions( $in_list, $args ) {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                $in_list[ 'package_ids' ] = esc_html__( 'Products In Package', 'table-rate-shipping-rates' );
                $in_list[ 'prem_1' ] = esc_html__( 'Variations In Package (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'package_cats' ] = esc_html__( 'Categories In Package', 'table-rate-shipping-rates' );
                $in_list[ 'package_tags' ] = esc_html__( 'Tags In Package', 'table-rate-shipping-rates' );
                $in_list[ 'prem_2' ] = esc_html__( 'Attributes In Package (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'prem_3' ] = esc_html__( 'Tax Classes In Package (Premium)', 'table-rate-shipping-rates' );
                $in_list[ 'package_sh_classes' ] = esc_html__( 'Shipping Classes In Package', 'table-rate-shipping-rates' );
                $in_list[ 'prem_4' ] = esc_html__( 'Product Meta Fields In Package (Premium)', 'table-rate-shipping-rates' );
            }

            return $in_list;
        }

        public function get_products_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                    'in_all_list' => esc_html__( 'All in the list', 'table-rate-shipping-rates' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'table-rate-shipping-rates' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'table-rate-shipping-rates' ),
                    'none' => esc_html__( 'None in the list', 'table-rate-shipping-rates' ),
                ),
                'width' => '99%',
                'box_width' => '29%',
            );

            $in_fields[] = array(
                'id' => 'product_slugs',
                'type' => 'select2',
                'multiple' => true,
                'minimum_input_length' => 2,
                'placeholder' => esc_html__( 'Search products...', 'table-rate-shipping-rates' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'wc:products',
                    'ajax' => true,
                    'value_col' => 'slug',
                ),
                'width' => '100%',
                'box_width' => '71%',
            );

            return $in_fields;
        }

        public function get_categories_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                    'in_all_list' => esc_html__( 'All in the list', 'table-rate-shipping-rates' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'table-rate-shipping-rates' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'table-rate-shipping-rates' ),
                    'none' => esc_html__( 'None in the list', 'table-rate-shipping-rates' ),
                ),
                'width' => '99%',
                'box_width' => '29%',
            );

            $in_fields[] = array(
                'id' => 'category_slugs',
                'type' => 'select2',
                'multiple' => true,
                'minimum_input_length' => 1,
                'placeholder' => esc_html__( 'Search categories...', 'table-rate-shipping-rates' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'categories:product_cat',
                    'ajax' => true,
                    'value_col' => 'slug',
                ),
                'width' => '100%',
                'box_width' => '71%',
            );

            return $in_fields;
        }

        public function get_tags_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                    'in_all_list' => esc_html__( 'All in the list', 'table-rate-shipping-rates' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'table-rate-shipping-rates' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'table-rate-shipping-rates' ),
                    'none' => esc_html__( 'None in the list', 'table-rate-shipping-rates' ),
                ),
                'width' => '99%',
                'box_width' => '29%',
            );

            $in_fields[] = array(
                'id' => 'tag_slugs',
                'type' => 'select2',
                'multiple' => true,
                'minimum_input_length' => 1,
                'placeholder' => esc_html__( 'Search tags...', 'table-rate-shipping-rates' ),
                'allow_clear' => true,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'categories:product_tag',
                    'ajax' => true,
                    'value_col' => 'slug',
                ),
                'width' => '100%',
                'box_width' => '71%',
            );

            return $in_fields;
        }

        public function get_shipping_classes_fields( $in_fields, $args ){
            
            $in_fields[] = array(
                'id' => 'compare',
                'type' => 'select2',
                'default' => 'in_list',
                'options' => array(
                    'in_list' => esc_html__( 'Any in the list', 'table-rate-shipping-rates' ),
                    'in_all_list' => esc_html__( 'All in the list', 'table-rate-shipping-rates' ),
                    'in_list_only' => esc_html__( 'Only in the list', 'table-rate-shipping-rates' ),
                    'in_all_list_only' => esc_html__( 'Only all in the list', 'table-rate-shipping-rates' ),
                    'none' => esc_html__( 'None in the list', 'table-rate-shipping-rates' ),
                ),
                'width' => '99%',
                'box_width' => '29%',
            );

            $in_fields[] = array(
                'id' => 'shipping_classes',
                'type' => 'select2',
                'multiple' => true,
                'placeholder' => esc_html__( 'Search shipping classes...', 'table-rate-shipping-rates' ),
                'minimum_input_length' => 1,
                'minimum_results_forsearch' => 10,
                'data' => array(
                    'source' => 'wc:shipping_classes',
                    'ajax' => true,
                    'value_col' => 'slug',
                ),
                'width' => '100%',
                'box_width' => '71%',
            );

            return $in_fields;
        }
    }

    new WTARS_Shipped_Admin_Conditions_Package_Items();
}
