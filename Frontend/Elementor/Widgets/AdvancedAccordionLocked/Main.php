<?php
namespace PrimeKit\Frontend\Elementor\Widgets\AdvancedAccordionLocked;

if (!defined('ABSPATH'))
    exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use PrimeKit\Frontend\Elementor\Inc\Helpers;



class Main extends Widget_Base
{

    public function get_name()
    {
        // IMPORTANT: same slug as Pro so pages swap seamlessly when Pro is activated
        return 'primekitpro-advanced-accordion';
    }

    public function get_title()
    {
        return esc_html__('Advanced Accordion', 'primekit-addons');
    }

    public function get_icon()
    {
        // reuse your icon + hint lock
        return 'eicon-accordion primekit-addons-icon-lock';
    }

    public function get_categories()
    {
        // Put proxies under a clear “Pro” bucket
        return ['primekitpro-category'];
    }

    public function get_keywords()
    {
        return ['primekit-addons', 'advanced', 'accordion', 'pro', 'locked'];
    }

    public function show_in_panel()
    {
        // Only show the proxy if Pro is NOT active
        return !Helpers::is_pro_active();
    }

    public function get_custom_help_url()
    {
        // ⬇ Replace with your pricing/activation URL
        return Helpers::get_pro_upgrade_url();
    }

public function get_style_depends() {
    return ['primekit-locked-styles']; // same handle used in Helpers
}


    public function get_script_depends()
    {
        // Do NOT enqueue Pro assets from the free proxy
        return [];
    }


    protected function register_controls()
    {
        $this->start_controls_section(
            'primekit_locked_section',
            ['label' => esc_html__('Available in PrimeKit Pro', 'primekit-addons')]
        );

        $upsell = Helpers::get_upsell_html('Advanced Accordion');

        $this->add_control(
            'primekit_locked_html',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => $upsell,
                'content_classes' => 'primekit-locked-note',
            ]
        );

        $this->end_controls_section();
    }


    protected function render()
    {
        // Print shared HTML
        echo Helpers::get_locked_box_html('Advanced Accordion');
    }
}
