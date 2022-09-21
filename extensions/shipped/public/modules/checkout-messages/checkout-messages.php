<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


if ( !class_exists( 'WTARS_Shipped_Checkout_Messages' ) ) {

    WTARS_Shipped_Inc::recursive_require( dirname( __FILE__ ), array( 'checkout-messages.php' ) );

    class WTARS_Shipped_Checkout_Messages {

        public function process_messages( $data, $instance_id ) {

            //get settings
            $settings = WTARS_Shipped::get_option( 'checkout_message_settings', $instance_id, array( 'show_on_cart' => 'yes' ) );

            if ( 'no' == $settings[ 'show_on_cart' ] && is_cart() ) {
                return;
            }

            if ( !isset( $data[ 'messages' ][ $instance_id ] ) ) {
                return;
            }


            if ( defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

                WTARS_Shipped_Premium_Checkout_Messages::process_messages( $data, $instance_id );

                return;
            }

            foreach ( $data[ 'messages' ][ $instance_id ] as $message_id => $messages_data ) {
                $this->process_message( $messages_data, $message_id, $instance_id );
            }
        }

        private function process_message( $message_data, $message_id, $instance_id ) {

            if ( '' == $message_data[ 'message' ] ) {
                return;
            }

            $raw_message = apply_filters( 'wtars_shipped/message-text', $message_data[ 'message' ], $message_id, 'error', $instance_id );

            $message = $this->prepare_message( $raw_message, $message_data[ 'parameters' ], $instance_id );

            if ( !wc_has_notice( $message, 'error' ) ) {

                wc_add_notice( $message, 'error' );
            }
        }

        private function prepare_message( $message, $parameters, $instance_id ) {

            foreach ( $parameters as $parameter_id => $parameter ) {

                $parameter_type = $parameter[ 'parameter_type' ];

                $parameter_value = apply_filters( 'wtars_shipped/parameter-value', $parameter[ 'value' ], $parameter_id, $parameter_type, $instance_id );

                if ( $parameter[ 'is_currency' ] && is_numeric( $parameter_value ) ) {
                    $parameter_value = wc_price( apply_filters( 'wtars_shipped/parameter-currency-value', $parameter_value, $parameter_id, $parameter_type, $instance_id ) );
                }

                $rep_value = '/{{' . $parameter_id . '}}/';

                $message = preg_replace( $rep_value, $parameter_value, $message );
            }

            return $message;
        }

    }

}