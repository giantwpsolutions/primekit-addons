<?php
/**
 * SetupWizard.php
 *
 * Displays a setup wizard after plugin activation so users can configure
 * PrimeKit Addons in a guided, step-by-step flow — similar to ElementsKit.
 *
 * @package PrimeKit\Admin\Inc\SetupWizard
 * @since 1.3.2
 */
namespace PrimeKit\Admin\Inc\SetupWizard;

if (!defined('ABSPATH')) exit;

class SetupWizard
{
    /** Ordered list of wizard steps (slug => label). */
    private array $steps = [];

    public function __construct()
    {
        $this->steps = [
            'welcome'  => __('Welcome', 'primekit-addons'),
            'features' => __('Features', 'primekit-addons'),
            'done'     => __('Done!', 'primekit-addons'),
        ];

        add_action('admin_menu',  [$this, 'register_wizard_page'], 30);
        add_action('admin_init',  [$this, 'maybe_redirect']);
        add_action('wp_ajax_primekit_wizard_save_features', [$this, 'ajax_save_features']);
    }

    // ─── Redirect ────────────────────────────────────────────────────────────

    /**
     * After activation, redirect once to the wizard.
     * Skips on multisite, AJAX, or CLI requests.
     */
    public function maybe_redirect(): void
    {
        if (!get_transient('primekit_setup_wizard_redirect')) return;
        if (is_network_admin() || wp_doing_ajax() || defined('WP_CLI')) return;

        delete_transient('primekit_setup_wizard_redirect');

        // Don't redirect if already on the wizard page
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if ($current_page === 'primekit-setup-wizard') return;

        wp_safe_redirect(admin_url('admin.php?page=primekit-setup-wizard&step=welcome'));
        exit;
    }

    // ─── Page Registration ────────────────────────────────────────────────────

    /** Register a hidden admin page for the wizard (no sidebar menu entry). */
    public function register_wizard_page(): void
    {
        add_submenu_page(
            null,                                                   // hidden — no parent menu
            __('PrimeKit Setup Wizard', 'primekit-addons'),
            __('Setup Wizard', 'primekit-addons'),
            'manage_options',
            'primekit-setup-wizard',
            [$this, 'render_wizard']
        );
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render_wizard(): void
    {
        $step = $this->get_current_step();

        // Enqueue wizard assets
        wp_enqueue_style(
            'primekit-setup-wizard',
            PRIMEKIT_ADMIN_ASSETS . '/css/setup-wizard.css',
            ['wp-admin'],
            PRIMEKIT_VERSION
        );
        wp_enqueue_script(
            'primekit-setup-wizard',
            PRIMEKIT_ADMIN_ASSETS . '/js/setup-wizard.js',
            ['jquery'],
            PRIMEKIT_VERSION,
            true
        );
        wp_localize_script('primekit-setup-wizard', 'primekitWizard', [
            'ajaxUrl'      => admin_url('admin-ajax.php'),
            'nonce'        => wp_create_nonce('primekit_wizard_nonce'),
            'doneUrl'      => admin_url('admin.php?page=primekit-setup-wizard&step=done'),
            'dashboardUrl' => admin_url('admin.php?page=primekit_home'),
        ]);

        // Load saved feature options to pre-populate toggles
        $features = wp_parse_args(
            get_option('primekit_features_options', []),
            ['enable_themebuilder' => 1, 'enable_editor_template_import' => 1]
        );

        include __DIR__ . '/Views/wizard.php';
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Returns the sanitized current step slug, falling back to 'welcome'. */
    public function get_current_step(): string
    {
        $step = isset($_GET['step']) ? sanitize_text_field(wp_unslash($_GET['step'])) : 'welcome';
        return array_key_exists($step, $this->steps) ? $step : 'welcome';
    }

    /** Returns the URL for a given step. */
    public function step_url(string $step): string
    {
        return esc_url(admin_url('admin.php?page=primekit-setup-wizard&step=' . $step));
    }

    /** Returns an ordered array of step slugs. */
    public function get_steps(): array
    {
        return $this->steps;
    }

    // ─── AJAX ─────────────────────────────────────────────────────────────────

    /** Save feature selections from Step 2 and redirect to "Done". */
    public function ajax_save_features(): void
    {
        check_ajax_referer('primekit_wizard_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Unauthorized', 'primekit-addons')]);
        }

        $submitted = isset($_POST['features']) && is_array($_POST['features'])
            ? array_map('sanitize_text_field', wp_unslash($_POST['features']))
            : [];

        $options = [
            'enable_themebuilder'            => in_array('enable_themebuilder', $submitted, true) ? 1 : 0,
            'enable_editor_template_import'  => in_array('enable_editor_template_import', $submitted, true) ? 1 : 0,
        ];

        update_option('primekit_features_options', $options);
        update_option('primekit_wizard_completed', 1);

        wp_send_json_success(['redirect' => admin_url('admin.php?page=primekit-setup-wizard&step=done')]);
    }
}
