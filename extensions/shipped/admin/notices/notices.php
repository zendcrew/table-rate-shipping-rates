<?php

if ( !class_exists( 'Reon' ) ) {
    return;
}

if ( !class_exists( 'WTARS_Shipped_Admin_Notices' ) ) {

    class WTARS_Shipped_Admin_Notices {

        private static $instance;
        private $notices_name;

        private function __construct() {

            $this->notices_name = 'wtars_shipped_admin_notices';

            add_action( 'admin_notices', array( $this, 'show_notice' ) );
            add_action( 'wp_ajax_wtars_shipped_dismiss_notice', array( $this, 'dismiss_notice' ) );
        }

        public static function get_instance(): self {

            if ( !self::$instance ) {

                self::$instance = new self();
            }

            return self::$instance;
        }

        public function enqueue_scripts() {

            wp_enqueue_script( 'shipped_notices_scripts_js', WTARS_SHIPPED_ASSETS_URL . 'admin-notices.js', array( 'jquery' ), '1.0', true );
        }

        public function show_notice() {

            if ( $this->can_show_pro_notices() ) {

                include_once 'notices-view-pro.php';
            } else if ( $this->can_show_notices() ) {

                include_once 'notices-view.php';
            }
        }

        public function dismiss_notice() {

            if ( !isset( $_POST[ 'dismiss_notice_id' ] ) ) {

                wp_die();
            }

            $maybe_later = false;

            if ( isset( $_POST[ 'mayme_later' ] ) ) {

                $maybe_later = ('yes' == sanitize_text_field( $_POST[ 'mayme_later' ] ));
            }

            $notice_id = sanitize_text_field( $_POST[ 'dismiss_notice_id' ] );


            $review = $this->get_notice_option();

            if ( !$maybe_later && $this->notice_is_pro( $notice_id ) ) {

                $review[ 'review_request_pro' ][ 'dismissed' ] = true;
            } else if ( !$maybe_later ) {
                $review[ 'review_request' ][ 'dismissed' ] = true;
            }

            $time = time();

            $review[ 'review_request_pro' ][ 'time' ] = $time;
            $review[ 'review_request' ][ 'time' ] = $time;

            update_option( $this->notices_name, $review );

            wp_die();
        }

        private function can_show_notices() {

            if ( !is_super_admin() ) {

                return false;
            }

            $review = $this->get_notice_option();

            $review_request = $review[ 'review_request' ];

            return $this->can_show_request( $review_request, 3 );
        }

        private function can_show_pro_notices() {

            if ( !is_super_admin() ) {

                return false;
            }

            if ( !$this->is_pro_version() ) {

                return false;
            }

            $review = $this->get_notice_option();

            $review_request = $review[ 'review_request_pro' ];

            return $this->can_show_request( $review_request, 3 );
        }

        private function get_notice_option() {

            $nodice_option = get_option( $this->notices_name, false );

            if ( !$nodice_option ) {

                return $this->get_default_notice_option();
            }

            return $nodice_option;
        }

        private function get_default_notice_option() {

            $default_request_review = array(
                'time' => time(),
                'dismissed' => false,
            );

            $default_review = array(
                'review_request' => $default_request_review,
                'review_request_pro' => $default_request_review,
            );

            update_option( $this->notices_name, $default_review );

            return $default_review;
        }

        private function can_show_request( $review_request, $days ) {

            if ( $review_request[ 'dismissed' ] ) {

                return false;
            }

            $time = $review_request[ 'time' ] + (DAY_IN_SECONDS * $days);

            $current_time = time();

            return ($time < $current_time);
        }

        private function notice_is_pro( $notice_id ) {

            return ('pro' == $notice_id);
        }

        private function is_pro_version() {

            if ( class_exists( 'WTARS_Shipped_Premium' ) ) {

                return true;
            }

            return false;
        }

    }

    WTARS_Shipped_Admin_Notices::get_instance();
}
