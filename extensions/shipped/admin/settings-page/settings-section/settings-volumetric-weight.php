<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Volumetric_Weight_Settings' ) ) {

    class WTARS_Shipped_Admin_Volumetric_Weight_Settings {

        public function __construct() {
            add_filter( 'wtars_shipped_admin/get-settings-section-fields', array( $this, 'get_panel' ), 50, 2 );

            add_filter( 'reon/get-repeater-field-weight_calcs-templates', array( $this, 'get_templates' ), 10, 2 );

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                add_filter( 'roen/get-repeater-template-weight_calcs-calc_default-fields', array( $this, 'get_template_fields' ), 10, 2 );
            }
        }

        public function get_panel( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'panel',
                'last' => true,
                'white_panel' => false,
                'panel_size' => 'smaller',
                'width' => '100%',
                'field_css_class' => array( 'shipped_setting_panel' ),
                'merge_fields' => false,
                'fields' => $this->get_fields( array(), $args ),
            );

            return $in_fields;
        }

        public function get_templates( $in_templates, $repeater_args ) {

            $in_templates[] = array(
                'id' => 'calc_default',
                'head' => array(
                    'title' => '',
                    'defaut_title' => esc_html__( 'Weight', 'table-rate-shipping-rates' ),
                    'title_field' => 'admin_note',
                    'subtitle_field' => 'formula',
                ),
                'empy_button' => true,
            );

            $in_templates[] = array(
                'id' => 'calc_option',
                'head' => array(
                    'title' => '',
                    'defaut_title' => esc_html__( 'Volumetric Weight', 'table-rate-shipping-rates' ),
                    'title_field' => 'admin_note',
                    'subtitle_field' => 'formula',
                ),
            );

            return $in_templates;
        }

        public static function get_template_fields( $in_fields, $repeater_args ) {

            $in_fields[] = array(
                'id' => 'option_id',
                'type' => 'autoid',
                'autoid' => 'shipped',
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 3,
                'full_width' => true,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'admin_note',
                        'type' => 'textbox',
                        'tooltip' => esc_html__( 'Adds a private note for reference purposes', 'table-rate-shipping-rates' ),
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Admin Note', 'table-rate-shipping-rates' ),
                        'default' => '',
                        'placeholder' => esc_html__( 'Type here...', 'table-rate-shipping-rates' ),
                        'width' => '100%',
                    ),
                    array(
                        'id' => 'formula',
                        'type' => 'select2',
                        'column_size' => 1,
                        'column_title' => esc_html__( 'Volumetric Weight Formula', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Determines how volumetric weights should be calculated', 'table-rate-shipping-rates' ),
                        'default' => 'no',
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => array(
                            'no' => esc_html__( 'No formula', 'table-rate-shipping-rates' ),
                            'prem_1' => esc_html__( 'Weight = ( L x W x H ) / DIM Factor (Premium)', 'table-rate-shipping-rates' ),
                            'prem_2' => esc_html__( 'Weight = ( L x W x H ) x DIM Factor (Premium)', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                        'fold_id' => 'weight_formula',
                    ),
                ),
            );

            $in_fields[] = array(
                'id' => 'any_ids',
                'type' => 'columns-field',
                'columns' => 5,
                'full_width' => true,
                'merge_fields' => false,
                'fields' => array(
                    array(
                        'id' => 'round_weight',
                        'type' => 'select2',
                        'column_size' => 2,
                        'column_title' => esc_html__( 'Round Weight', 'table-rate-shipping-rates' ),
                        'tooltip' => esc_html__( 'Determines how weights or volumetric weight should be rounded', 'table-rate-shipping-rates' ),
                        'default' => 'no',
                        'disabled_list_filter' => 'wtars_shipped_admin/get-disabled-list',
                        'options' => array(
                            'no' => esc_html__( 'Do not round weight', 'table-rate-shipping-rates' ),
                            'prem_1' => esc_html__( 'Round weight up to whole unit (Premium)', 'table-rate-shipping-rates' ),
                            'prem_2' => esc_html__( 'Round weight down to whole unit (Premium)', 'table-rate-shipping-rates' ),
                            'prem_3' => esc_html__( 'Round weight to the nearest whole unit (Premium)', 'table-rate-shipping-rates' ),
                            'prem_4' => esc_html__( 'Round weight up to half unit (Premium)', 'table-rate-shipping-rates' ),
                            'prem_5' => esc_html__( 'Round weight down to half unit (Premium)', 'table-rate-shipping-rates' ),
                            'prem_6' => esc_html__( 'Round weight to the nearest half unit (Premium)', 'table-rate-shipping-rates' ),
                        ),
                        'width' => '100%',
                    ),
                ),
            );

            return $in_fields;
        }

        private function get_fields( $in_fields, $args ) {

            $in_fields[] = array(
                'id' => 'any_id',
                'type' => 'paneltitle',
                'full_width' => true,
                'center_head' => true,
                'title' => esc_html__( 'Weights &amp; Volumetric Weight Calculation', 'table-rate-shipping-rates' ),
                'desc' => esc_html__( 'Use these settings to control how item weights should be calculated', 'table-rate-shipping-rates' ),
            );

            $max_sections = 1;
            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $max_sections = 99999;
            }

            $in_fields[] = array(
                'id' => 'weight_calcs',
                'type' => 'repeater',
                'white_repeater' => true,
                'repeater_size' => 'smaller',
                'accordions' => true,
                'buttons_sep' => false,
                'delete_button' => true,
                'clone_button' => false,
                'width' => '100%',
                'max_sections' => $max_sections,
                'max_sections_msg' => esc_html__( 'Please upgrade to premium version in order to add more options', 'table-rate-shipping-rates' ),
                'default' => $this->get_default_calc(),
                'static_template' => 'calc_default',
                'section_type_id' => 'calc_option_type',
                'auto_expand' => array(
                    'new_section' => true,
                    'cloned_section' => false,
                ),
                'sortable' => array(
                    'enabled' => false,
                ),
                'template_adder' => array(
                    'position' => 'right',
                    'show_list' => false,
                    'button_text' => esc_html__( 'Add Calculator', 'table-rate-shipping-rates' ),
                ),
            );

            return $in_fields;
        }

        private function get_default_calc() {

            if ( !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                return array(
                    array(
                        'calc_option_type' => 'calc_default',
                        'option_id' => '2234343',
                    ),
                );
            }

            return array(
                array(
                    'calc_option_type' => 'calc_default',
                    'option_id' => '2234343',
                ),
                array(
                    'calc_option_type' => 'calc_option',
                    'option_id' => '2234344',
                ),
            );
        }

    }

    new WTARS_Shipped_Admin_Volumetric_Weight_Settings();
}


