<?php
/**
 * Elementor Library Tab Integration
 *
 * Registers PrimeKit template library as a custom tab
 * in Elementor's native template library modal.
 *
 * @package PrimeKit
 * @subpackage Admin/Inc/Templates
 * @since 1.2.11
 */

namespace PrimeKit\Admin\Inc\Templates;

defined('ABSPATH') || exit;

/**
 * Class ElementorTab
 *
 * Handles the registration and integration of PrimeKit templates
 * into Elementor's template library interface.
 */
class ElementorTab
{
    /**
     * Constructor
     *
     * Initializes hooks for Elementor editor integration.
     */
    public function __construct()
    {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     *
     * @return void
     */
    private function init_hooks()
    {
        // Register template source with Elementor
        add_action('elementor/init', [$this, 'register_template_source']);

        // Enqueue assets in Elementor editor
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);

        // Add custom library tab
        add_action('elementor/editor/footer', [$this, 'print_template_views']);
    }

    /**
     * Register PrimeKit template source with Elementor
     *
     * @return void
     */
    public function register_template_source()
    {
        // Get Elementor's template library manager
        $library_manager = \Elementor\Plugin::instance()->templates_manager;

        // Register our custom source
        $library_manager->register_source(Library_Source::class);
    }

    /**
     * Enqueue JavaScript for Elementor editor
     *
     * @return void
     */
    public function enqueue_editor_scripts()
    {
        wp_enqueue_script(
            'primekit-elementor-library',
            PRIMEKIT_TEMPLATE_ASSETS . '/js/elementor-library.js',
            ['jquery', 'elementor-editor', 'backbone-marionette'],
            PRIMEKIT_VERSION,
            true
        );

        // Pass data to JavaScript
        wp_localize_script('primekit-elementor-library', 'PrimeKitLibrary', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('primekit_library_nonce'),
            'source' => 'primekit-library',
            'logo' => PRIMEKIT_URL . 'Admin/Assets/img/primekit-icon.svg',
        ]);
    }

    /**
     * Enqueue CSS for Elementor editor
     *
     * @return void
     */
    public function enqueue_editor_styles()
    {
        wp_enqueue_style(
            'primekit-elementor-library',
            PRIMEKIT_TEMPLATE_ASSETS . '/css/elementor-library.css',
            ['elementor-editor'],
            PRIMEKIT_VERSION
        );
    }

    /**
     * Print Backbone template views for the library modal
     *
     * @return void
     */
    public function print_template_views()
    {
        ?>
        <script type="text/template" id="tmpl-elementor-primekit-library-header-actions">
            <div id="primekit-library-header-actions">
                <div id="primekit-library-header-sync" class="elementor-templates-modal__header__item">
                    <i class="eicon-sync" aria-hidden="true" title="<?php esc_attr_e('Sync Library', 'primekit-addons'); ?>"></i>
                    <span class="elementor-screen-only"><?php esc_html_e('Sync Library', 'primekit-addons'); ?></span>
                </div>
            </div>
        </script>

        <script type="text/template" id="tmpl-elementor-primekit-library-header-logo">
            <span class="primekit-library-logo-wrap">
                <img src="<?php echo esc_url(PRIMEKIT_URL . 'Admin/Assets/img/primekit-icon.svg'); ?>" alt="<?php esc_attr_e('PrimeKit', 'primekit-addons'); ?>" class="primekit-library-logo">
            </span>
        </script>

        <script type="text/template" id="tmpl-elementor-primekit-library-loading">
            <div class="elementor-loader-wrapper">
                <div class="elementor-loader">
                    <div class="elementor-loader-boxes">
                        <div class="elementor-loader-box"></div>
                        <div class="elementor-loader-box"></div>
                        <div class="elementor-loader-box"></div>
                        <div class="elementor-loader-box"></div>
                    </div>
                </div>
                <div class="elementor-loading-title"><?php esc_html_e('Loading Templates', 'primekit-addons'); ?></div>
            </div>
        </script>

        <script type="text/template" id="tmpl-elementor-primekit-library-templates">
            <div id="primekit-library-toolbar">
                <div id="primekit-library-filter-text">
                    <input id="primekit-library-search" placeholder="<?php esc_attr_e('Search Template...', 'primekit-addons'); ?>" />
                    <i class="eicon-search"></i>
                </div>
            </div>
            <div class="primekit-library-templates-container">
                <div id="primekit-library-templates-list"></div>
            </div>
        </script>

        <script type="text/template" id="tmpl-elementor-primekit-library-template">
            <div class="primekit-library-template-body" data-template-id="{{ template_id }}">
                <div class="primekit-library-template-screenshot">
                    <img src="{{ thumbnail }}" alt="{{ title }}">
                    <div class="primekit-library-template-overlay">
                        <div class="primekit-library-template-controls">
                            <button class="primekit-library-btn-insert elementor-button" data-template-id="{{ template_id }}">
                                <i class="eicon-file-download" aria-hidden="true"></i>
                                <span><?php esc_html_e('Insert', 'primekit-addons'); ?></span>
                            </button>
                            <# if ( url ) { #>
                            <a href="{{ url }}" class="primekit-library-btn-preview elementor-button" target="_blank">
                                <i class="eicon-device-desktop" aria-hidden="true"></i>
                                <span><?php esc_html_e('Preview', 'primekit-addons'); ?></span>
                            </a>
                            <# } #>
                        </div>
                    </div>
                </div>
                <div class="primekit-library-template-name">{{ title }}</div>
                <# if ( isPro ) { #>
                <span class="primekit-library-template-badge primekit-library-badge-pro"><?php esc_html_e('Pro', 'primekit-addons'); ?></span>
                <# } #>
            </div>
        </script>

        <script type="text/template" id="tmpl-elementor-primekit-library-empty">
            <div class="elementor-template-library-blank-icon">
                <img src="<?php echo esc_url(PRIMEKIT_URL . 'Admin/Assets/img/primekit-icon.svg'); ?>" class="elementor-template-library-no-results">
            </div>
            <div class="elementor-template-library-blank-title"><?php esc_html_e('No Templates Found', 'primekit-addons'); ?></div>
            <div class="elementor-template-library-blank-message"><?php esc_html_e('Try different search keywords or sync the library.', 'primekit-addons'); ?></div>
        </script>
        <?php
    }
}
