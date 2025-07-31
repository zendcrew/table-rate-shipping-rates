<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Settings_Section' ) ) {

    ReonUtil::recursive_require( dirname( __FILE__ ), array( 'settings-section.php' ) );

    class WTARS_Shipped_Admin_Settings_Section extends WTARS_Shipped_Admin_Section {

        protected function init() {

            $option_name = $this->get_option_name();

            add_filter( 'get-option-page-' . $option_name . 'section-settings-fields', array( $this, 'get_page_fields' ), 10 );
        }

        public function get_page_fields( $in_fields ) {

            $args = array(
                'instance_id' => $this->get_instance_id(),
                'option_name' => $this->get_option_name(),
            );

            return apply_filters( 'wtars_shipped_admin/get-settings-section-fields', $in_fields, $args );
        }

    }

}