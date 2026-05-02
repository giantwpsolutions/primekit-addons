<?php
/**
 * This file contains the class responsible for adding the Templates menu to the WP admin sidebar.
 *
 * @package PrimeKit
 * @subpackage Admin/Inc/Dashboard/Templates
 */

namespace PrimeKit\Admin\Inc\Dashboard\Templates;

class TemplatesMenu
{
    public function __construct()
    {
        // Submenu removed — handled by the Vue admin app.
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_primekit_fetch_templates', [$this, 'fetch_templates']);
    }
    public function enqueue_scripts($hook)
    {
        if ($hook !== 'primekit_page_primekit_templates') {
            return;
        }

        wp_enqueue_style('primekit-template-menu', PRIMEKIT_ADMIN_ASSETS . '/css/template-menu.css', [], null);

        wp_enqueue_script('primekit-templates-admin', PRIMEKIT_ADMIN_ASSETS . '/js/templates-admin.js', ['jquery'], null, true);

        wp_localize_script('primekit-templates-admin', 'PrimeKitTemplates', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('primekit_fetch_templates'),
        ]);
    }


    public function add_templates_submenu()
    {
        add_submenu_page(
            'primekit_home', // Parent slug
            esc_html__('Templates', 'primekit-addons'),     // Page title
            esc_html__('Templates', 'primekit-addons'),     // Menu title
            'manage_options', // Capability
            'primekit_templates', // Menu slug
            [$this, 'render_templates_page'] // Callback function
        );
    }

    public function render_templates_page()
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Templates', 'primekit-addons'); ?></h1>
            <p><?php esc_html_e('Browse the available templates below. Loading may take a moment as they\'re fetched from a remote server.', 'primekit-addons'); ?>
            </p>

            <div id="primekit-templates-content-wrapper">
                <div class="primekit-loading"></div>
            </div>
            <div id="primekit-templates-pagination-wrapper"></div>

        </div>
        <?php
    }

    public function fetch_templates()
    {
        check_ajax_referer('primekit_fetch_templates', 'nonce');

        $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $per_page = 16;

        $response = wp_remote_get('https://demo.primekitaddons.com/wp-json/primekit/v1/templates');

        if (is_wp_error($response)) {
            wp_send_json_error(['message' => 'Failed to fetch templates']);
        }

        $templates = json_decode(wp_remote_retrieve_body($response), true);

        $total_templates = count($templates);
        $total_pages = ceil($total_templates / $per_page);
        $offset = ($page - 1) * $per_page;

        $paged_templates = array_slice($templates, $offset, $per_page);

        $output = '';
        ob_start();

        foreach ($paged_templates as $template) {
            $details_response = wp_remote_get("https://demo.primekitaddons.com/wp-json/primekit/v1/templates/{$template['id']}");
            if (is_wp_error($details_response)) {
                continue;
            }

            $details = json_decode(wp_remote_retrieve_body($details_response), true);

            $title = esc_html($template['title']);
            $thumbnail = esc_url($template['thumbnail']);
            $download_url = esc_url($details['download_url'] ?? '#');
            $demo_url = esc_url($details['demo_url'] ?? '#');
            ?>
            <div class="primekit-single-template-item">
                <div class="primekit-template-thumbnail">
                    <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>">
                </div>
                <div class="primekit-template-footer">
                    <h2><?php echo $title; ?></h2>
                    <div class="primekit-template-buttons">
                        <a href="<?php echo $download_url; ?>" target="_blank"><?php esc_html_e('Download', 'primekit-addons'); ?> </a>
                        <a href="<?php echo $demo_url; ?>"  target="_blank"><?php esc_html_e('Preview', 'primekit-addons'); ?></a>
                    </div>
                </div>
            </div>
            <?php
        }

        $output = ob_get_clean();


        // Pagination UI
        $pagination_html = '';

        if ($total_pages > 1) {
            $pagination_html .= '<div class="primekit-pagination" style="margin-top: 20px; display: flex; gap: 10px;">';
            for ($i = 1; $i <= $total_pages; $i++) {
                $is_active = ($i === $page) ? 'button-primary current-page' : '';
                $pagination_html .= '<button class="button primekit-page-btn ' . esc_attr($is_active) . '" data-page="' . esc_attr($i) . '">' . esc_html($i) . '</button>';
            }
            $pagination_html .= '</div>';
        }

        wp_send_json_success([
            'html' => $output,
            'pagination' => $pagination_html,
            'current_page' => $page
        ]);
    }



}