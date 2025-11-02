<?php
/**
 * Plugin Name: PrimeKit Addons and Templates
 * Plugin URI: https://primekitaddons.com/
 * Description: The Elementor Custom Widgets plugin is built to enhance your website’s look and performance. With PrimeKit Addons and Templates, you’ll get access to a Theme Builder, Pop-Ups, Cost estimation, Pricing table, Forms, and WooCommerce building features, along with stunning custom elements that blend seamlessly with your site’s design.
 * Version: 1.2.9
 * Author: Nexiby LLC
 * Author URI: https://nexiby.com/
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: primekit-addons
 * Domain Path: /languages
 * namespace: PrimeKit
 * Elementor tested up to: 3.32
 * Elementor Pro tested up to: 3.32
 * Requires Plugins: elementor

 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

final class PrimeKitAddons
{

    // Singleton instance.
    private static $instance = null;

    /**
     * Initializes the PrimeKit class by defining constants, including necessary files, and initializing hooks.
     */
    private function __construct()
    {
        $this->define_constants();
        $this->include_files();
        $this->init_hooks();
    }

    // Load plugin textdomain.
    public function register_textdomain()
    {
        load_plugin_textdomain('primekit-addons', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }


    /**
     * Retrieves the singleton instance of the plugin.
     *
     * @return PrimeKit The singleton instance of the plugin.
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Defines plugin constants.
     */
    private static $plugin_name = null;

    private function define_constants()
    {
        // Define Plugin Version.
        define('PRIMEKIT_VERSION', '1.2.9');

        // Define Plugin Path.
        define('PRIMEKIT_PATH', plugin_dir_path(__FILE__));

        // Define Plugin URL.
        define('PRIMEKIT_URL', plugin_dir_url(__FILE__));

        define('PRIMEKIT_BASENAME', plugin_basename(__FILE__));

        define('PRIMEKIT_FILE', __FILE__);

        // Define Plugin Name directly from plugin header
        define('PRIMEKIT_NAME', 'PrimeKit Addons and Templates');



    }

    /**
     * Includes necessary files.
     */
    private function include_files()
    {
        if (file_exists(PRIMEKIT_PATH . 'vendor/autoload.php')) {
            require_once PRIMEKIT_PATH . 'vendor/autoload.php';
        }
    }

    /**
     * Initializes hooks.
     */
    private function init_hooks()
    {
        add_action('plugins_loaded', array($this, 'plugin_loaded'));
        add_action('init', array($this, 'register_textdomain'));
        register_activation_hook(PRIMEKIT_PATH, array($this, 'activate'));
        register_deactivation_hook(PRIMEKIT_PATH, array($this, 'deactivate'));
    }

    /**
     * Called when the plugin is loaded.
     */
    public function plugin_loaded()
    {
        // Check if Elementor is active and loaded
        if (did_action('elementor/loaded') && class_exists('\Elementor\Plugin')) {
            if (class_exists('PrimeKit\Manager')) {
                new \PrimeKit\Manager();
            }
        } else {
            // Show admin notice if Elementor is not installed or activated
            add_action('admin_notices', function () {
                $elementor_url = admin_url('plugin-install.php?s=elementor&tab=search&type=term');
                echo '<div class="notice notice-error is-dismissible">';
                echo '<p><strong>'. PRIMEKIT_NAME .'</strong> requires <a href="' . esc_url($elementor_url) . '" target="_blank">Elementor</a> to be installed and activated.</p>';
                echo '</div>';
            });
        }
    }



    /**
     * Activates the plugin.
     */
    public function activate()
    {
        PrimeKit\Activate::activate();
    }

    /**
     * Deactivates the plugin.
     */
    public function deactivate()
    {
        PrimeKit\Deactivate::deactivate();
    }
}

/**
 * Initializes the PrimeKit plugin.
 */
if (!function_exists('primekit_addons_initialize')) {
    function primekit_addons_initialize()
    {
        return PrimeKitAddons::get_instance();
    }

    primekit_addons_initialize();
}