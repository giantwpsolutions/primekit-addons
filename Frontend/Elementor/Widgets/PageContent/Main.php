<?php
/**
 * Post Content
 *
 * Elementor widget for displaying the post content.
 *
 * @since 1.0.2
 *
 * @package PrimeKit
 * @subpackage PrimeKit/Frontend/Elementor/Widgets
 */
namespace PrimeKit\Frontend\Elementor\Widgets\PageContent;

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

/**
 * Elementor List Widget.
 */
class Main extends Widget_Base
{

	public function get_name()
	{
		return 'primekit-page-content';
	}

	public function get_title()
	{
		return esc_html__('Page Contents', 'primekit-addons');
	}

	public function get_icon()
	{
		return 'eicon-post-content primekit-addons-icon';
	}

	public function get_categories()
	{
		return ['primekit-category'];
	}

	public function get_keywords()
	{
		return ['prime', 'post', 'content', 'blog', 'page'];
	}


	/**
	 * Register list widget controls.
	 */
	protected function register_controls()
	{
		//Template
		$this->start_controls_section(
			'primekit_addons_page_content',
			[
				'label' => esc_html__('Alignment', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);


		//Alignment
		$this->add_responsive_control(
			'primekit_addons_page_content_align',
			[
				'label' => esc_html__('Alignment', 'primekit-addons'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'primekit-addons'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'primekit-addons'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'primekit-addons'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area' => 'text-align: {{VALUE}}',
				],
			]
		);

		//PrimeKit Notice
		$this->add_control(
			'primekit_addons_addons_notice',
			[
				'type' => \Elementor\Controls_Manager::NOTICE,
				'notice_type' => 'warning',
				'dismissible' => false,
				'heading' => esc_html__('Created by PrimeKit', 'primekit-addons'),
				'content' => esc_html__('This amazing widget is built with PrimeKit Addons, making it super easy to create beautiful and functional designs.', 'primekit-addons'),
			]
		);

		$this->end_controls_section();

		//post title style

		$this->start_controls_section(
			'primekit_addons_page_content_style',
			[
				'label' => esc_html__('Paragraph Style', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primekit_addons_page_content_color',
			[
				'label' => esc_html__('Text Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#555555',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'primekit_addons_page_content_link_color',
			[
				'label' => esc_html__('Link Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#10b0e0',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'primekit_addons_page_content_link_hov_color',
			[
				'label' => esc_html__('Link Hover Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#6309b3',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'primekit_addons_page_content_typography',
				'label' => esc_html__('Typography', 'primekit-addons'),
				'selector' => '{{WRAPPER}} .primekit-elementor-page-content-area',
			]
		);

		//end of paragragh style
		$this->end_controls_section();

		//H1 style
		$this->start_controls_section(
			'primekit_addons_page_content_h1_style',
			[
				'label' => esc_html__('H1 Style', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primekit_addons_page_h1_color',
			[
				'label' => esc_html__('Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area h1' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'primekit_addons_page_h1_typography',
				'label' => esc_html__('Typography', 'primekit-addons'),
				'selector' => '{{WRAPPER}} .primekit-elementor-page-content-area h1',
			]
		);

		//end H1 style
		$this->end_controls_section();

		//H2 style
		$this->start_controls_section(
			'primekit_addons_page_content_h2_style',
			[
				'label' => esc_html__('H2 Style', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primekit_addons_page_h2_color',
			[
				'label' => esc_html__('Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area h2' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'primekit_addons_page_h2_typography',
				'label' => esc_html__('Typography', 'primekit-addons'),
				'selector' => '{{WRAPPER}} .primekit-elementor-page-content-area h2',
			]
		);

		//end H2 style
		$this->end_controls_section();

		//H3 style
		$this->start_controls_section(
			'primekit_addons_page_content_h3_style',
			[
				'label' => esc_html__('H3 Style', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primekit_addons_page_h3_color',
			[
				'label' => esc_html__('Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'primekit_addons_page_h3_typography',
				'label' => esc_html__('Typography', 'primekit-addons'),
				'selector' => '{{WRAPPER}} .primekit-elementor-page-content-area h3',
			]
		);

		//end H3 style
		$this->end_controls_section();

		//H4 style
		$this->start_controls_section(
			'primekit_addons_page_content_h4_style',
			[
				'label' => esc_html__('H4 Style', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primekit_addons_page_h4_color',
			[
				'label' => esc_html__('Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area h4' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'primekit_addons_page_h4_typography',
				'label' => esc_html__('Typography', 'primekit-addons'),
				'selector' => '{{WRAPPER}} .primekit-elementor-page-content-area h4',
			]
		);

		//end H4 style
		$this->end_controls_section();

		//H5 style
		$this->start_controls_section(
			'primekit_addons_page_content_h5_style',
			[
				'label' => esc_html__('H5 Style', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primekit_addons_page_h5_color',
			[
				'label' => esc_html__('Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area h5' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'primekit_addons_page_h5_typography',
				'label' => esc_html__('Typography', 'primekit-addons'),
				'selector' => '{{WRAPPER}} .primekit-elementor-page-content-area h5',
			]
		);

		//end H5 style
		$this->end_controls_section();

		//H6 style
		$this->start_controls_section(
			'primekit_addons_page_content_h6_style',
			[
				'label' => esc_html__('H6 Style', 'primekit-addons'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primekit_addons_page_h6_color',
			[
				'label' => esc_html__('Color', 'primekit-addons'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333333',
				'selectors' => [
					'{{WRAPPER}} .primekit-elementor-page-content-area h6' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'primekit_addons_page_h6_typography',
				'label' => esc_html__('Typography', 'primekit-addons'),
				'selector' => '{{WRAPPER}} .primekit-elementor-page-content-area h6',
			]
		);

		//end H6 style
		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 */
	protected function render()
	{
		include 'renderview.php';
	}
}