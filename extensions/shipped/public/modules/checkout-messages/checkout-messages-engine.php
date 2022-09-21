<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Checkout_Messages_Engine' ) ) {

    class WTARS_Shipped_Checkout_Messages_Engine {

        private $message_cache;

        public function __construct() {

            $this->message_cache = new WTARS_Shipped_Checkout_Messages_Cache();
            add_filter( 'wtars_shipped/process-data', array( $this, 'process_data' ), 30, 1 );
        }

        public function process_data( $data ) {

            //shipping instance needs to be set
            if ( !isset( $data[ 'wc' ][ 'instance_id' ] ) ) {
                return $data;
            }

            $instance_id = $data[ 'wc' ][ 'instance_id' ];

            // check for cached or in memory data
            if ( isset( $data[ 'messages' ][ $instance_id ] ) ) {

                return $data;
            }


            //get cart notifications
            $data = $this->get_messages( $data, $instance_id );


            //store cache data
            if ( isset( $data[ 'messages' ][ $instance_id ] ) ) {
                $this->message_cache->store_cache( $data[ 'messages' ], $instance_id );
            } else {
                $this->message_cache->store_cache( array(), $instance_id );
            }




            return $data;
        }

        public function get_messages( $data, $instance_id ) {

            //get settings            
            $messages_settings = $this->get_messages_settings( $instance_id );

            //check if cart notifications should be processed
            if ( 'no' == $messages_settings[ 'mode' ] ) {
                return $data;
            }

            // go through the handling fees and apply them
            foreach ( $this->get_message_options( $instance_id ) as $message_option ) {

                $message_option_head = array(
                    'apply_mode' => $message_option[ 'apply_mode' ],
                    'enable' => $message_option[ 'enable' ],
                    'message_id' => $message_option[ 'message_id' ],
                    'settings' => $messages_settings,
                    'instance_id' => $instance_id,
                    'module' => 'checkout-messages',
                    'sub_module' => 'messages',
                );

                $message_conditions = $message_option[ 'message_conditions' ];
                $message_conditions[ 'instance_id' ] = $instance_id;
                $message_conditions[ 'module' ] = 'checkout-messages';
                $message_conditions[ 'sub_module' ] = 'messages';


                if ( !$this->can_apply_message( $message_option_head, $message_conditions, $data ) ) {
                    continue;
                }

                $message = array(
                    'message_type' => $message_option[ 'message_type' ],
                    'always_show' => $message_option[ 'always_show' ],
                    'message' => $message_option[ 'message' ],
                    'parameters' => $this->get_parameters( $message_option, $instance_id, $data ),
                );

                if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                    $data = WTARS_Shipped_Premium_Checkout_Messages_Engine::add_message( $data, $message, $message_option_head );
                } else {
                    $data = $this->add_message( $data, $message, $message_option_head );
                }
            }

            if ( isset( $data[ 'messages' ][ $instance_id ] ) && has_filter( 'wtars_shipped/checkout-messages/gotten-messages' ) ) {
                $data[ 'messages' ][ $instance_id ] = apply_filters( 'wtars_shipped/checkout-messages/gotten-messages', $data[ 'messages' ][ $instance_id ], $data );
            }

            return $data;
        }

        private function get_parameters( $message_option, $instance_id, $data ) {

            $parameters = array();
            
            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $parameters = WTARS_Shipped_Premium_Checkout_Messages_Engine::get_parameters( $message_option, $instance_id, $data );
            }
            
            // allows other plugins to modify the processed parameters
            if ( has_filter( 'wtars_shipped/gotten-parameters' ) ) {
                $parameters = apply_filters( 'wtars_shipped/gotten-parameters', $parameters, $parameters_args, $data );
            }

            return $parameters;
        }

        private function can_apply_message( $message_option_head, $message_conditions, $data ) {

            // allows active message only

            if ( 'no' == $message_option_head[ 'enable' ] ) {
                return false;
            }

            $bool_val = true;

            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {
                $bool_val = WTARS_Shipped_Premium_Checkout_Messages_Engine::can_apply_message( $message_option_head, $data );
            }

            // allows other plugins to check if the message should apply

            if ( has_filter( 'wtars_shipped/checkout-messages/can-apply-message' ) ) {
                $bool_val = apply_filters( 'wtars_shipped/checkout-messages/can-apply-message', $bool_val, $message_option_head, $data );
            }

            // no need for validations
            if ( false == $bool_val ) {
                return $bool_val;
            }

            // validate message conditions

            $bool_val = WTARS_Shipped_Condition_Types::validate_conditions( $message_conditions, $data );


            return $bool_val;
        }

        private function add_message( $data, $message, $message_option_head ) {

            $instance_id = $message_option_head[ 'instance_id' ];

            $data[ 'messages' ][ $instance_id ][ $message_option_head[ 'message_id' ] ] = $message;

            // allows other plugins to modify message data once added
            if ( has_filter( 'wtars_shipped/checkout-messages/added-message' ) ) {

                if ( isset( $data[ 'messages' ][ $instance_id ][ $message_option_head[ 'message_id' ] ] ) ) {

                    $data[ 'messages' ][ $instance_id ][ $message_option_head[ 'message_id' ] ] = apply_filters( 'wtars_shipped/checkout-messages/added-message', $data[ 'messages' ][ $instance_id ][ $message_option_head[ 'message_id' ] ], $instance_id, $message_option_head );
                }
            }

            return $data;
        }

        private function get_messages_settings( $instance_id ) {

            $default = array(
                'mode' => 'no',
            );

            return WTARS_Shipped::get_option( 'checkout_messages_settings', $instance_id, $default );
        }

        private function get_message_options( $instance_id ) {

            $fee_options = array();

            foreach ( WTARS_Shipped::get_option( 'checkout_messages', $instance_id, array() ) as $fee_option ) {

                $fee_options[] = $fee_option;
            }

            return $fee_options;
        }

    }

}


