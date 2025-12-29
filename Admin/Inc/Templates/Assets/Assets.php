<?php
/**
 * Assets.php
 *
 * This file contains the Assets class, which handles the initialization and configuration of the PrimeKit Admin Assets.
 * It ensures the proper loading of required assets such as CSS and JavaScript files for the PrimeKit Admin plugin.
 *
 * @package PrimeKit\Admin\Inc\Templates\Assets
 * @since 1.0.5
 */
namespace PrimeKit\Admin\Inc\Templates\Assets;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


/**
 * Handles the initialization and configuration of the PrimeKit Admin Assets.
 * This class ensures the proper loading of required assets such as CSS and JavaScript files.
 *
 * @package PrimeKit\Admin\Inc\Templates\Assets
 * @since 1.0.5
 */
class Assets
{


    /**
     * Constructor for the Assets class.
     *
     * Sets up the necessary actions to enqueue scripts and styles 
     * for the Elementor editor within the PrimeKit Admin.
     *
     * @since 1.0.5
     */

    public function __construct()
    {
        add_action('elementor/editor/after_enqueue_scripts', array($this, 'template_editor_scripts'));
        add_action('elementor/editor/after_enqueue_scripts', array($this, 'template_editor_styles'));
    }


    /**
     * Enqueues JavaScript script for the Elementor editor.
     *
     * This function enqueues the JavaScript file necessary for adding custom 
     * button functionality in the Elementor editor for the PrimeKit Admin.
     *
     * @since 1.0.5
     */

    public function template_editor_scripts()
    {
        wp_enqueue_script(
            'primekit-namespace',
            PRIMEKIT_TEMPLATE_ASSETS . '/js/namespace.js',
            ['jquery'],
            PRIMEKIT_VERSION,
            true
        );

        wp_enqueue_script(
            'primekit-templates',
            PRIMEKIT_TEMPLATE_ASSETS . '/js/templates.js',
            ['jquery', 'primekit-namespace'],
            PRIMEKIT_VERSION,
            true
        );

        wp_enqueue_script(
            'primekit-template-categories',
            PRIMEKIT_TEMPLATE_ASSETS . '/js/TemplateCategories.js',
            ['jquery', 'primekit-namespace'],
            PRIMEKIT_VERSION,
            true
        );
    }

    /**
     * Enqueues CSS stylesheet for the Elementor editor.
     *
     * This function enqueues the CSS file necessary for adding custom
     * button styles in the Elementor editor for the PrimeKit Admin.
     *
     * @since 1.0.5
     */
    public function template_editor_styles()
    {
        wp_enqueue_style(
            'primekit-template-modal',
            PRIMEKIT_TEMPLATE_ASSETS . '/css/elementor-template-btn.css',
            [],
            PRIMEKIT_VERSION
        );
    }
}
