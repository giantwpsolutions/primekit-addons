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
        return 'eicon-accordion primekit-addons-icon eicon-lock';
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

    public function get_style_depends()
    {
        // Do NOT enqueue Pro assets from the free proxy
        return [];
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
        $message = Helpers::get_locked_message('Advanced Accordion');
        $url = Helpers::get_pro_upgrade_url();

        echo '<div class="primekit-locked-widget" style="border:1px dashed #bbb;padding:18px;border-radius:8px;text-align:center">';
        echo '<p style="font-weight:600;margin-bottom:6px;">' . wp_kses_post($message) . '</p>';
        echo '<a class="elementor-button elementor-button-success" href="' . esc_url($url) . '" target="_blank" rel="noopener">';
        echo esc_html__('Upgrade to PrimeKit Pro', 'primekit');
        echo '</a></div>';
    }
}
