<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ReonOptionPageWC')) {

    class ReonOptionPageWC {

        private static $wc_option_page;
        private static $sub_menu;

        public static function init() {
            self::$wc_option_page = array();
            self::$sub_menu = array();
            add_filter('woocommerce_settings_tabs_array', array(new self(), 'init_menu_tabs'), 99);
            add_action("admin_menu", array(new self(), 'init_menu_page'));

            add_action("admin_menu", array(new self(), 'init_sub_menu'));
            add_action('woocommerce_admin_settings_sanitize_option', array(new self(), 'sanitize_page_field'), 200, 3);

            add_action('woocommerce_admin_field_rn-page', array(new self(), 'render_wc_page_field'));
            add_action('woocommerce_admin_field_rn-section', array(new self(), 'render_wc_section_field'));
            add_action('woocommerce_admin_field_rn-sectionend', array(new self(), 'render_wc_sectionend_field'));
        }

        public static function init_menu_tabs($settings_tabs) {
            foreach (self::get_wc_pages() as $page) {
                if (isset($page['wc_menu']['enable']) && $page['wc_menu']['enable'] == true) {
                    if (isset($page['wc_menu']['parent']) && $page['wc_menu']['parent'] == '') {
                        $settings_tabs[$page['wc_menu']['slug']] = $page['wc_menu']['title'];
                    }
                }
            }
            return $settings_tabs;
        }

        public static function init_menu_page() {
            foreach (self::get_wc_pages() as $page) {
                if (isset($page['wc_menu']['enable']) && $page['wc_menu']['enable'] == true) {
                    if (isset($page['wc_menu']['parent']) && $page['wc_menu']['parent'] == '') {
                        add_action('woocommerce_settings_tabs_' . $page['wc_menu']['slug'], array(new self(), 'show_wc_menu_page'));
                        add_action('woocommerce_update_options_' . $page['wc_menu']['slug'], array(new self(), 'update_wc_menu_page'));
                    }
                }
            }
        }

        public static function show_wc_menu_page() {

            if (isset($_GET['page']) && sanitize_key($_GET['page']) == 'wc-settings') {

                if (isset($_GET['tab'])) {
                    $wc_tab = sanitize_key($_GET['tab']);
                    foreach (self::get_wc_pages() as $page) {
                        if (isset($page['wc_menu']['enable']) && $page['wc_menu']['enable'] == true) {
                            if (isset($page['wc_menu']['parent']) && $page['wc_menu']['parent'] == '' && $page['wc_menu']['slug'] = $wc_tab) {
                                woocommerce_admin_fields($page['wc_fields']);
                            }
                        }
                    }
                }
            }
        }

        public static function update_wc_menu_page() {
            if (isset($_GET['page']) && sanitize_key($_GET['page']) == 'wc-settings') {

                if (isset($_GET['tab'])) {
                    $wc_tab = sanitize_key($_GET['tab']);
                    foreach (self::get_wc_pages() as $page) {
                        if (isset($page['wc_menu']['enable']) && $page['wc_menu']['enable'] == true) {
                            if (isset($page['wc_menu']['parent']) && $page['wc_menu']['parent'] == '' && $page['wc_menu']['slug'] = $wc_tab) {
                                woocommerce_update_options($page['wc_fields']);
                            }
                        }
                    }
                }
            }
        }

        public static function sanitize_page_field($value, $wc_field, $raw_value) {
            if ($wc_field['type'] == 'rn-page') {
                return null;
            }
            return $value;
        }

        public static function init_sub_menu() {
            foreach (self::get_wc_pages() as $page) {
                if (isset($page['wc_menu']['enable']) && $page['wc_menu']['enable'] == true) {
                    if (isset($page['wc_menu']['parent']) && $page['wc_menu']['parent'] != '') {
                        self::$sub_menu[$page['wc_menu']['parent']] = $page['wc_menu']['parent'];
                    }
                }
            }

            foreach (self::$sub_menu as $s_menu) {
                add_filter('woocommerce_get_sections_' . $s_menu, array(new self(), 'add_sub_section_menu'));
                add_filter('woocommerce_get_settings_' . $s_menu, array(new self(), 'add_sub_section'), 10, 2);
            }
        }

        public static function add_sub_section_menu($sections) {

            foreach (self::get_wc_pages() as $page) {
                if (!isset($page['wc_menu']['enable']) || $page['wc_menu']['enable'] == false) {
                    continue;
                }
                if (!isset($page['wc_menu']['parent']) && $page['wc_menu']['parent'] == '') {
                    continue;
                }
                if (!isset($page['wc_menu']['slug']) && $page['wc_menu']['slug'] == '') {
                    continue;
                }
                if (!isset(self::$sub_menu[$page['wc_menu']['parent']])) {
                    continue;
                }
                $sections[$page['wc_menu']['slug']] = $page['wc_menu']['title'];
            }

            return $sections;
        }

        public static function add_sub_section($settings, $current_section) {
            foreach (self::get_wc_pages() as $page) {
                if (!isset($page['wc_menu']['enable']) || $page['wc_menu']['enable'] == false) {
                    continue;
                }
                if (!isset($page['wc_menu']['parent']) && $page['wc_menu']['parent'] == '') {
                    continue;
                }
                if (!isset($page['wc_menu']['slug']) && $page['wc_menu']['slug'] == '') {
                    continue;
                }
                if (!isset(self::$sub_menu[$page['wc_menu']['parent']])) {
                    continue;
                }
                if ($current_section == $page['wc_menu']['slug']) {

                    return $page['wc_fields'];
                }
            }

            return $settings;
        }

        public static function render_wc_page_field($page_field) {
            if (!isset($page_field['desc_tip'])) {
                $page_field['desc_tip'] = false;
            }
            $description = WC_Admin_Settings::get_field_description($page_field);

            if (!isset($page_field['id'])) {
                $page_field['id'] = '';
            }

            if (!isset($page_field['title'])) {
                $page_field['title'] = '';
            }


            if (!isset($page_field['description'])) {
                $page_field['description'] = '';
            }

            $page = ReonOptionPage::get_page($page_field['option_name'], true, true);
            $page['is_wc'] = true;
                        
            $tabs_key = $page['tabs_key'];
            
            if (isset($_GET[$tabs_key])) {
                $page['tab'] = sanitize_key($_GET[$tabs_key]);
            }
            
            $page['group'] = self::get_active_group_by_tab($page['sections'], $page['tab']);

            if (isset($page_field['instance_id'])) {
                $page['instance_id'] = $page_field['instance_id'];
            }

            $cols = 1;
            if ((isset($page_field['title']) && $page_field['title'] != '') || $description['description'] != '' || $description['tooltip_html'] != '') {
                $cols = 2;
            }

            $css_class = isset($page_field['css_class']) ? $page_field['css_class'] : array();

            $css_class[] = 'forminp';
            $css_class[] = 'forminp-' . $page_field['type'];

            $attributes = isset($page_field['attributes']) ? $page_field['attributes'] : array();
            $attributes['colspan'] = 2;
            $attributes['class'] = ReonUtil::array_to_classes($css_class);
            ?><tr valign="top">
                <?php
                if ($cols == 1) {
                    ?>
                    <td <?php echo ReonUtil::array_to_attributes($attributes); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                        <?php
                        ReonOptionPage::render_page($page);
                        ?>
                    </td>
                    <?php
                } else {
                    ?>
                    <th scope="row" class="titledesc">
                        <label for="<?php echo esc_attr($page_field['id']); ?>"><?php echo esc_html($page_field['title']); ?></label>
                        <?php echo wp_kses_post($description['tooltip_html']); ?>
                        &lrm; <p class="rn_wc_desc"><?php echo wp_kses_post($page_field['description']); ?></p>
                    </th>
                    <td class="forminp forminp-<?php echo esc_attr(sanitize_title($page_field['type'])); ?>">
                        <?php
                        ReonOptionPage::render_page($page);
                        ?>
                    </td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }

        public static function render_wc_section_field($field) {
            echo '<table class="form-table">' . "\n\n";
        }

        public static function render_wc_sectionend_field($field) {
            echo '</table>';
        }

        public static function get_wc_pages() {
            $pages = array();
            foreach (self::$wc_option_page as $page) {
                $pages[] = $page;
            }
            return $pages;
        }

        public static function set_page($args = array()) {
            $page = self::process_wc_optionpage($args);
            self::$wc_option_page[] = $page;

            ReonOptionPage::set_page(self::process_optionpage($args));
        }

        public static function set_section($option_name = '', $args = array()) {
            ReonOptionPage::set_section($option_name, $args);
        }

        private static function process_optionpage($args) {
            $page = array();
            foreach (self::default_optionpage($args['option_name']) as $key => $option) {
                if (!isset($page[$key])) {
                    $page[$key] = $option;
                }
            }

            foreach ($args as $key => $value) {
                if ($key != 'wc_menu' && $key != 'wc_fields') {
                    $page[$key] = $value;
                }
            }


            return $page;
        }

        private static function process_wc_optionpage($args) {
            $page = array();

            if (isset($args['option_name'])) {
                $page['option_name'] = $args['option_name'];
            }

            if (isset($args['wc_menu'])) {
                $page['wc_menu'] = $args['wc_menu'];
            }

            if (isset($args['wc_fields'])) {
                $page['wc_fields'] = $args['wc_fields'];
            }

            $page = apply_filters('reon/process-wc-option-page-' . $page['option_name'], $page);


            foreach (self::default_optionpage($args['option_name']) as $key => $option) {
                if (!isset($page[$key])) {
                    $page[$key] = $option;
                }
            }
            return $page;
        }
        
        private static function get_active_group_by_tab($sections, $tab) {
            $cnt = 1;

            foreach ($sections as $section) {
                if ($cnt == $tab) {
                    return $section['group'];
                }
                $cnt++;
            }
        }
        
        private static function default_optionpage($option_name) {
            $args = array(
                'group' => 0,
                'instance_key' => 'instance_id',
                'menu' => array(
                    'enable' => false,
                ),
                'admin_bar' => array(
                    'enable' => false,
                ),
                'import_export' => array(
                    'enable' => false,
                ),
                'wc_menu' => array(
                    'enable' => true,
                    'parent' => '',
                    'slug' => 'reon',
                    'title' => 'Reon Framework',
                ),
                'wc_fields' => array(
                    'rn_page' => array(
                        'type' => 'rn-page',
                        'page_name' => $option_name,
                    ),
                ),
            );
            return $args;
        }

    }

}