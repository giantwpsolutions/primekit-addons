<?php
namespace PrimeKit\Frontend\Elementor\Widgets\AdvancedPricingTableLocked;

if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use PrimeKit\Frontend\Elementor\Inc\Helpers;

class Main extends Widget_Base {

    public function get_name() {
        // Same slug as Pro so it swaps seamlessly when Pro is active
        return 'primekitpro-advanced-pricing-table';
    }

    public function get_title() {
        return esc_html__('Advanced Pricing Table', 'primekit-addons');
    }

    public function get_icon() {
        // Your icon + lock hint
        return 'eicon-price-table primekit-addons-icon-lock';
    }

    public function get_categories() {
        return ['primekitpro-category'];
    }

    public function get_keywords() {
        return ['primekit-addons', 'advanced', 'pricing', 'table', 'pro', 'locked'];
    }

    public function show_in_panel() {
        // Only show the proxy when Pro is NOT active
        return ! Helpers::is_pro_active();
    }

    public function get_custom_help_url() {
        return Helpers::get_pro_upgrade_url();
    }

    public function get_style_depends() {
        // Use the centralized inline CSS from Helpers
        return ['primekit-locked-styles'];
    }

    public function get_script_depends() {
        // No Pro scripts in the proxy
        return [];
    }

    protected function register_controls() {
        // Simple upsell inside the Elementor sidebar
        $this->start_controls_section(
            'primekit_locked_section',
            ['label' => esc_html__('Available in PrimeKit Pro', 'primekit-addons')]
        );

        $this->add_control(
            'primekit_locked_html',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => Helpers::get_upsell_html('Advanced Pricing Table'),
                'content_classes' => 'primekit-locked-note',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Frontend/editor canvas: show the locked box
        echo Helpers::get_locked_box_html('Advanced Pricing Table');
    }
}
