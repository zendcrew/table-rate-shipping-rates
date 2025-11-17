<?php

if ( !defined( 'ABSPATH' ) ) {

    exit;
}

if ( !class_exists( 'WTARS_Shipped_Locale' ) ) {

    class WTARS_Shipped_Locale {

        private static $instance;
        private $translator_obj;

        public static function get_instance(): self {

            if ( is_null( self::$instance ) ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public function get_current_language() {

            $translator = $this->get_translator();

            if ( !$translator ) {

                return false;
            }

            if ( method_exists( $translator, 'get_current_language' ) ) {

                return $translator->get_current_language();
            }

            return false;
        }

        public function translate_product_ids( $product_ids, $translation_id ) {

            $translated_product_ids = array();

            foreach ( $product_ids as $key => $product_id ) {

                $translation_id_key = $translation_id . '-' . $key;

                $translated_product_ids[ $key ] = $this->translate_product_id( $product_id, $translation_id_key );
            }

            return $translated_product_ids;
        }

        public function translate_product_id( $product_id, $translation_id ) {

            if ( !$this->can_translate_products() ) {

                return $product_id;
            }

            $translator = $this->get_translator();

            if ( !$translator ) {

                return $product_id;
            }

            if ( method_exists( $translator, 'translate_product_id' ) ) {

                return $translator->translate_product_id( $product_id, $translation_id );
            }

            return $product_id;
        }

        public function translate_term_ids( $term_ids, $translation_id, $taxonomy ) {

            $translated_term_ids = array();

            foreach ( $term_ids as $key => $term_id ) {

                $translation_id_key = $translation_id . '-' . $key;

                $translated_term_ids[ $key ] = $this->translate_term_id( $term_id, $translation_id_key, $taxonomy );
            }

            return $translated_term_ids;
        }

        public function translate_term_id( $term_id, $translation_id, $taxonomy ) {

            if ( !$this->can_translate_terms() ) {

                return $term_id;
            }

            $translator = $this->get_translator();

            if ( !$translator ) {

                return $term_id;
            }

            if ( method_exists( $translator, 'translate_term_id' ) ) {

                return $translator->translate_term_id( $term_id, $translation_id, $taxonomy );
            }

            return $term_id;
        }

        public function translate_string( $value, $translation_id ) {

            $translator = $this->get_translator();

            if ( !$translator ) {

                return $value;
            }

            if ( method_exists( $translator, 'translate_string' ) ) {

                return $translator->translate_string( $value, $translation_id );
            }

            return $value;
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

            foreach ( $translators as $key => $translator ) {

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

        private function can_translate_products() {

            if ( !defined( 'WTARS_SHIPPED_LOCALE_PRODUCTS' ) ) {

                return true;
            }

            if ( true === WTARS_SHIPPED_LOCALE_PRODUCTS ) {

                return true;
            }

            return false;
        }

        private function can_translate_terms() {

            if ( !defined( 'WTARS_SHIPPED_LOCALE_TERMS' ) ) {

                return true;
            }

            if ( true === WTARS_SHIPPED_LOCALE_TERMS ) {

                return true;
            }

            return false;
        }

    }

}
