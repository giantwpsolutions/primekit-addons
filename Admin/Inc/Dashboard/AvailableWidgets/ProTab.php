<?php
namespace PrimeKit\Admin\Inc\Dashboard\AvailableWidgets;

//don't load this file directly
if (!defined('ABSPATH')) {
    exit;
}


use PrimeKit\Admin\Inc\Dashboard\AvailableWidgets\PrimeKitWidgets;
/**
 * ProTab class
 * Handles the rendering of the Pro widgets.
 *
 * @package PrimeKit\Admin\Inc\Dashboard\AvailableWidgets
 * @since 1.0.0
 */
class ProTab
{

    /**
     * Generates a complete demo URL by appending the given path to the base domain.
     *
     * @param string $path The specific path to append to the demo domain URL.
     * @return string The fully constructed demo URL.
     */
    public static function demo_url($path)
    {
        $domain = 'https://demo.primekitaddons.com/';
        return $domain . $path;
    }

    /**
     * Renders the Pro widgets.
     *
     * @since 1.0.0
     */
    public static function primekit_pro_widgets_display()
    {
      // Animated Timeline
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_anim_timeline_widget_field',
            esc_html__('Animated Timeline', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/animated-timeline.svg',
            false,
            self::demo_url('widgets/animated-timeline')
        ); 
      
        // Jobs
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_job_widget_field',
            esc_html__('Jobs', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/jobs.svg',
            false,
            self::demo_url('widgets/jobs')
        );     
        // Email Signature
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_email_signature_widget_field',
            esc_html__('Email Signature', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/email-signature.svg',
            false,
            self::demo_url('widgets/email-signature')
        );     
        // Team Member
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_team_member_carousel_widget_field',
            esc_html__('Team Member Carousel', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/team-member-carousel.svg',
            false,
            self::demo_url('widgets/team-member-carousel')
        );     
        
        // Timeline Milestone
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_timeline_milestone_widget_field',
            esc_html__('Timeline Milestone', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/timeline-milestone.svg',
            false,
            self::demo_url('widgets/timeline-milestone')
        );     
        
        // Video Testimonials
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_video_testimonials_widget_field',
            esc_html__('Video Testimonials', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/video-testimonials.svg',
            false,
            self::demo_url('widgets/video-testimonials')
        );     
        // Resources
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_resources_widget_field',
            esc_html__('Resources', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/resources.svg',
            false,
            self::demo_url('widgets/resources')
        );     
        // Resources Form
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_resources_form_widget_field',
            esc_html__('Resources Form', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/resource-form.svg',
            false,
            self::demo_url('widgets/resources-form')
        );     
        // Project Progress
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_project_progress_prack_widget_field',
            esc_html__('Project Progress', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/project-progress-tracker.svg',
            false,
            self::demo_url('widgets/project-progress')
        );     
        // Revenue Growth
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_revenue_growth_graph_widget_field',
            esc_html__('Revenue Growth Graph', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/revenue-growth-graphs.svg',
            false,
            self::demo_url('widgets/revenue-growth')
        );           
        // Advanced Accordion
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_advanced_accordion_widget_field',
            esc_html__('Advanced Accordion', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/advance-accordion.svg',
            false,
            self::demo_url('widgets/advanced-accordion')
        );           
        // Pricing Table
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_advanced_pricing_table_widget_field',
            esc_html__('Advanced Pricing Table', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/advanced-pricing-table.svg',
            false,
            self::demo_url('widgets/advanced-pricing-table')
        );           
        // Advanced Tab
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_advanced_tab_widget_field',
            esc_html__('Advanced Tabs', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/advanced-tab.svg',
            false,
            self::demo_url('widgets/advanced-tabs')
        );           
        // WhatsApp Chat
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_whatsapp_chat_widget_field',
            esc_html__('WhatsApp Chat', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/WhatsApps-Chat.svg',
            false,
            self::demo_url('widgets/whatsapp-chat')
        );           
        // Protected Content
        PrimeKitWidgets::primekit_available_widget(
            'primekitpro_protected_content_widget_field',
            esc_html__('Protected Content', 'primekit-pro'),
            PRIMEKIT_ADMIN_ASSETS . '/img/icons/protected-contents.svg',
            false,
            self::demo_url('widgets/protected-content')
        );  
    }
}