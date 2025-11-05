<?php
// File: primekit-free/elementor/widgets/AdvancedTabLocked/Main.php
namespace PrimeKit\Frontend\Elementor\Widgets\AdvancedTabLocked;

if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use PrimeKit\Frontend\Elementor\Inc\Helpers;

class Main extends Widget_Base {

    public function get_name() {
        // EXACT same slug as Pro so it swaps seamlessly
        return 'primekitpro-advanced-tab';
    }

    public function get_title() {
        return esc_html__('Advanced Tabs', 'primekit-addons');
    }

    public function get_icon() {
        // your icon + lock hint
        return 'eicon-tabs primekit-addons-icon';
    }

    public function get_categories() {
        return ['primekitpro-category'];
    }

    public function get_keywords() {
        return ['primekit-addons', 'advanced', 'tab', 'tabs', 'pro', 'locked'];
    }

    public function show_in_panel() {
        // Only show proxy when Pro is NOT active
        return ! Helpers::is_pro_active();
    }

    public function get_custom_help_url() {
        return Helpers::get_pro_upgrade_url(); // https://primekitaddons.com/pro/
    }

    public function get_style_depends() {
        // shared inline CSS handle from Helpers
        return ['primekit-locked-styles'];
    }

    public function get_script_depends() {
        return [];
    }

    protected function register_controls() {
        // Simple upsell inside Elementor’s controls panel
        $this->start_controls_section(
            'primekit_locked_section',
            ['label' => esc_html__('Available in PrimeKit Pro', 'primekit-addons')]
        );

        $this->add_control(
            'primekit_locked_html',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => Helpers::get_upsell_html('Advanced Tabs'),
                'content_classes' => 'primekit-locked-note',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Canvas output when user drops the locked widget
        echo Helpers::get_locked_box_html('Advanced Tabs');
    }
}
