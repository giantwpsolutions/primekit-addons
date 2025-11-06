<?php
namespace PrimeKit\Frontend\Elementor\Widgets\LottieLocked;

if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use PrimeKit\Frontend\Elementor\Inc\Helpers;

class Main extends Widget_Base {

    public function get_name() {
        // Match Pro slug so the real widget takes over when Pro is active
        return 'primekitpro-lottie';
    }

    public function get_title() {
        return esc_html__('Lottie', 'primekit-addons');
    }

    public function get_icon() {
        // subtle lock hint in the panel
        return 'eicon-animation primekit-addons-icon-lock';
    }

    public function get_categories() {
        return ['primekitpro-category'];
    }

    public function get_keywords() {
        return ['primekit-addons', 'lottie', 'animation', 'json', 'gif', 'interactive', 'locked'];
    }

    public function show_in_panel() {
        // Only expose this proxy if Pro is NOT active
        return ! Helpers::is_pro_active();
    }

    public function get_custom_help_url() {
        return Helpers::get_pro_upgrade_url();
    }

    public function get_style_depends() {
        // shared minimal styles for the locked card
        return ['primekit-locked-styles'];
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
                'raw'  => Helpers::get_upsell_html('Lottie'),
                'content_classes' => 'primekit-locked-note',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        echo Helpers::get_locked_box_html('Lottie');
    }
}