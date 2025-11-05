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
     * Singleton instance
     *
     * @var Helpers|null
     */
    private static ?Helpers $instance = null;

    /**
     * Get singleton instance
     *
     * @return Helpers
     */
    public static function instance(): Helpers
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        // Frontend (and Elementor preview on frontend)
        add_action('wp_enqueue_scripts', function () {
            \PrimeKit\Frontend\Elementor\Inc\Helpers::ensure_locked_styles();
        }, 20);

        // Elementor editor sidebar/panel
        add_action('elementor/editor/after_enqueue_styles', function () {
            \PrimeKit\Frontend\Elementor\Inc\Helpers::ensure_locked_styles();
        }, 20);

    }



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
        $title = esc_html__('This widget is part of PrimeKit Pro', 'primekit-addons');
        $message = $extra_message ?: sprintf(
            esc_html__('Activate or upgrade to unlock %s and 15+ more premium widgets.', 'primekit-addons'),
            esc_html($widget_title)
        );

        return sprintf(
            '<div class="primekit-upsell-box">
                <p class="primekit-upsell-title"><strong>%s</strong></p>
                <p class="primekit-upsell-desc">%s</p>
                <a class="primekit-upsell-btn" href="%s" target="_blank" rel="noopener">%s</a>
            </div>',
            esc_html($title),
            esc_html($message),
            esc_url(self::get_pro_upgrade_url()),
            esc_html__('View Pro Plans', 'primekit-addons')
        );
    }

    /**
     * Shared HTML for the canvas locked box (no inline styles).
     */
    public static function get_locked_box_html(string $widget_title): string
    {
        $msg = self::get_locked_message($widget_title);
        $url = self::get_pro_upgrade_url();

        return sprintf(
            '<div class="primekit-locked-widget">
                <div class="primekit-locked-title">%s</div>
                <a class="primekit-locked-btn" href="%s" target="_blank" rel="noopener">%s</a>
            </div>',
            wp_kses_post($msg),
            esc_url($url),
            esc_html__('Upgrade to PrimeKit Pro', 'primekit-addons')
        );
    }

    /**
     * Make sure styles for upsell + locked box exist on both editor & frontend.
     * Call this from your widget's render().
     */
    public static function ensure_locked_styles(): void
    {
        // Register a handle without file and inject CSS inline
        $handle = 'primekit-locked-styles';

        if (!wp_style_is($handle, 'registered')) {
            wp_register_style($handle, false, [], '1.0.0'); // no file, inline only
        }

        if (!wp_style_is($handle, 'enqueued')) {
            wp_enqueue_style($handle);
        }

        // Only add once
        if (did_action('primekit/locked_styles_printed')) {
            return;
        }

        $css = '
        .primekit-upsell-box{ text-align:center; padding:14px 10px; line-height:1.5; }
        .primekit-upsell-title{ margin:0 0 8px; font-size:14px; }
        .primekit-upsell-desc{ margin:0 0 12px; color:#555; }
        .primekit-upsell-btn{
            display:inline-block; padding:8px 14px; border-radius:6px;
            background:#e6006d; color:#fff; text-decoration:none; font-weight:600;
            box-shadow:0 6px 14px rgba(230,0,109,.25);
        }
        .primekit-upsell-btn:hover{ filter:brightness(1.05); }

        .primekit-locked-widget{
            border:1px dashed #BDBDBD; padding:18px; border-radius:8px; text-align:center;
            background:#fff;
        }
        .primekit-locked-title{ font-weight:600; margin-bottom:10px; color:#111; }
        .primekit-locked-btn{
            display:inline-block; padding:8px 14px; border-radius:6px;
            background:#e6006d; color:#fff; text-decoration:none; font-weight:600;
            box-shadow:0 6px 14px rgba(230,0,109,.25);
            color:#fff !important;
        }
        .primekit-locked-btn:hover{ filter:brightness(1.05); color:#fff !important; }';

        wp_add_inline_style($handle, $css);

        // Mark as printed to avoid duplicates
        do_action('primekit/locked_styles_printed');
    }

}
