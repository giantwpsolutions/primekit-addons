<?php
namespace PrimeKit\Frontend\Elementor\Inc;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Helper functions for Elementor widgets
 */
class Helpers
{

    /**
     * Check if PrimeKit Pro is active/loaded.
     */
    public static function is_pro_active(): bool
    {
        // Any of these being true means Pro is present.
        if (defined('PRIMEKIT_PRO_VERSION')) {
            return true;
        }


        if (class_exists('\PrimeKitPro')) {
            return true;
        }


        return false;
    }

    /**
     * Get the locked widget notice text.
     *
     * @param string $widget_title The title of the locked widget.
     * @return string
     */
    public static function get_locked_message(string $widget_title): string
    {
        return sprintf(
            'Locked Widget: %s<br>Activate PrimeKit Pro to use this widget.',
            esc_html($widget_title)
        );
    }

    /**
     * Get the Pro upgrade URL.
     *
     * @return string
     */
    public static function get_pro_upgrade_url(): string
    {
        return 'https://primekitaddons.com/pro/';
    }


    /**
     * Get upsell HTML for Elementor sidebar.
     *
     * @param string $widget_title
     * @param string|null $extra_message Optional extra text.
     * @return string
     */
    public static function get_upsell_html(string $widget_title, string $extra_message = null): string
    {
        $title = sprintf(
            esc_html__('This widget is part of PrimeKit Pro', 'primekit-addons')
        );
        $message = $extra_message ?: sprintf(
            esc_html__('Activate or upgrade to unlock %s and 14+ more premium widgets.', 'primekit-addons'),
            esc_html($widget_title)
        );
        $url = self::get_pro_upgrade_url();
        $button = esc_html__('View Pro Plans', 'primekit-addons');

        return sprintf(
            '<div style="text-align:center;padding:14px 10px;line-height:1.5;">
                <p style="margin:0 0 8px;font-size:14px;"><strong>%s</strong></p>
                <p style="margin:0 0 12px;">%s</p>
                <a class="elementor-button elementor-button-success" href="%s" target="_blank" rel="noopener">%s</a>
            </div>',
            esc_html($title),
            esc_html($message),
            esc_url($url),
            esc_html($button)
        );
    }

}
