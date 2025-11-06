<?php
namespace PrimeKit\Frontend\Elementor\Widgets\ProjectProgressTrackLocked;

if ( ! defined('ABSPATH') ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use PrimeKit\Frontend\Elementor\Inc\Helpers;

class Main extends Widget_Base {

    public function get_name() {
        return 'primekitpro-project-progress-track';
    }

    public function get_title() {
        return esc_html__('Project Progress Tracker', 'primekit-addons');
    }

    public function get_icon() {
        return 'primekit-addons-icon eicon-lock';
    }

    public function get_categories() {
        return ['primekitpro-category'];
    }

    public function get_keywords() {
        return ['primekit-addons', 'project', 'progress', 'tracker', 'pro', 'locked'];
    }

    public function show_in_panel() {
        return ! Helpers::is_pro_active();
    }

    public function get_custom_help_url() {
        return Helpers::get_pro_upgrade_url();
    }

    public function get_style_depends() {
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
                'raw'  => Helpers::get_upsell_html('Project Progress Tracker'),
                'content_classes' => 'primekit-locked-note',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        echo Helpers::get_locked_box_html('Project Progress Tracker');
    }
}
