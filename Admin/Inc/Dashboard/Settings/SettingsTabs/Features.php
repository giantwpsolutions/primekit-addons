<?php

namespace PrimeKit\Admin\Inc\Dashboard\Settings\SettingsTabs;

if (!defined('ABSPATH')) exit;
/**
 * Class Features
 *
 * Manages the "Features" tab settings within the PrimeKit admin panel.
 * This includes feature toggles such as Theme Builder and Template Importer,
 * along with option management utilities like get, set, and defaults.
 *
 * @package PrimeKit\Admin
 */
class Features {
    /**
     * Option key used to store feature settings.
     *
     * @var string
     */
    protected $option_key = 'primekit_features_options';

    /**
     * Constructor.
     * Registers the settings during admin_init.
     */
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }
   /**
     * Retrieves all feature options with defaults merged in.
     *
     * @return array Merged options with defaults.
     */
    public function get_all() {
        return wp_parse_args(get_option($this->option_key, []), $this->defaults());
    }

    /**
     * Retrieves a specific feature option with optional fallback.
     *
     * @param string $name     The option name.
     * @param mixed  $fallback Optional fallback value.
     * @return mixed
     */
    public function get($name, $fallback = null) {
        $options = $this->get_all();
        return $options[$name] ?? $fallback;
    }

    /**
     * Sets or updates a single feature option.
     *
     * @param string $name  The option name.
     * @param mixed  $value The value to set.
     * @return bool True on success, false on failure.
     */
    public function set($name, $value) {
        if (!is_admin() || !current_user_can('manage_options')) {
            return false; // Prevent abuse from frontend or low-privilege users
        }    
        $options = $this->get_all();
        $options[$name] = $value === '1' ? 1 : 0;
        return update_option($this->option_key, $options);
    }
    

    /**
     * Defines default values for all available feature toggles.
     *
     * @return array Default feature settings.
     */
    public function defaults() {
        return [
            'enable_themebuilder' => 1,
            'enable_editor_template_import' => 1,
        ];
    }
    /**
     * Registers settings, sections, and fields for the Features tab.
     */
    public function register_settings() {
        register_setting(
            'primekit_features_options',
            'primekit_features_options',
            [$this, 'sanitize']
        );

        add_settings_section(
            'features_settings_section',
            esc_html__('Features Settings', 'primekit-addons'),
            [$this, 'section_info'],
            'primekit_features_settings'
        );

        // Template Importer inside Elementor
        add_settings_field(
            'enable_editor_template_import',
            esc_html__('Template Importer', 'primekit-addons'),
            [$this, 'render_editor_checkbox'],
            'primekit_features_settings',
            'features_settings_section'
        );

        // Theme Builder interface
        add_settings_field(
            'enable_themebuilder',
            esc_html__('Theme Builder', 'primekit-addons'),
            [$this, 'render_themebuilder_checkbox'],
            'primekit_features_settings',
            'features_settings_section'
        );
    }

     /**
     * Renders description text for the features settings section.
     */
    public function section_info() {
        echo '<p>' . esc_html__('Enable or disable optional feature modules for your Elementor-based site.', 'primekit-addons') . '</p>';
    }

    /**
     * Renders the checkbox field for the Template Importer setting.
     */
    public function render_editor_checkbox() {
        $options = get_option('primekit_features_options', []);
        $enabled = isset($options['enable_editor_template_import']) ? (bool)$options['enable_editor_template_import'] : true;
        ?>
        <label>
            <input type="checkbox"
                   name="primekit_features_options[enable_editor_template_import]"
                   value="1"
                   <?php checked($enabled); ?>>
            <?php esc_html_e('Allow importing ready-made templates directly within Elementor editor.', 'primekit-addons'); ?>
        </label>
        <p class="description"><?php esc_html_e('Lets users browse and import professionally designed templates from your server directly inside Elementor editor.', 'primekit-addons'); ?></p>
        <?php
    }

    /**
     * Renders the checkbox field for the Theme Builder setting.
     */
    public function render_themebuilder_checkbox() {
        $options = get_option('primekit_features_options', []);
        $enabled = isset($options['enable_themebuilder']) ? (bool)$options['enable_themebuilder'] : true;
        ?>
        <label>
            <input type="checkbox"
                   name="primekit_features_options[enable_themebuilder]"
                   value="1"
                   <?php checked($enabled); ?>>
            <?php esc_html_e('Enable advanced theme building.', 'primekit-addons'); ?>
        </label>
        <p class="description"><?php esc_html_e('Allows custom header, footer, single page/post, archive, and WooCommerce layout design using Elementor.', 'primekit-addons'); ?></p>
        <?php
    }

    /**
     * Sanitizes and validates submitted feature options.
     * Also includes nonce verification for extra security.
     *
     * @param array $input The raw submitted options.
     * @return array Sanitized options.
     */
    public function sanitize($input) {
        // Security check
        if (!isset($_POST['primekit_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['primekit_nonce'])), 'primekit_save_settings')) {
            add_settings_error(
                'primekit_features_settings',
                'primekit_nonce_error',
                esc_html__('Security check failed. Settings were not saved.', 'primekit-addons'),
                'error'
            );
            return get_option('primekit_features_options', []);
        }

        $new_input = [];

        $new_input['enable_editor_template_import'] = isset($input['enable_editor_template_import']) ? 1 : 0;
        $new_input['enable_themebuilder'] = isset($input['enable_themebuilder']) ? 1 : 0;

        add_settings_error(
            'primekit_features_settings',
            'primekit_features_success',
            esc_html__('Features settings saved successfully.', 'primekit-addons'),
            'updated'
        );

        return $new_input;
    }
}