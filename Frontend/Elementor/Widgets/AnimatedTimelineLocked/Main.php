<?php
// File: primekit-free/elementor/widgets/AnimatedTimelineLocked/Main.php
namespace PrimeKit\Frontend\Elementor\Widgets\AnimatedTimelineLocked;

if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use PrimeKit\Frontend\Elementor\Inc\Helpers;

class Main extends Widget_Base {

    public function get_name() {
        // EXACT same slug as Pro
        return 'primekitpro-animated-timeline';
    }

    public function get_title() {
        return esc_html__('Animated Timeline', 'primekit-addons');
    }

    public function get_icon() {
        // your icon + lock hint
        return 'eicon-time-line primekit-addons-icon eicon-lock';
    }

    public function get_categories() {
        return ['primekitpro-category'];
    }

    public function get_keywords() {
        return ['primekit-addons', 'animated', 'timeline', 'company', 'history', 'pro', 'locked'];
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
        // Upsell block in the sidebar
        $this->start_controls_section(
            'primekit_locked_section',
            ['label' => esc_html__('Available in PrimeKit Pro', 'primekit-addons')]
        );

        $this->add_control(
            'primekit_locked_html',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => Helpers::get_upsell_html('Animated Timeline'),
                'content_classes' => 'primekit-locked-note',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Canvas message when dropped on the page
        echo Helpers::get_locked_box_html('Animated Timeline');
    }
}
