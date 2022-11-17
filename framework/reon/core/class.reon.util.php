<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ReonUtil')) {

    class ReonUtil {

        public static function array_to_styles($array_object = array()) {
            $result = '';
            foreach ($array_object as $key => $value) {
                $result = $result . esc_attr($key) . ":" . esc_attr($value) . ";";
            }
            return $result;
        }

        public static function array_to_attributes($array_object = array()) {
            $result = '';
            $cnt = count($array_object) - 1;

            foreach ($array_object as $key => $value) {

                $at_value = $value;
                if ($value === true) {
                    $at_value = 'true';
                }
                if ($value === false) {
                    $at_value = 'false';
                }
                $result = $result . esc_attr($key) . '=' . '"' . esc_attr($at_value) . '"';
                $cnt--;
                if ($cnt >= 0) {
                    $result = $result . ' ';
                }
            }
            return $result;
        }

        public static function array_to_classes($array_object = array()) {
            return implode(' ', $array_object);
        }

        public static function array_to_dropdown_list($array_object = array(), $selected_object = '', $disabled_object = '') {
            $selected = array();
            $disabled = array();

            if (is_array($selected_object)) {
                $selected = $selected_object;
            } else if ($selected_object != '') {
                $selected[] = $selected_object;
            } else if (isset($array_object['']) && $selected_object == '') {
                $selected[] = $selected_object;
            }

            if (is_array($disabled_object)) {
                $disabled = $disabled_object;
            } else if ($disabled_object != '') {
                $disabled[] = $disabled_object;
            }

            foreach ($array_object as $key => $text) {
                $drop_value = esc_attr($key);
                if (is_array($text)) {
                    ?>
                    <optgroup label="<?php echo esc_attr($text['label']); ?>">
                        <?php
                        self::array_to_dropdown_list($text['options'], $selected_object, $disabled_object);
                        ?>
                    </optgroup>
                    <?php
                } else {
                    ?>
                    <option value="<?php echo esc_attr($drop_value); ?>"<?php echo esc_html(self::s_c_r_d_helper($key, $selected, 'selected')); ?><?php echo esc_html(self::s_c_r_d_helper($key, $disabled, 'disabled')); ?>><?php echo esc_html($text); ?></option>
                    <?php
                }
            }
        }

        public static function s_c_r_d_helper($value, $array_or_string, $type = 'selected', $echo = false) {

            $result = '';
            if (self::s_c_r_d_check($value, $array_or_string)) {
                $result = ' ' . esc_attr($type) . '="' . esc_attr($type) . '"';
            }
            if ($echo) {
                echo wp_kses_post($result);
            }
            return $result;
        }

        public static function s_c_r_d_check($value, $array_or_string) {

            if (is_array($array_or_string)) {
                foreach ($array_or_string as $item) {
                    if ((string) $value === (string) $item) {
                        return true;
                    }
                }
            } else {
                if ((string) $value === (string) $array_or_string) {
                    return true;
                }
            }
            return false;
        }

        public static function get_session_object($id) {
            return $_SESSION[$id];
        }

        public static function is_session_object_set($id) {
            return (isset($_SESSION[$id]));
        }

        public static function set_session_object($id, $object = array()) {
            $_SESSION[$id] = $object;
        }

        public static function unset_session_object($id) {
            if (self::is_session_object_set($id)) {
                unset($_SESSION[$id]);
            }
        }

        public static function get_screen_args_from_field($field, $args = array()) {
            $args['screen'] = $field['screen'];

            if (isset($field['field_args'])) {
                $args['field_args'] = $field['field_args'];
            }

            if (isset($field['title_field'])) {
                $args['title_field'] = $field['title_field'];
            }

            if (isset($field['subtitle_field'])) {
                $args['subtitle_field'] = $field['subtitle_field'];
            }
            if (isset($field['repeater_id'])) {
                $args['repeater_id'] = $field['repeater_id'];
            }

            if (isset($field['in_repeater'])) {
                $args['in_repeater'] = $field['in_repeater'];
            }

            if (isset($field['parent_id'])) {
                $args['parent_id'] = $field['parent_id'];
            }

            if (isset($field['post_id'])) {
                $args['post_id'] = $field['post_id'];
            }
            if (isset($field['metabox_id'])) {
                $args['metabox_id'] = $field['metabox_id'];
            }
            if (isset($field['option_name'])) {
                $args['option_name'] = $field['option_name'];
            }

            return $args;
        }

        public static function random_char($length = 10, $allowedchars = '') {
            $result = '';
            $cnt = 0;
            $allowed_chars = str_split(!empty($allowedchars) ? $allowedchars : '0123456789abcdefghijklmnopqrstwxyzABCDEFGHIJKLMNOPQRSTWXYZ-_+=!#@~.,', 1);
            while ($cnt < $length) {
                $index = rand(0, (count($allowed_chars) - 1));
                $result = $result . $allowed_chars[$index];
                $cnt++;
            }
            return $result;
        }

        public static function random_char_from_pattern($pattern) {
            $code = '';
            foreach (self::get_random_pattern($pattern) as $value) {
                if (isset($value['allowed'])) {
                    $code .= self::random_char($value['length'], $value['allowed']);
                }
                if (isset($value['text'])) {
                    $code .= $value['text'];
                }
            }
            return $code;
        }

        private static function get_random_pattern($pattern) {
            //[N5],[A3],[C3],[Aa4],[Cc3],[a4],[c3]
            $result = array();
            $splts = str_split($pattern, 1);
            $brk_open = false;
            $p_text = '';

            foreach ($splts as $splt) {
                if ($splt == ']' && $brk_open == true) {
                    $t_splts = str_split($p_text);
                    if (count($t_splts) > 0) {
                        $s_tp = '';
                        $s_ln = '';
                        foreach ($t_splts as $t_splt) {
                            if ($t_splt == 'A' || $t_splt == 'a' || $t_splt == 'C' || $t_splt == 'c' || $t_splt == 'N') {
                                $s_tp .= $t_splt;
                            } else {
                                $s_ln .= $t_splt;
                            }
                        }
                        if ($s_tp == 'N') {
                            $s_tp = '0123456789';
                        }
                        if ($s_tp == 'A') {
                            $s_tp = 'ABCDEFGHIJKLMNOPQRSTWXYZ';
                        }
                        if ($s_tp == 'a') {
                            $s_tp = 'abcdefghijklmnopqrstwxyz';
                        }
                        if ($s_tp == 'Aa') {
                            $s_tp = 'abcdefghijklmnopqrstwxyzABCDEFGHIJKLMNOPQRSTWXYZ';
                        }
                        if ($s_tp == 'C') {
                            $s_tp = '0123456789ABCDEFGHIJKLMNOPQRSTWXYZ';
                        }
                        if ($s_tp == 'c') {
                            $s_tp = '0123456789abcdefghijklmnopqrstwxyz';
                        }
                        if ($s_tp == 'Cc') {
                            $s_tp = '0123456789abcdefghijklmnopqrstwxyzABCDEFGHIJKLMNOPQRSTWXYZ';
                        }
                        $result[] = array(
                            'allowed' => $s_tp,
                            'length' => absint($s_ln)
                        );
                    }


                    $p_text = '';
                    $brk_open = false;
                } else if ($splt == '[' && $brk_open == false) {
                    if (strlen($p_text) > 0) {
                        $result[] = array(
                            'text' => $p_text,
                            'length' => strlen($p_text)
                        );
                        $p_text = '';
                    }
                    $brk_open = true;
                } else {
                    $p_text .= $splt;
                }
            }
            if (strlen($p_text) > 0) {
                $result[] = array(
                    'text' => $p_text,
                    'length' => strlen($p_text)
                );
            }
            return $result;
        }

        public static function get_controls_field_ids($fields) {
            $field_ids = array();
            if (isset($fields)) {
                foreach ($fields as $fld) {
                    if (isset($fld['merge_fields']) && $fld['merge_fields'] == false) {
                        foreach (self::get_controls_field_ids($fld['fields']) as $fld_id) {
                            $field_ids[] = $fld_id;
                        }
                    } else {
                        $field_ids[] = $fld['id'];
                    }
                }
            }
            return $field_ids;
        }

        public static function get_controls_field_types($fields, $field_args) {
            $field_types = array();
            foreach ($fields as $fld) {

                if (!isset($fld['sanitize_type'])) {
                    $fld['sanitize_type'] = $fld['type'];
                }

                if (isset($fld['merge_fields']) && $fld['merge_fields'] == false && isset($fld['fields'])) {
                    foreach (self::get_controls_field_types($fld['fields'], $field_args) as $f_type) {
                        $fld_tp = array(
                            'id' => $f_type['id'],
                            'type' => $f_type['type'],
                            'sanitize_type' => $f_type['sanitize_type'],
                            'screen' => $f_type['screen'],
                            'children' => $f_type['children']
                        );

                        if (isset($f_type['option_name'])) {
                            $fld_tp['option_name'] = $f_type['option_name'];
                        }

                        if (isset($f_type['post_id'])) {
                            $fld_tp['post_id'] = $f_type['post_id'];
                        }
                        if (isset($f_type['metabox_id'])) {
                            $fld_tp['metabox_id'] = $f_type['metabox_id'];
                        }

                        $field_types[] = $fld_tp;
                    }
                } else {
                    $fld_tp = array(
                        'id' => $fld['id'],
                        'type' => $fld['type'],
                        'sanitize_type' => $fld['sanitize_type'],
                        'screen' => $field_args['screen'],
                        'children' => apply_filters('reon/get-' . $fld['type'] . '-children-types', array(), $fld, $field_args)
                    );
                    if (isset($field_args['option_name'])) {
                        $fld_tp['option_name'] = $field_args['option_name'];
                    }

                    if (isset($field_args['post_id'])) {
                        $fld_tp['post_id'] = $field_args['post_id'];
                    }
                    if (isset($field_args['metabox_id'])) {
                        $fld_tp['metabox_id'] = $field_args['metabox_id'];
                    }
                    $field_types[] = $fld_tp;
                }
            }
            return $field_types;
        }

        public static function recursive_require( $dir, $ingore_list = array(), $subdirs = array() ) {

            if ( $dir_handle = opendir( $dir ) ) {
                while ( false !== ($file_path = readdir( $dir_handle )) ) {

                    if ( in_array( $file_path, $subdirs ) ) {
                        self::recursive_require( $dir . '/' . $file_path, $ingore_list, $subdirs );
                    } else {
                        $explode_entry = explode( '.', $file_path );
                        if ( isset( $explode_entry[ 1 ] ) && $explode_entry[ 1 ] == 'php' && !in_array( $file_path, $ingore_list ) ) {
                            require_once $dir . '/' . $file_path;
                        }
                    }
                }
                closedir( $dir_handle );
            }
        }
    }

}

