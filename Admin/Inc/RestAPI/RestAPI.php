<?php
namespace PrimeKit\Admin\Inc\RestAPI;

if (!defined('ABSPATH')) exit;

/**
 * REST API endpoints for the Vue admin dashboard.
 */
class RestAPI
{
    const NAMESPACE = 'primekit/v1';

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        $auth = ['permission_callback' => [$this, 'check_admin']];

        register_rest_route(self::NAMESPACE, '/dashboard', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_dashboard'],
        ] + $auth);

        register_rest_route(self::NAMESPACE, '/widgets', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_widgets'],
        ] + $auth);

        register_rest_route(self::NAMESPACE, '/widgets/toggle', [
            'methods'  => 'POST',
            'callback' => [$this, 'toggle_widget'],
        ] + $auth);

        register_rest_route(self::NAMESPACE, '/settings', [
            ['methods' => 'GET',  'callback' => [$this, 'get_settings']] + $auth,
            ['methods' => 'POST', 'callback' => [$this, 'save_settings']] + $auth,
        ]);

        register_rest_route(self::NAMESPACE, '/extensions', [
            ['methods' => 'GET',  'callback' => [$this, 'get_extensions']] + $auth,
            ['methods' => 'POST', 'callback' => [$this, 'save_extensions']] + $auth,
        ]);

    }

    public function check_admin()
    {
        return current_user_can('manage_options');
    }

    // ─── Dashboard ──────────────────────────────────────────────
    public function get_dashboard()
    {
        $all_widgets   = $this->get_all_widget_keys();
        $active_count  = count(array_filter($all_widgets, fn($k) => get_option($k, '1') === '1'));
        $total_count   = count($all_widgets);

        $free_keys = $this->get_free_widget_keys();
        $woo_keys  = $this->get_woo_widget_keys();
        $pro_keys  = $this->get_pro_widget_keys();

        $free_active = count(array_filter($free_keys, fn($k) => get_option($k, '1') === '1'));
        $woo_active  = count(array_filter($woo_keys,  fn($k) => get_option($k, '1') === '1'));

        $features        = get_option('primekit_features_options', []);
        $perf_mode       = get_option('primekit_performance_mode', 'standard');

        return rest_ensure_response([
            'version'        => PRIMEKIT_VERSION,
            'totalWidgets'   => $total_count,
            'activeWidgets'  => $active_count,
            'inactiveWidgets'=> $total_count - $active_count,
            'proWidgets'     => count($pro_keys),
            'freeWidgets'    => count($free_keys),
            'wooWidgets'     => count($woo_keys),
            'performanceMode'=> $perf_mode,
            'changelog'      => $this->get_changelog(),
        ]);
    }

    // ─── Widgets ─────────────────────────────────────────────────
    public function get_widgets()
    {
        $all = array_merge(
            $this->get_free_widget_keys(),
            $this->get_woo_widget_keys(),
            $this->get_pro_widget_keys()
        );
        $result = [];
        foreach ($all as $key) {
            $result[$key] = get_option($key, '1') === '1';
        }
        return rest_ensure_response(['widgets' => $result]);
    }

    public function toggle_widget(\WP_REST_Request $req)
    {
        $body    = $req->get_json_params();
        $name    = sanitize_text_field( $body['name']    ?? '' );
        $enabled = ! empty( $body['enabled'] );

        $allowed = array_merge(
            $this->get_free_widget_keys(),
            $this->get_woo_widget_keys()
        );

        if ( ! $name || ! in_array( $name, $allowed, true ) ) {
            return new \WP_Error( 'invalid_widget', 'Widget key not allowed.', [ 'status' => 400 ] );
        }

        update_option( $name, $enabled ? '1' : '0' );
        return rest_ensure_response( [ 'success' => true, 'key' => $name, 'enabled' => $enabled ] );
    }

    // ─── Settings ────────────────────────────────────────────────
    public function get_settings()
    {
        $mailchimp = get_option('primekit_mailchimp_options', []);
        $cost_est  = get_option('primekit_cost_estimation_options', [
            'cost_estimation_package_1' => 'Low',
            'cost_estimation_package_2' => 'Medium',
            'cost_estimation_package_3' => 'High',
        ]);
        $perf_mode = get_option('primekit_performance_mode', 'standard');
        return rest_ensure_response([
            'mailchimp'        => $mailchimp,
            'cost_estimation'  => $cost_est,
            'performanceMode'  => $perf_mode,
            'sysInfo'          => [
                'plugin'       => PRIMEKIT_VERSION,
                'wp'           => get_bloginfo('version'),
                'php'          => PHP_VERSION,
                'elementor'    => defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : 'Not installed',
            ],
        ]);
    }

    public function save_settings(\WP_REST_Request $req)
    {
        $body = $req->get_json_params();

        if (isset($body['mailchimp'])) {
            $clean = [];
            if (isset($body['mailchimp']['mailchimp_api_key'])) {
                $clean['mailchimp_api_key'] = sanitize_text_field($body['mailchimp']['mailchimp_api_key']);
            }
            update_option('primekit_mailchimp_options', $clean);
        }

        if (isset($body['cost_estimation'])) {
            $clean = [];
            foreach (['cost_estimation_package_1', 'cost_estimation_package_2', 'cost_estimation_package_3'] as $field) {
                if (isset($body['cost_estimation'][$field])) {
                    $clean[$field] = sanitize_text_field($body['cost_estimation'][$field]);
                }
            }
            update_option('primekit_cost_estimation_options', $clean);
        }

        if (isset($body['performanceMode']) && in_array($body['performanceMode'], ['standard', 'aggressive'], true)) {
            update_option('primekit_performance_mode', $body['performanceMode']);
        }

        return rest_ensure_response(['success' => true]);
    }

    // ─── Extensions ──────────────────────────────────────────────
    public function get_extensions()
    {
        $opts = get_option('primekit_features_options', []);
        return rest_ensure_response(array_merge([
            'enable_themebuilder'            => true,
            'enable_editor_template_import'  => true,
            'enable_custom_css'              => true,
            'enable_css_transform'           => true,
            'enable_preloader'               => true,
            'enable_wrapper_url'             => true,
            'enable_nested_tabs'             => true,
        ], $opts));
    }

    public function save_extensions(\WP_REST_Request $req)
    {
        $body    = $req->get_json_params();
        $allowed = [
            'enable_themebuilder', 'enable_editor_template_import',
            'enable_custom_css', 'enable_css_transform',
            'enable_preloader', 'enable_wrapper_url', 'enable_nested_tabs',
        ];
        $clean = [];
        foreach ($allowed as $key) {
            $clean[$key] = isset($body[$key]) ? (bool) $body[$key] : false;
        }
        update_option('primekit_features_options', $clean);
        return rest_ensure_response(['success' => true]);
    }

    // ─── Header Footer ───────────────────────────────────────────
    public function get_header_footer_templates()
    {
        $posts = get_posts([
            'post_type'   => 'primekit_library',
            'post_status' => ['publish', 'draft'],
            'numberposts' => -1,
            'meta_query'  => [[
                'key'     => 'primekit_themebuilder_select',
                'value'   => ['header', 'footer'],
                'compare' => 'IN',
            ]],
        ]);

        $templates = [];
        foreach ( $posts as $post ) {
            $type = get_post_meta( $post->ID, 'primekit_themebuilder_select', true );
            $templates[] = [
                'id'      => $post->ID,
                'title'   => $post->post_title ?: __( '(no title)', 'primekit-addons' ),
                'type'    => $type,
                'status'  => $post->post_status,
                'editUrl' => admin_url( 'post.php?post=' . $post->ID . '&action=elementor' ),
                'date'    => get_the_date( 'M j, Y', $post ),
            ];
        }

        return rest_ensure_response( [ 'templates' => $templates ] );
    }

    public function delete_header_footer_template( \WP_REST_Request $req )
    {
        $id   = (int) $req->get_param( 'id' );
        $post = get_post( $id );

        if ( ! $post || $post->post_type !== 'primekit_library' ) {
            return new \WP_Error( 'not_found', 'Template not found.', [ 'status' => 404 ] );
        }

        $type = get_post_meta( $id, 'primekit_themebuilder_select', true );
        if ( ! in_array( $type, [ 'header', 'footer' ], true ) ) {
            return new \WP_Error( 'forbidden', 'Not a header/footer template.', [ 'status' => 403 ] );
        }

        wp_delete_post( $id, true );
        return rest_ensure_response( [ 'success' => true ] );
    }

    // ─── Widget key lists ────────────────────────────────────────
    private function get_free_widget_keys(): array
    {
        return [
            'primekit_anim_text_widget_field', 'primekit_blockquote_widget_field',
            'primekit_blog_fancy_widget_field', 'primekit_author_bio_widget_field',
            'primekit_blog_grid_widget_field', 'primekit_blog_list_widget_field',
            'primekit_breadcrumb_widget_field', 'primekit_call_button_widget_field',
            'primekit_back_top_widget_field', 'primekit_before_after_widget_field',
            'primekit_card_info_widget_field', 'primekit_cat_list_widget_field',
            'primekit_contact_form7_widget_field', 'primekit_circular_skill_widget_field',
            'primekit_comment_form_widget_field', 'primekit_contact_info_widget_field',
            'primekit_count_down_widget_field', 'primekit_counter_up_widget_field',
            'primekit_cta_widget_field', 'primekit_feat_img_widget_field',
            'primekit_flip_box_widget_field', 'primekit_icon_box_widget_field',
            'primekit_img_hover_widget_field', 'primekit_img_text_scroll_widget_field',
            'primekit_loading_screen_widget_field', 'primekit_page_title_widget_field',
            'primekit_page_content_widget_field', 'primekit_popup_widget_field',
            'primekit_portfolio_widget_field', 'primekit_post_content_widget_field',
            'primekit_post_meta_widget_field', 'primekit_post_title_widget_field',
            'primekit_pricing_table_widget_field', 'primekit_recent_post_widget_field',
            'primekit_related_post_widget_field', 'primekit_search_form_widget_field',
            'primekit_search_icon_widget_field', 'primekit_sec_title_widget_field',
            'primekit_shape_anim_widget_field', 'primekit_single_img_scroll_field',
            'primekit_site_logo_widget_field', 'primekit_site_title_tagline_field',
            'primekit_skill_bar_widget_field', 'primekit_social_share_widget_field',
            'primekit_sticker_text_field', 'primekit_tag_info_widget_field',
            'primekit_team_member_widget_field', 'primekit_testi_caro_widget_field',
            'primekit_wp_menu_widget_field', 'primekit_dual_button_widget_field',
            'primekit_business_hours_field', 'primekit_archive_title_field',
            'primekit_gravity_form_field', 'primekit_image_gallery_field',
            'primekit_lottie_icon_widget_field', 'primekit_mailchimp_switch_field',
            'primekit_template_slider_field', 'primekit_cost_estimation_field',
            'primekit_modern_post_grid_field', 'primekit_popular_posts_field',
            'primekit_fetch_posts_field', 'primekit_posts_slider_field',
            'primekit_copyright_field', 'primekit_advanced_list_field',
            'primekit_glass_card_field', 'primekit_page_list_widget_field',
        ];
    }

    private function get_woo_widget_keys(): array
    {
        return [
            'primekit_wc_add_to_cart_field', 'primekit_wc_cart_icon_field',
            'primekit_wc_cart_page_field', 'primekit_wc_checkout_field',
            'primekit_wc_product_image_field', 'primekit_wc_product_meta_field',
            'primekit_wc_product_price_field', 'primekit_wc_related_field',
            'primekit_wc_short_desc_field', 'primekit_wc_product_tabs_field',
            'primekit_wc_product_title_field', 'primekit_wc_account_field',
            'primekit_wc_breadcrumb_field',
        ];
    }

    private function get_pro_widget_keys(): array
    {
        return [
            'primekit_advanced_accordion_locked_widget_field',
            'primekit_advanced_pricing_table_locked_widget_field',
            'primekit_advanced_tab_locked_widget_field',
            'primekit_email_signature_locked_widget_field',
            'primekit_jobs_locked_widget_field',
            'primekit_lottie_locked_widget_field',
            'primekit_project_progress_track_locked_widget_field',
            'primekit_protected_content_locked_widget_field',
            'primekit_resource_form_locked_widget_field',
            'primekit_resources_locked_widget_field',
            'primekit_revenue_growth_graphs_locked_widget_field',
            'primekit_team_member_carousel_locked_widget_field',
            'primekit_timeline_milestone_locked_widget_field',
            'primekit_video_testimonials_locked_widget_field',
            'primekit_whatsapp_chat_locked_widget_field',
        ];
    }

    private function get_all_widget_keys(): array
    {
        return array_merge(
            $this->get_free_widget_keys(),
            $this->get_woo_widget_keys()
        );
    }

    private function get_changelog(): array
    {
        return array_slice([
            // ── v1.2.14 ──────────────────────────────────────────────────
            ['type' => 'improvement', 'label' => 'ADMIN UI',      'text' => 'Theme Builder & Header/Footer modals fully redesigned — wider layout, modern purple gradient header, cleaner form fields'],
            ['type' => 'improvement', 'label' => 'ADMIN UI',      'text' => 'Edit button on Header/Footer list now opens the settings popup pre-filled instead of navigating away'],
            ['type' => 'improvement', 'label' => 'FEATURE',       'text' => 'Header Footer Builder: conditional display — choose Display Rules, Exclusion Rules, and User Roles per template (Elementor HFB-style)'],
            ['type' => 'improvement', 'label' => 'FEATURE',       'text' => 'Header Footer Builder: templates now support multiple display conditions — specific rules take priority over global fallback'],
            ['type' => 'improvement', 'label' => 'PERFORMANCE',   'text' => 'Condition matching engine rewritten — archive checks now use queried object instead of unreliable get_post_type() outside the loop'],
            ['type' => 'fix',         'label' => 'FIX',           'text' => 'Theme Builder list no longer shows Header/Footer templates; each list now shows only its own template type'],
            // ── v1.2.13 ──────────────────────────────────────────────────
            ['type' => 'new',         'label' => 'NEW WIDGET',    'text' => 'Glass Card — frosted glass container with backdrop-blur & border-glow effects'],
            ['type' => 'new',         'label' => 'NEW WIDGET',    'text' => 'Sticker Text — animated sticky label element ideal for promotions and highlights'],
            ['type' => 'new',         'label' => 'NEW WIDGET',    'text' => 'Sticky Call Button — floating click-to-call button with pulse animation'],
            ['type' => 'improvement', 'label' => 'IMPROVEMENT',   'text' => 'Before After Image — added touch/swipe support for mobile devices'],
            ['type' => 'improvement', 'label' => 'IMPROVEMENT',   'text' => 'Testimonial Carousel — smooth CSS transitions, 4 new layout skins added'],
            ['type' => 'improvement', 'label' => 'IMPROVEMENT',   'text' => 'Pricing Table — new ribbon style and highlight column support'],
            ['type' => 'fix',         'label' => 'FIX',           'text' => 'Sticky Call Button z-index conflict with Elementor popup overlay'],
            ['type' => 'fix',         'label' => 'FIX',           'text' => 'Countdown Timer accuracy drift on heavily cached WordPress pages'],
        ], 0, 7);
    }
}
