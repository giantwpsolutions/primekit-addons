<?php
namespace PrimeKit\Frontend\Elementor\Widgets\PageList;

if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

class Main extends Widget_Base
{
    public function get_name()
    {
        return 'primekit-page-list';
    }

    public function get_title()
    {
        return esc_html__('Page List', 'primekit-addons');
    }

    public function get_icon()
    {
        return 'eicon-post-list primekit-addons-icon';
    }

    public function get_categories()
    {
        return ['primekit-category'];
    }

    public function get_keywords()
    {
        return ['prime', 'page', 'list', 'pages', 'navigation', 'menu'];
    }

    protected function register_controls()
    {
        // ── List (Repeater) ──────────────────────────────────────────────
        $this->start_controls_section(
            'primekit_page_list_section',
            [
                'label' => esc_html__('List', 'primekit-addons'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'text',
            [
                'label'       => esc_html__('Text', 'primekit-addons'),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__('List Title', 'primekit-addons'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'sub_title',
            [
                'label'       => esc_html__('Sub Title', 'primekit-addons'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => '',
                'placeholder' => esc_html__('Type your subtitle here', 'primekit-addons'),
                'rows'        => 2,
            ]
        );

        $repeater->add_control(
            'bg_type',
            [
                'label'   => esc_html__('Background Type', 'primekit-addons'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'solid'    => ['title' => esc_html__('Solid', 'primekit-addons'),    'icon' => 'eicon-paint-bucket'],
                    'gradient' => ['title' => esc_html__('Gradient', 'primekit-addons'), 'icon' => 'eicon-barcode'],
                ],
                'default' => 'solid',
                'toggle'  => false,
            ]
        );

        $repeater->add_control(
            'bg_color',
            [
                'label'     => esc_html__('Background Color', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'condition' => ['bg_type' => 'solid'],
            ]
        );

        $repeater->add_control(
            'bg_gradient_from',
            [
                'label'     => esc_html__('Gradient Start', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6c63ff',
                'condition' => ['bg_type' => 'gradient'],
            ]
        );

        $repeater->add_control(
            'bg_gradient_to',
            [
                'label'     => esc_html__('Gradient End', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#c836f8',
                'condition' => ['bg_type' => 'gradient'],
            ]
        );

        $repeater->add_control(
            'show_icon',
            [
                'label'        => esc_html__('Show Icon', 'primekit-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'primekit-addons'),
                'label_off'    => esc_html__('Hide', 'primekit-addons'),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $repeater->add_control(
            'selected_icon',
            [
                'label'     => esc_html__('Icon', 'primekit-addons'),
                'type'      => Controls_Manager::ICONS,
                'default'   => ['value' => '', 'library' => ''],
                'condition' => ['show_icon' => 'yes'],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label'         => esc_html__('Link', 'primekit-addons'),
                'type'          => Controls_Manager::URL,
                'placeholder'   => 'https://your-link.com',
                'show_external' => true,
                'default'       => ['url' => ''],
            ]
        );

        $repeater->add_control(
            'show_label',
            [
                'label'        => esc_html__('Show Label', 'primekit-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__('Show', 'primekit-addons'),
                'label_off'    => esc_html__('Hide', 'primekit-addons'),
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $repeater->add_control(
            'label_text',
            [
                'label'     => esc_html__('Label Text', 'primekit-addons'),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__('New', 'primekit-addons'),
                'condition' => ['show_label' => 'yes'],
            ]
        );

        $repeater->add_control(
            'label_color',
            [
                'label'     => esc_html__('Label Color', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'condition' => ['show_label' => 'yes'],
            ]
        );

        $repeater->add_control(
            'label_bg',
            [
                'label'     => esc_html__('Label Background', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6c63ff',
                'condition' => ['show_label' => 'yes'],
            ]
        );

        $this->add_control(
            'primekit_page_list_items',
            [
                'label'       => esc_html__('Items', 'primekit-addons'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    ['text' => esc_html__('Page One', 'primekit-addons')],
                    ['text' => esc_html__('Page Two', 'primekit-addons')],
                    ['text' => esc_html__('Page Three', 'primekit-addons')],
                ],
                'title_field' => '{{{ text }}}',
            ]
        );

        $this->end_controls_section();

        // ── Settings ──────────────────────────────────────────────────────
        $this->start_controls_section(
            'primekit_page_list_settings',
            [
                'label' => esc_html__('Settings', 'primekit-addons'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'primekit_page_list_heading',
            [
                'label'       => esc_html__('List Heading', 'primekit-addons'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '',
                'placeholder' => esc_html__('e.g. Products', 'primekit-addons'),
                'label_block' => true,
                'dynamic'     => ['active' => true],
            ]
        );

        $this->add_control(
            'primekit_page_list_heading_tag',
            [
                'label'     => esc_html__('Heading Tag', 'primekit-addons'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'h3',
                'options'   => [
                    'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4',
                    'h5' => 'H5', 'h6' => 'H6', 'p'  => 'P',
                ],
                'condition' => ['primekit_page_list_heading!' => ''],
            ]
        );

        $this->add_control(
            'primekit_page_list_layout',
            [
                'label'   => esc_html__('Layout', 'primekit-addons'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'list' => ['title' => esc_html__('List', 'primekit-addons'),  'icon' => 'eicon-editor-list-ul'],
                    'grid' => ['title' => esc_html__('Grid', 'primekit-addons'),  'icon' => 'eicon-gallery-grid'],
                ],
                'default' => 'list',
                'toggle'  => false,
            ]
        );

        $this->add_responsive_control(
            'primekit_page_list_columns',
            [
                'label'          => esc_html__('Columns', 'primekit-addons'),
                'type'           => Controls_Manager::SELECT,
                'default'        => '2',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options'        => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'selectors' => [
                    '{{WRAPPER}} .primekit-page-list-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
                'condition' => ['primekit_page_list_layout' => 'grid'],
            ]
        );

        // PrimeKit Notice
        $this->add_control(
            'primekit_elementor_addons_notice',
            [
                'type'        => Controls_Manager::NOTICE,
                'notice_type' => 'warning',
                'dismissible' => false,
                'heading'     => esc_html__('Created by PrimeKit', 'primekit-addons'),
                'content'     => esc_html__('This amazing widget is built with PrimeKit Addons, making it super easy to create beautiful and functional designs.', 'primekit-addons'),
            ]
        );

        $this->end_controls_section();

        // ── Item Style ────────────────────────────────────────────────────
        $this->start_controls_section(
            'primekit_page_list_card_style',
            [
                'label' => esc_html__('Item Style', 'primekit-addons'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'primekit_page_list_gap',
            [
                'label'      => esc_html__('Vertical Spacing', 'primekit-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => ['px' => ['min' => 0, 'max' => 80]],
                'default'    => ['unit' => 'px', 'size' => 10],
                'selectors'  => [
                    '{{WRAPPER}} .primekit-page-list-wrap' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'primekit_page_list_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'primekit-addons'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default'    => ['top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px', 'isLinked' => true],
                'selectors'  => [
                    '{{WRAPPER}} .primekit-page-list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'primekit_page_list_shadow',
                'selector' => '{{WRAPPER}} .primekit-page-list-item',
            ]
        );

        $this->end_controls_section();

        // ── Heading Style ─────────────────────────────────────────────────
        $this->start_controls_section(
            'primekit_page_list_heading_style',
            [
                'label'     => esc_html__('Heading Style', 'primekit-addons'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => ['primekit_page_list_heading!' => ''],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'primekit_page_list_heading_typo',
                'selector' => '{{WRAPPER}} .primekit-page-list-heading',
            ]
        );

        $this->add_control(
            'primekit_page_list_heading_color',
            [
                'label'     => esc_html__('Color', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .primekit-page-list-heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'primekit_page_list_heading_spacing',
            [
                'label'      => esc_html__('Bottom Spacing', 'primekit-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => ['px' => ['min' => 0, 'max' => 60]],
                'default'    => ['unit' => 'px', 'size' => 16],
                'selectors'  => [
                    '{{WRAPPER}} .primekit-page-list-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ── Title Style ───────────────────────────────────────────────────
        $this->start_controls_section(
            'primekit_page_list_title_style',
            [
                'label' => esc_html__('Title Style', 'primekit-addons'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'primekit_page_list_title_typo',
                'selector' => '{{WRAPPER}} .primekit-page-list-title',
            ]
        );

        $this->add_control(
            'primekit_page_list_title_color',
            [
                'label'     => esc_html__('Color', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#111827',
                'selectors' => [
                    '{{WRAPPER}} .primekit-page-list-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'primekit_page_list_title_hover_color',
            [
                'label'     => esc_html__('Hover Color', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6c63ff',
                'selectors' => [
                    '{{WRAPPER}} .primekit-page-list-item:hover .primekit-page-list-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ── Subtitle Style ────────────────────────────────────────────────
        $this->start_controls_section(
            'primekit_page_list_subtitle_style',
            [
                'label' => esc_html__('Sub Title Style', 'primekit-addons'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'primekit_page_list_subtitle_typo',
                'selector' => '{{WRAPPER}} .primekit-page-list-subtitle',
            ]
        );

        $this->add_control(
            'primekit_page_list_subtitle_color',
            [
                'label'     => esc_html__('Color', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6b7280',
                'selectors' => [
                    '{{WRAPPER}} .primekit-page-list-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'primekit_page_list_subtitle_gap',
            [
                'label'      => esc_html__('Top Gap', 'primekit-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => ['px' => ['min' => 0, 'max' => 30]],
                'default'    => ['unit' => 'px', 'size' => 4],
                'selectors'  => [
                    '{{WRAPPER}} .primekit-page-list-subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // ── Icon Style ────────────────────────────────────────────────────
        $this->start_controls_section(
            'primekit_page_list_icon_style',
            [
                'label' => esc_html__('Icon Style', 'primekit-addons'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'primekit_page_list_icon_size',
            [
                'label'      => esc_html__('Icon Size', 'primekit-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => ['px' => ['min' => 10, 'max' => 80]],
                'default'    => ['unit' => 'px', 'size' => 24],
                'selectors'  => [
                    '{{WRAPPER}} .primekit-page-list-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .primekit-page-list-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'primekit_page_list_icon_color',
            [
                'label'     => esc_html__('Icon Color', 'primekit-addons'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#6c63ff',
                'selectors' => [
                    '{{WRAPPER}} .primekit-page-list-icon'     => 'color: {{VALUE}};',
                    '{{WRAPPER}} .primekit-page-list-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'primekit_page_list_icon_gap',
            [
                'label'      => esc_html__('Gap', 'primekit-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range'      => ['px' => ['min' => 0, 'max' => 40]],
                'default'    => ['unit' => 'px', 'size' => 12],
                'selectors'  => [
                    '{{WRAPPER}} .primekit-page-list-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        include 'renderview.php';
    }
}
