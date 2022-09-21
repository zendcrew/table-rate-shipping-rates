<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTARS_Shipped_Polylang_Messages' ) ) {

    class WTARS_Shipped_Polylang_Messages {

        public function __construct() {

            add_filter( 'wtars_shipped/message-text', array( $this, 'tranlate_message' ), 10, 4 );
        }

        public function tranlate_message( $message, $message_id, $message_type, $instance_id ) {

            $id_prefix = $this->get_string_id_prefix( $message_id, $instance_id );

            $string_id = $id_prefix;

            return WTARS_Shipped_Polylang::get_translated_string( $message, $string_id );
        }

        public function get_strings( $options, $instance_id ) {

            $message_strings = array();

            foreach ( $this->get_messages( $options ) as $message ) {

                $key = $this->get_string_id_prefix( $message[ 'message_id' ], $instance_id );

                $message_strings[ $key ] = array(
                    'value' => $message[ 'message' ],
                    'is_multiline' => true
                );
            }

            return $message_strings;
        }

        private function get_string_id_prefix( $message_id, $instance_id ) {

            return 'wtars_shipped_message_' . $instance_id . '_' . $message_id;
        }

        private function get_messages( $options ) {

            if ( !isset( $options[ 'checkout_messages' ] ) ) {

                return array();
            }

            return $options[ 'checkout_messages' ];
        }

    }

}