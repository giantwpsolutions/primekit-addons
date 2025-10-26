<?php 
/**
 * PublicManager.php
 *
 * This file contains the PublicManager class, which is responsible for handling the
 * initialization and configuration of the PrimeKit Public.
 * It ensures the proper setup of the required configurations and functionalities
 * for the PrimeKit Public.
 *
 * @package PrimeKit\Public
 * @since 1.0.0
 */
namespace PrimeKit\Frontend;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


use PrimeKit\Frontend\Elementor\Configuration;

/**
 * Class PublicManager
 *
 * Handles the initialization and configuration of the PrimeKit Public.
 * It ensures the proper setup of the required configurations and functionalities
 * for the PrimeKit Public.
 *
 * @package PrimeKit\Public
 * @since 1.0.0
 */
 class Frontend {

    protected $Elementor_Config;

    /**
     * PublicManager constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->initialize();
    }

    /**
     * Initialize the PrimeKit Public
     *
     * @since 1.0.0
     */
    public function initialize() {
        $this->Elementor_Config = Configuration::instance();
    
    }
 }