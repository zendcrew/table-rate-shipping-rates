<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'Reon' ) ) {

    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Locale' ) && !defined( 'WTARS_SHIPPED_PREMIUM' ) ) {

    class WTARS_Shipped_Admin_Locale {

        private static $instance;
        private $prev_translation_ids_key = 'wtars_shipped_locale_ids_';
        private $translator_obj;

        public static function get_instance(): self {

            if ( is_null( self::$instance ) ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public function process_options( $options, $instance_id ) {

            $translator = $this->get_translator();

            if ( !$translator ) {

                return $options;
            }

            $strings = $this->get_strings( $options, $instance_id );

            if ( !$strings ) {

                return $options;
            }

            $this->unregister_prev_strings( $strings, $instance_id, $translator );

            $this->register_strings( $strings, $translator );

            return $options;
        }

        private function register_strings( $strings, $translator ) {

            if ( !method_exists( $translator, 'register_string' ) ) {

                return;
            }

            foreach ( $strings as $translation_id => $value ) {

                $translator->register_string( $value, $translation_id );
            }
        }

        private function unregister_prev_strings( $strings, $instance_id, $translator ) {

            if ( !method_exists( $translator, 'unregister_string' ) ) {

                return;
            }

            $new_translation_ids = array_keys( $strings );

            $prev_translation_ids = $this->get_prev_translation_id_ids( $instance_id );


            foreach ( $prev_translation_ids as $translation_id ) {

                if ( in_array( $translation_id, $new_translation_ids ) ) {

                    continue;
                }

                $translator->unregister_string( $translation_id );
            }

            $this->save_new_translation_id_ids( $new_translation_ids, $instance_id );
        }

        private function get_strings( $options, $instance_id ) {

            //TODO: Remove this block later
            if ( !defined( 'WTARS_SHIPPED_DEV_LOCALE' ) ) {

                return false;
            }
            //TODO: End of block

            $strings = apply_filters( 'wtars_shipped/get-locale-strings', array(), $options, $instance_id );

            if ( !is_array( $strings ) ) {

                return false;
            }

            if ( !count( $strings ) ) {

                return false;
            }

            return $strings;
        }

        private function get_prev_translation_id_ids( $instance_id ) {

            return get_option( $this->prev_translation_ids_key . $instance_id, array() );
        }

        private function save_new_translation_id_ids( $new_translation_ids, $instance_id ) {

            if ( !count( $new_translation_ids ) ) {

                delete_option( $this->prev_translation_ids_key . $instance_id );
            } else {

                update_option( $this->prev_translation_ids_key . $instance_id, $new_translation_ids, false );
            }
        }

        private function get_translator() {

            if ( !is_null( $this->translator_obj ) ) {

                return $this->translator_obj;
            }

            $this->translator_obj = false;

            $translators = $this->register_translators();

            if ( !is_array( $translators ) ) {

                return $this->translator_obj;
            }

            foreach ( $translators as $translator ) {

                if ( !class_exists( $translator ) ) {

                    continue;
                }

                if ( method_exists( $translator, 'get_instance' ) ) {

                    $this->translator_obj = $translator::get_instance();
                } else {

                    $this->translator_obj = new $translator();
                }

                return $this->translator_obj;
            }

            return $this->translator_obj;
        }

        private function register_translators() {

            $translators = array();

            //TODO: Remove this block later
            if ( !defined( 'WTARS_SHIPPED_DEV_LOCALE' ) ) {

                return $translators;
            }
            //TODO: End of block

            if ( class_exists( 'WTARS_Shipped_Polylang' ) ) {

                $translators[ 'polylang' ] = 'WTARS_Shipped_Polylang';
            }

            if ( class_exists( 'WTARS_Shipped_WPML' ) ) {

                $translators[ 'wpml' ] = 'WTARS_Shipped_WPML';
            }

            return apply_filters( 'wtars_shipped/register-locale-translators', $translators );
        }

    }

}