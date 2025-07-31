<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Section' ) ) {

    class WTARS_Shipped_Admin_Section {

        private $option_name;
        private $instance_id;

        public function __construct( $option_name, $instance_id ) {
            
            $this->option_name = $option_name;
            
            $this->instance_id = $instance_id;
            
            $this->init();
        }

        protected function init() {}

        protected function get_option_name() {
            
            return $this->option_name;
        }

        protected function get_instance_id() {
            
            return $this->instance_id;
        }

    }

}