<?php
/**
 * PrimeKit Template Class
 *
 * This class is responsible for handling the initial setup of the theme.
 *
 * @package PrimeKit
 * @subpackage PrimeKit/Admin/Inc/Templates
 * @author SupreoX Limited
 * @since 1.0.5
 */
namespace PrimeKit\Admin\Inc\Templates;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

use PrimeKit\Admin\Inc\Templates\Assets\Assets;
use PrimeKit\Admin\Inc\Templates\Markup\Modal;
use PrimeKit\Admin\Inc\Templates\Library_Source;
use PrimeKit\Admin\Inc\Templates\Library_Manager;

/**
 * Class Template
 *
 * The Template class is responsible for handling the initial setup of the theme within the PrimeKit package.
 * It performs necessary actions during the admin initialization process to ensure the theme setup is executed correctly.
 *
 * @package PrimeKit\Admin\Inc\Templates
 * @since 1.0.5
 */

class Templates
{

    protected $assets;
    protected $modal;
    protected $markup;
    protected $Library_Source;
    protected $Library_Manager;

    /**
     * Initializes the PrimeKit Template class.
     *
     * This function sets up the PrimeKit Template by setting constants and initializing the classes used by the PrimeKit Template.
     *
     * @since 1.0.5
     */
    public function __construct()
    {
        $this->setConstants(); // Set the constants.
        $this->init_classes(); // Initialize the classes.

        // Register template source with Elementor
        add_action('elementor/init', [$this, 'register_template_source']);
    }


    /**
     * Initializes the classes used by the PrimeKit Template.
     *
     * This function sets up the Assets class, which handles the initialization and configuration of the PrimeKit Template assets.
     *
     * @since 1.0.5
     */
    public function init_classes()
    {
        $this->assets = new Assets();
        $this->markup = new Modal();
        $this->Library_Source = new Library_Source();
        $this->Library_Manager = new Library_Manager();
    }

    /**
     * Register PrimeKit template source with Elementor
     *
     * @since 1.2.12
     */
    public function register_template_source()
    {
        // Get Elementor's template library manager
        $library_manager = \Elementor\Plugin::instance()->templates_manager;

        // Register our custom source
        $library_manager->register_source(Library_Source::class);
    }


    /**
     * Sets the constants for the PrimeKit Template.
     *
     * Defines the URL path for the PrimeKit Template assets directory.
     *
     * @since 1.0.5
     */
    public function setConstants()
    {
        define('PRIMEKIT_TEMPLATE_ASSETS', plugin_dir_url(__FILE__) . 'Assets');
        define('PRIMEKIT_TEMPLATE_PATH', plugin_dir_path(__FILE__));
    }


    public function primekit_get_templates()
    {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }

        $templates = [
            [
                'id' => 1,
                'title' => 'Template 1',
                'thumbnail' => PRIMEKIT_TEMPLATE_ASSETS . '/img/template1.jpg',
            ],
            [
                'id' => 2,
                'title' => 'Template 2',
                'thumbnail' => PRIMEKIT_TEMPLATE_ASSETS . '/img/template2.jpg',
            ],
        ];

        wp_send_json_success($templates);
    }





}
