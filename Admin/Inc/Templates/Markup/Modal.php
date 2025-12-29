<?php
/**
 * PrimeKit Admin Template Markup
 *
 * Markup for the modal that displays the template builder.
 *
 * @package PrimeKit_Addons
 * @subpackage Admin/Inc/Templates/Markup
 */
namespace PrimeKit\Admin\Inc\Templates\Markup;

/**
 * Class Modal
 *
 * This class is responsible for rendering the template modal markup to the page.
 */
class Modal
{
    public function __construct()
    {
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_modal']);
        add_action('wp_ajax_primekit_get_template_categories', [$this, 'get_template_categories']);
      //  add_action('wp_ajax_nopriv_primekit_get_template_categories', [$this, 'get_template_categories']);

    }

    /**
     * Get unique categories from templates API
     */
    public function get_template_categories()
    {
        check_ajax_referer('primekit_template_nonce', 'nonce');

        $response = wp_remote_get('https://demo.primekitaddons.com/wp-json/primekit/v1/templates');


        if (is_wp_error($response)) {
            wp_send_json_error('Failed to fetch templates');
            return;
        }

        $body = wp_remote_retrieve_body($response);
        $templates = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error('Invalid JSON response');
            return;
        }

        if (!is_array($templates)) {
            wp_send_json_error('Invalid template data format');
            return;
        }

        $categories = ['All'];

        foreach ($templates as $template) {
            if (isset($template['categories']) && is_array($template['categories'])) {
                $categories = array_merge($categories, $template['categories']);
            }
        }

        $categories = array_unique($categories);
        $categories = array_values($categories);

        wp_send_json_success([
            'categories' => $categories,
            'templates' => $templates,
        ]);

    }


    public function enqueue_modal()
    {

        // Enqueue Template Categories JS
        wp_enqueue_script(
            'primekit-template-categories',
            PRIMEKIT_TEMPLATE_ASSETS . '/js/TemplateCategories.js',
            ['jquery'],
            PRIMEKIT_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script('primekit-template-categories', 'primekitTemplates', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('primekit_template_nonce'),
            'pluginUrl' => PRIMEKIT_URL
        ));

        // Render modal directly since we're in Elementor editor
        add_action('elementor/editor/footer', [$this, 'render']);
    }
    public function render()
    {
        ?>
        <style>
            #primekit-template-modal {
                display: none;
            }
            #primekit-template-modal .modal__overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.6);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 999999;
            }
            #primekit-template-modal .modal__container {
                background-color: #fff;
                padding: 0;
                max-width: 1200px !important;
                width: 1200px !important;
                min-width: 1200px !important;
                max-height: 90vh !important;
                height: 90vh !important;
                border-radius: 4px;
                overflow: hidden;
                box-sizing: border-box;
                position: relative;
                display: flex;
                flex-direction: column;
            }
            #primekit-template-modal .modal__header {
                padding: 0;
                border-bottom: 1px solid #e0e0e0;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            #primekit-template-modal .primekit-modal-heading {
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #333;
                padding: 20px 30px;
            }
            #primekit-template-modal .primekit-modal-heading img {
                width: 28px;
                height: 28px;
                object-fit: contain;
            }
            #primekit-template-modal .primekit-brand-text {
                font-size: 18px;
                font-weight: 700;
                background: linear-gradient(135deg, #0049E7 0%, #C835F8 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            #primekit-template-modal .primekit-templates-popup-tabs {
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 1;
                margin: 0;
            }
            #primekit-template-modal .primekit-templates-popup-tab {
                margin: 0;
            }
            #primekit-template-modal .primekit-header-right {
                display: flex;
                align-items: center;
                gap: 15px;
                padding-right: 20px;
            }
            #primekit-template-modal .primekit-templates-popup-tab ul {
                display: flex;
                list-style: none;
                margin: 0;
                padding: 0;
                gap: 0;
                height: 100%;
            }
            #primekit-template-modal .primekit-templates-popup-tab ul li {
                margin: 0;
                padding: 0;
            }
            #primekit-template-modal .primekit-templates-popup-tab ul li a,
            #primekit-template-modal .primekit-templates-popup-tab ul a {
                display: block !important;
                padding: 22px 25px !important;
                text-decoration: none !important;
                color: #666 !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                transition: all 0.3s ease !important;
                position: relative !important;
                border-bottom: 3px solid transparent !important;
                border-radius: 0 !important;
                height: 100% !important;
                box-sizing: border-box !important;
                background-color: transparent !important;
                background: transparent !important;
            }
            #primekit-template-modal .primekit-templates-popup-tab ul li a:hover,
            #primekit-template-modal .primekit-templates-popup-tab ul a:hover {
                color: #0049E7 !important;
                background: transparent !important;
                background-color: transparent !important;
            }
            #primekit-template-modal .primekit-templates-popup-tab ul li a.active,
            #primekit-template-modal .primekit-templates-popup-tab ul a.active {
                color: #0049E7 !important;
                border-bottom-color: #0049E7 !important;
                background: transparent !important;
                background-color: transparent !important;
            }
            #primekit-template-modal .primekit-templates-popup-tab ul li a.primekit-template-type-btn,
            #primekit-template-modal .primekit-templates-popup-tab ul li .primekit-template-type-btn {
                background: transparent !important;
                border-radius: 0 !important;
                padding: 22px 25px !important;
            }
            #primekit-template-modal .primekit-templates-popup-tab ul li a.primekit-template-type-btn.active,
            #primekit-template-modal .primekit-templates-popup-tab ul li .primekit-template-type-btn.active {
                background-color: transparent !important;
                background: transparent !important;
            }
            #primekit-template-modal .primekit-templates-search {
                margin: 0 0 20px 0;
                padding: 0;
            }
            #primekit-template-modal .primekit-templates-search form {
                margin: 0;
            }
            #primekit-template-modal .primekit-templates-search input {
                padding: 10px 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
                width: 100%;
                max-width: 400px;
                outline: none;
                transition: border-color 0.3s ease;
            }
            #primekit-template-modal .primekit-templates-search input:focus {
                border-color: #0049E7;
            }
            #primekit-template-modal .modal__content {
                flex: 1;
                overflow-y: auto;
                padding: 0;
            }
            #primekit-template-modal .primekit-templates-popup-content-area {
                display: flex;
                height: 100%;
            }
            #primekit-template-modal .primekit-templates-sidebar {
                width: 250px;
                flex-shrink: 0;
                border-right: 1px solid #e0e0e0;
                padding: 20px;
                background: #f9f9f9;
            }
            #primekit-template-modal .primekit-template-filters h3 {
                margin: 0 0 15px 0;
                font-size: 16px;
                font-weight: 600;
                color: #333;
            }
            #primekit-template-modal .primekit-category-radios {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            #primekit-template-modal .primekit-radio-item {
                position: relative;
                cursor: pointer;
                display: block;
            }
            #primekit-template-modal .primekit-radio-item input[type="radio"] {
                position: absolute;
                opacity: 0;
                cursor: pointer;
            }
            #primekit-template-modal .primekit-radio-label {
                display: flex;
                align-items: center;
                padding: 10px 15px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
                color: #666;
                background: #fff;
                border: 1px solid #e0e0e0;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            #primekit-template-modal .primekit-radio-label:hover {
                border-color: #0049E7;
                color: #333;
            }
            #primekit-template-modal .primekit-radio-item input[type="radio"]:checked + .primekit-radio-label {
                background: linear-gradient(135deg, #0049E7 0%, #C835F8 100%);
                color: #fff;
                border-color: transparent;
                font-weight: 600;
            }
            #primekit-template-modal .primekit-radio-count {
                margin-left: auto;
                font-size: 12px;
                opacity: 0.8;
            }
            #primekit-template-modal .primekit-templates-popup-content {
                flex: 1;
                padding: 20px;
                overflow-y: auto;
            }
            #primekit-template-modal .modal__close {
                position: relative;
                width: 32px;
                height: 32px;
                border: none;
                background: transparent;
                cursor: pointer;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 3px;
                flex-shrink: 0;
            }
            #primekit-template-modal .modal__close:before,
            #primekit-template-modal .modal__close:after {
                content: '';
                position: absolute;
                width: 20px;
                height: 2px;
                background: #555;
                transform: rotate(45deg);
            }
            #primekit-template-modal .modal__close:after {
                transform: rotate(-45deg);
            }
            #primekit-template-modal .modal__close:hover {
                background: #f0f0f0;
            }
        </style>
        <div id="primekit-template-modal" class="modal micromodal-slide primekit-templates-render-area" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="primekit-templates-modal-title">
                    <header class="modal__header primekit-modal-header">
                        <h2 id="primekit-templates-modal-title" class="primekit-modal-heading">
                            <img src="<?php echo PRIMEKIT_ADMIN_ASSETS . '/img/primekit-icon.svg'; ?>" alt="">
                            <span class="primekit-brand-text">PrimeKit</span>
                        </h2>
                        <!--Template Tabs in Header-->
                        <div class="primekit-templates-popup-tabs">
                            <div class="primekit-templates-popup-tab">
                                <ul>
                                    <!-- Dynamically loaded from AJAX by loadTemplateTypes(); -->
                                </ul>
                            </div>
                        </div>
                        <div class="primekit-header-right">
                            <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                        </div>
                    </header>
                    <main class="modal__content primekit-templates-contents-area">
                        <!--Template Popup-->
                        <div class="primekit-templates-popup-content-area">
                            <!--Template Popup Sidebar-->
                            <aside class="primekit-templates-sidebar">
                                <div class="primekit-template-filters">
                                    <h3><?php esc_html_e('Categories', 'primekit-addons'); ?></h3>
                                    <div class="primekit-category-radios" id="primekit-category-radios">
                                        <!-- Categories will be loaded dynamically via JavaScript -->
                                    </div>
                                </div>
                            </aside><!--/Template Popup Sidebar-->
                            <!--Template Popup Content-->
                            <div class="primekit-templates-popup-content">
                                <!--Search Bar-->
                                <div class="primekit-templates-search">
                                    <form action="">
                                        <input type="text" name="search" id="primekit-templates-search"
                                            placeholder="Search templates...">
                                    </form>
                                </div>
                                <!--Template Grid-->
                                <div class="primekit-template-grid-area" id="primekit-templates-modal-content">
                                    <p><?php echo esc_html__('Loading templates...', 'primekit-addons'); ?></p>
                                </div>
                            </div><!--/Template Popup Content-->
                        </div><!--/Template Popup-->

                    </main>
                    <!-- <footer class="modal__footer">
                        <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Close</button>
                    </footer> -->
                </div>
            </div>
        </div>


        <?php
    }

}
