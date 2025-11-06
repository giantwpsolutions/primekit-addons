<?php
namespace PrimeKit\Frontend\Elementor\Widgets\ResourceFormLocked;

if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use PrimeKit\Frontend\Elementor\Inc\Helpers;

class Main extends Widget_Base {

    public function get_name() {
        // EXACT same slug as Pro so it swaps seamlessly
        return 'primekitpro_resource_form';
    }

    public function get_title() {
        return esc_html__('Resource Form', 'primekit-addons');
    }

    public function get_icon() {
        // original icon + lock hint
        return 'eicon-site-search primekit-addons-icon-lock';
    }

    public function get_categories() {
        return ['primekitpro-category'];
    }

    public function get_keywords() {
        return ['primekit-addons', 'resource', 'form', 'search', 'download', 'pro', 'locked'];
    }

    public function show_in_panel() {
        // Only show this proxy when Pro is NOT active
        return ! Helpers::is_pro_active();
    }

    public function get_custom_help_url() {
        return Helpers::get_pro_upgrade_url(); // e.g. https://primekitaddons.com/pro/
    }

    public function get_style_depends() {
        // Shared inline CSS handle for locked UI
        return ['primekit-locked-styles'];
    }

    public function get_script_depends() {
        return [];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'primekit_locked_section',
            ['label' => esc_html__('Available in PrimeKit Pro', 'primekit-addons')]
        );

        $this->add_control(
            'primekit_locked_html',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => Helpers::get_upsell_html('Resource Form'),
                'content_classes' => 'primekit-locked-note',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        echo Helpers::get_locked_box_html('Resource Form');
    }
}
