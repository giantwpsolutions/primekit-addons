<?php
/**
 * ThemeBuilder.php
 *
 * This file contains the ThemeBuilder class, which is responsible for handling
 * the display of Elementor templates in the page templates.
 *
 * @package PrimeKit\Admin\Inc\ThemeBuilder
 * @since 1.0.0
 */

namespace PrimeKit\Admin\Inc\ThemeBuilder;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

use PrimeKit\Admin\Inc\ThemeBuilder\Classes\PostTypes;
use PrimeKit\Admin\Inc\ThemeBuilder\Classes\MetaBox;
use PrimeKit\Admin\Inc\ThemeBuilder\Classes\ModalMarkup;
use PrimeKit\Admin\Inc\ThemeBuilder\Admin\ConditionManager;
use PrimeKit\Admin\Inc\ThemeBuilder\Inc\Hooks\TemplateContentHooks;
use PrimeKit\Admin\Inc\ThemeBuilder\Classes\TemplateOverride;
use PrimeKit\Admin\Inc\ThemeBuilder\Admin\Menus;
use PrimeKit\Admin\Inc\ThemeBuilder\Admin\Column;

/**
 * ThemeBuilder
 *
 * Handles the display of Elementor templates in the page templates.
 *
 * @package PrimeKit\Admin\Inc\ThemeBuilder
 * @since 1.0.0
 */
class ThemeBuilder
{
    protected $post_types;

    protected $meta_box;

    protected $modal_markup;

    protected $condition_manager;

    protected $template_content_hooks;

    protected $template_override;

    protected $menus;
    protected $column;
    /**
     * Theme Builder constructor.
     *
     * Initializes the class, registers the hooks.
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        $this->setConstants(); // Set the constants.

        $this->classes_initialize(); // Initialize the class.


        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

        add_action('wp_ajax_primekit_library_new_post', array($this, 'handle_new_template_submission'));
        add_action('single_template', array($this, 'load_canvas_template'));

        add_action('get_header', array($this, 'primekit_override_header'));
        add_action('get_footer', [$this, 'primekit_override_footer']);
        add_action('primekit_footer', [$this, 'primekit_render_footer']);
        add_action('primekit_header', [$this, 'primekit_render_header']);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_elementor_styles_scripts'));

        add_action('template_redirect', array($this, 'primekit_override_shop_archive'), 1);
        add_action('template_redirect', array($this, 'primekit_override_shop_single'), 1);


    }


    /**
     * Sets constants for the plugin.
     *
     * @since 1.0.0
     */
    public function setConstants()
    {
        define('PRIMEKIT_TB_PATH', plugin_dir_path(__FILE__));
        define('PRIMEKIT_TB_ASSETS', plugin_dir_url(__FILE__) . 'Assets/');
    }

    /**
     * Override the header template.
     *
     * If a header template is assigned in the theme builder, this function will
     * load that template instead of the default header.php.
     *
     * @since 1.0.0
     */
    public function primekit_override_header()
    {
        // Skip override inside Elementor editor preview
        if (isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor')) {
            return;
        }

        if (self::get_header_footer_template_id('header')) {
            require_once PRIMEKIT_TB_PATH . 'Inc/Templates/primekit-header.php';
            $templates = [];
            $templates[] = 'header.php';
            remove_all_actions('wp_head');
            ob_start();
            locate_template($templates, true);
            ob_get_clean();
        }
    }


    /**
     * Render the header template.
     *
     * This method is responsible for rendering the header template which is set in the elementor page settings.
     * It is hooked in the `primekit_header` action.
     *
     * @since 1.0.0
     */
    public function primekit_render_header()
    {
        ?>
        <header class="primekit-custom-header dynamic-header">
            <div class="primekit-container">
                <?php echo self::get_header_content(); ?>
            </div>
        </header>
        <?php
    }

    /**
     * Retrieves the ID of the header template.
     *
     * @since 1.0.0
     * @return int|false The ID of the header template, or false if not found.
     */
    public static function primekit_get_header_id()
    {
        $header_id = self::get_header_footer_template_id('header');

        if ('' === $header_id) {
            $header_id = false;
        }

        return apply_filters('primekit_get_header_id', $header_id);
    }


    /**
     * Gets the ID of a template by type.
     *
     * @param string $type The type of template to retrieve (e.g. 'header', 'footer', etc).
     *
     * @return int|string The ID of the template, or an empty string if no template is found.
     */
    public static function get_template_id($type)
    {
        $args = [
            'post_type'      => 'primekit_library',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];
        $primekit_hf_templates = get_posts($args);

        foreach ($primekit_hf_templates as $template) {
            if (get_post_meta(absint($template->ID), 'primekit_themebuilder_select', true) === $type) {
                return $template->ID;
            }
        }

        return '';
    }

    /**
     * Gets the best-matching header or footer template ID, respecting display conditions.
     * Specific conditions win over "entire_site". Falls back to the first entire_site template.
     *
     * @param string $type 'header' or 'footer'
     * @return int|string Post ID or empty string.
     */
    public static function get_header_footer_template_id($type)
    {
        $templates = get_posts([
            'post_type'      => 'primekit_library',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => [[
                'key'   => 'primekit_themebuilder_select',
                'value' => $type,
            ]],
        ]);

        if (empty($templates)) return '';

        $fallback = '';
        foreach ($templates as $template) {
            $id = $template->ID;

            $display_rules   = get_post_meta($id, 'primekit_hf_display_rules',   true);
            $exclusion_rules = get_post_meta($id, 'primekit_hf_exclusion_rules',  true);
            $user_roles      = get_post_meta($id, 'primekit_hf_user_roles',       true);

            // Backward-compat: old single-condition string → wrap in array
            if (!is_array($display_rules)) {
                $legacy        = get_post_meta($id, 'primekit_hf_condition', true) ?: 'basic-global';
                $display_rules = [$legacy];
            }
            if (!is_array($exclusion_rules)) $exclusion_rules = [];
            if (!is_array($user_roles))      $user_roles      = [];

            // User role gate
            if (!self::check_user_role($user_roles)) continue;

            // Exclusion rules — skip if current page is excluded
            if (!empty($exclusion_rules) && self::matches_any_condition($exclusion_rules)) continue;

            // Entire website / empty → global fallback candidate
            $is_global = empty($display_rules) ||
                         in_array('basic-global', $display_rules) ||
                         in_array('entire_site',  $display_rules); // backward compat

            if ($is_global) {
                if (!$fallback) $fallback = $id;
                continue;
            }

            // Specific rules — use if any match
            if (self::matches_any_condition($display_rules)) {
                return $id;
            }
        }

        return $fallback;
    }

    /**
     * Returns true if the current page matches any of the given conditions.
     */
    public static function matches_any_condition(array $conditions)
    {
        foreach ($conditions as $condition) {
            if (self::check_hf_condition($condition)) return true;
        }
        return false;
    }

    /**
     * Returns true if the current user satisfies the user-role rule list.
     * Empty list or 'all' → visible to everyone.
     */
    public static function check_user_role(array $user_roles)
    {
        if (empty($user_roles) || in_array('all', $user_roles)) return true;

        // Support both dash (HFB) and underscore (legacy) variants
        if ((in_array('logged-in', $user_roles)  || in_array('logged_in', $user_roles))  && is_user_logged_in())  return true;
        if ((in_array('logged-out', $user_roles) || in_array('logged_out', $user_roles)) && !is_user_logged_in()) return true;

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            foreach ($user_roles as $role) {
                if (in_array($role, (array) $current_user->roles)) return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the current page matches the given display condition.
     * Supports both new HFB-style keys and legacy keys for backward compatibility.
     */
    public static function check_hf_condition($condition)
    {
        // Handle pipe-delimited post-type rules: post|all, post|all|archive, page|all, etc.
        if (strpos($condition, '|') !== false) {
            $parts     = explode('|', $condition);
            $post_type = $parts[0] ?? '';
            $arch_type = $parts[2] ?? '';
            $taxonomy  = $parts[3] ?? '';

            if ($arch_type === '') {
                // e.g. post|all  →  any singular of that post type
                return is_singular($post_type);
            }

            if ($arch_type === 'archive') {
                if (!is_archive()) return false;

                // Explicit post-type archive (e.g. /blog/ CPT archive)
                if (is_post_type_archive($post_type)) return true;

                // Taxonomy archives: check if the taxonomy object type includes the post type
                $obj = get_queried_object();
                if ($obj instanceof \WP_Term) {
                    $tax = get_taxonomy($obj->taxonomy);
                    if ($tax && in_array($post_type, (array) $tax->object_type, true)) return true;
                }

                // Date / author archives default to 'post'
                if (is_date() || is_author()) {
                    global $wp_query;
                    $qpt = $wp_query->get('post_type');
                    return $post_type === 'post' && (empty($qpt) || $qpt === 'post');
                }

                return false;
            }

            if ($arch_type === 'taxarchive' && $taxonomy) {
                // e.g. post|all|taxarchive|category
                if (!is_archive()) return false;
                $obj = get_queried_object();
                return isset($obj->taxonomy) && $obj->taxonomy === $taxonomy;
            }

            return false;
        }

        switch ($condition) {
            /* ── New HFB-style keys ── */
            case 'basic-global':      return true;
            case 'basic-singulars':   return is_singular();
            case 'basic-archives':    return is_archive();
            case 'special-404':       return is_404();
            case 'special-search':    return is_search();
            case 'special-blog':      return is_home();
            case 'special-front':     return is_front_page();
            case 'special-date':      return is_date();
            case 'special-author':    return is_author();
            case 'special-woo-shop':  return function_exists('is_shop') && is_shop();

            /* ── Legacy keys (backward compat) ── */
            case 'entire_site':   return true;
            case 'front_page':    return is_front_page();
            case 'single_post':   return is_single() && 'post' === get_post_type();
            case 'single_page':   return is_page();
            case 'archive':       return is_archive();
            case 'search':        return is_search();
            case '404':           return is_404();
            case 'shop_single':   return function_exists('is_product') && is_product();
            case 'shop_archive':  return function_exists('is_shop') && is_shop();

            default:              return false;
        }
    }
    /**
     * Retrieve the content of the header template.
     *
     * If a header template is assigned in the theme builder, this function will
     * load that template and echo its content.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function get_header_content()
    {
        $primekit_get_header_id = self::primekit_get_header_id();
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($primekit_get_header_id);
    }

    /**
     * Overrides the footer template for the current request.
     *
     * If a footer template is assigned in the theme builder, this function will
     * load that template instead of the default footer.php.
     *
     * @since 1.0.0
     */
    public function primekit_override_footer()
    {
        // Skip override inside Elementor editor preview
        if (isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor')) {
            return;
        }

        if (self::get_header_footer_template_id('footer')) {
            require_once PRIMEKIT_TB_PATH . 'Inc/Templates/primekit-footer.php';
            $templates = [];
            $templates[] = 'footer.php';
            remove_all_actions('wp_footer');
            ob_start();
            locate_template($templates, true);
            ob_get_clean();
        }
    }

    /**
     * Renders the footer template.
     *
     * This method is used to render the footer template, which is a custom
     * footer template that is used by the theme builder.
     *
     * @since 1.0.0
     */
    public function primekit_render_footer()
    {
        ?>
        <footer class="primekit-custom-footer">
            <div class="primekit-container">
                <?php echo self::get_footer_content(); ?>
            </div>
        </footer>
        <?php
    }

    /**
     * Retrieves the content of the footer template.
     *
     * If a footer template is assigned in the theme builder, this function will
     * load that template and echo its content.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function get_footer_content()
    {
        $primekit_get_footer_id = self::primekit_get_footer_id();
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($primekit_get_footer_id);
    }


    /**
     * Retrieve the ID of the footer template.
     *
     * @since 1.0.0
     * @return int|false The ID of the footer template, or false if not found.
     */
    public static function primekit_get_footer_id()
    {
        $footer_id = self::get_header_footer_template_id('footer');

        if ('' === $footer_id) {
            $footer_id = false;
        }

        return apply_filters('primekit_get_footer_id', $footer_id);
    }


    /**
     * Enqueues Elementor styles and scripts on the frontend, and the custom styles for the theme builder.
     *
     * Checks if Elementor is loaded before enqueueing its styles and scripts. Then enqueues the custom styles for the theme builder.
     */
    public function enqueue_elementor_styles_scripts()
    {
        //     if (did_action('elementor/loaded')) {
        //        \Elementor\Plugin::instance()->frontend->enqueue_styles();
        //        \Elementor\Plugin::instance()->frontend->enqueue_scripts();
        //    }

        wp_enqueue_style('primekit-theme-builder-style', PRIMEKIT_TB_ASSETS . 'css/style.css');
    }

    public function classes_initialize()
    {
        $this->post_types = new PostTypes();
        $this->menus = new Menus();
        $this->column = new Column();
        $this->modal_markup = new ModalMarkup();
        $this->condition_manager = new ConditionManager();
        $this->template_content_hooks = new TemplateContentHooks();
        $this->template_override = new TemplateOverride();
        $this->meta_box = new MetaBox();
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * Enqueues the plugin's admin styles and scripts, including the modal CSS and
     * JavaScript, as well as the new template submission JavaScript.
     *
     * @since 1.0.0
     *
     * @param string $hook The current admin screen.
     */
    public function admin_scripts($hook)
    {

        if (in_array($hook, array('edit.php', 'post-new.php'))) {
            $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
            if ('primekit_library' === $post_type || ('post-new.php' === $hook && empty($post_type))) {

                // Enqueue CSS for modal
                wp_enqueue_style(
                    'primekit-theme-builder-modal',
                    PRIMEKIT_TB_ASSETS . 'css/modal.css',
                    array(),
                    PRIMEKIT_VERSION,
                    'all'
                );

                // Enqueue main admin JS (wp-util provides wp.template)
                wp_enqueue_script(
                    'primekit-theme-builder-main',
                    PRIMEKIT_TB_ASSETS . 'js/admin.js',
                    array('jquery', 'wp-util'),
                    PRIMEKIT_VERSION,
                    true
                );

                // Enqueue AJAX script for new template
                wp_enqueue_script(
                    'primekit-tb-modal-ajax',
                    PRIMEKIT_TB_ASSETS . 'js/new-template-ajax.js',
                    array('jquery'),
                    PRIMEKIT_VERSION,
                    true
                );

                // Enqueue external micromodal JS
                wp_enqueue_script(
                    'micromodal-js',
                    PRIMEKIT_TB_ASSETS . 'js/micromodal.min.js',
                    array('jquery'),
                    '0.4.10',
                    true
                );

                wp_localize_script('primekit-tb-modal-ajax', 'primekitNewTemplateCreated', [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce'   => wp_create_nonce('primekit_new_template_nonce'),
                ]);

                // Localize all H/F template data so the edit modal can be pre-filled
                $hf_data = [
                    '_strings' => [
                        'addNew'       => __('Add New Template', 'primekit-addons'),
                        'editTemplate' => __('Edit Template', 'primekit-addons'),
                    ],
                ];
                $hf_posts = get_posts([
                    'post_type'      => 'primekit_library',
                    'posts_per_page' => -1,
                    'post_status'    => ['publish', 'draft'],
                    'meta_query'     => [[
                        'key'     => 'primekit_themebuilder_select',
                        'value'   => ['header', 'footer'],
                        'compare' => 'IN',
                    ]],
                ]);
                foreach ($hf_posts as $tpl) {
                    $dr = get_post_meta($tpl->ID, 'primekit_hf_display_rules',   true);
                    $er = get_post_meta($tpl->ID, 'primekit_hf_exclusion_rules',  true);
                    $ur = get_post_meta($tpl->ID, 'primekit_hf_user_roles',       true);
                    $cv = get_post_meta($tpl->ID, 'primekit_hf_canvas',           true);
                    if (!is_array($dr)) {
                        $legacy = get_post_meta($tpl->ID, 'primekit_hf_condition', true);
                        $dr     = $legacy ? [$legacy] : [];
                    }
                    if (!is_array($er)) $er = [];
                    if (!is_array($ur)) $ur = [];
                    $hf_data[$tpl->ID] = [
                        'id'             => $tpl->ID,
                        'title'          => $tpl->post_title,
                        'type'           => get_post_meta($tpl->ID, 'primekit_themebuilder_select', true),
                        'displayRules'   => array_values($dr),
                        'exclusionRules' => array_values($er),
                        'userRoles'      => array_values($ur),
                        'canvas'         => $cv === '1',
                    ];
                }
                wp_localize_script('primekit-theme-builder-main', 'primekitHFTemplates', $hf_data);
            }
        }
    }


    /**
     * Handles the submission of the new template modal form.
     *
     * Expects the following $_POST variables:
     * - postTitle: The title of the new post.
     * - templateType: The type of template to create (e.g. 'header', 'footer', etc.).
     * - postType: The post type to create (defaults to 'primekit_library' if not provided).
     *
     * Creates a new post with the given title and type, and updates the custom field
     * with the template type. If Elementor is installed and active, sets the Elementor
     * page template to 'elementor_canvas' for Header and Footer types, or
     * 'elementor_header_footer' for other types.
     *
     * @since 1.0.0
     */
    public function handle_new_template_submission()
    {
        check_ajax_referer('primekit_new_template_nonce', 'security');

        $post_title    = sanitize_text_field($_POST['postTitle']);
        $template_type = sanitize_text_field($_POST['templateType']);
        $post_type     = isset($_POST['postType']) ? sanitize_text_field($_POST['postType']) : 'primekit_library';
        $edit_post_id  = isset($_POST['editPostId']) ? intval($_POST['editPostId']) : 0;
        $save_only     = isset($_POST['saveOnly']) && $_POST['saveOnly'] === '1';

        // ── Update existing post ──────────────────────────────────────────
        if ($edit_post_id && get_post($edit_post_id)) {
            wp_update_post(['ID' => $edit_post_id, 'post_title' => $post_title]);
            update_post_meta($edit_post_id, 'primekit_themebuilder_select', $template_type);

            $display_rules   = isset($_POST['displayRules'])   ? json_decode(sanitize_text_field($_POST['displayRules']),   true) : [];
            $exclusion_rules = isset($_POST['exclusionRules']) ? json_decode(sanitize_text_field($_POST['exclusionRules']), true) : [];
            $user_roles      = isset($_POST['userRoles'])      ? json_decode(sanitize_text_field($_POST['userRoles']),      true) : [];
            $canvas          = isset($_POST['canvasTemplate']) && $_POST['canvasTemplate'] === '1' ? '1' : '';

            update_post_meta($edit_post_id, 'primekit_hf_display_rules',   is_array($display_rules)   ? $display_rules   : []);
            update_post_meta($edit_post_id, 'primekit_hf_exclusion_rules', is_array($exclusion_rules) ? $exclusion_rules : []);
            update_post_meta($edit_post_id, 'primekit_hf_user_roles',      is_array($user_roles)      ? $user_roles      : []);
            update_post_meta($edit_post_id, 'primekit_hf_canvas',          $canvas);

            if ($save_only) {
                $redirect = admin_url('edit.php?post_type=primekit_library&primekit_filter=hf');
            } elseif (did_action('elementor/loaded')) {
                $redirect = admin_url("post.php?post={$edit_post_id}&action=elementor");
            } else {
                $redirect = get_edit_post_link($edit_post_id, '');
            }

            wp_send_json_success(['redirect_url' => $redirect]);
            exit;
        }

        // ── Create new post ───────────────────────────────────────────────
        $post_id = wp_insert_post([
            'post_title' => $post_title,
            'post_status' => 'draft',
            'post_type' => $post_type,
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            // Update the custom field with the template type
            update_post_meta($post_id, 'primekit_themebuilder_select', $template_type);

            // Save display/exclusion/user-role conditions and canvas flag for header/footer
            if (in_array($template_type, ['header', 'footer'])) {
                $display_rules   = isset($_POST['displayRules'])   ? json_decode(sanitize_text_field($_POST['displayRules']),   true) : [];
                $exclusion_rules = isset($_POST['exclusionRules']) ? json_decode(sanitize_text_field($_POST['exclusionRules']), true) : [];
                $user_roles      = isset($_POST['userRoles'])      ? json_decode(sanitize_text_field($_POST['userRoles']),      true) : [];
                $canvas          = isset($_POST['canvasTemplate'])  && $_POST['canvasTemplate'] === '1' ? '1' : '';

                update_post_meta($post_id, 'primekit_hf_display_rules',   is_array($display_rules)   ? $display_rules   : []);
                update_post_meta($post_id, 'primekit_hf_exclusion_rules', is_array($exclusion_rules) ? $exclusion_rules : []);
                update_post_meta($post_id, 'primekit_hf_user_roles',      is_array($user_roles)      ? $user_roles      : []);
                update_post_meta($post_id, 'primekit_hf_canvas',          $canvas);
            }

            // Check if Elementor is installed and active
            if (did_action('elementor/loaded')) {
                $use_canvas = in_array($template_type, ['header', 'footer']) ||
                              (isset($_POST['canvasTemplate']) && $_POST['canvasTemplate'] === '1');
                update_post_meta($post_id, '_wp_page_template', $use_canvas ? 'elementor_canvas' : 'elementor_header_footer');

                $edit_with_elementor_url = admin_url("post.php?post={$post_id}&action=elementor");
                wp_send_json_success(['redirect_url' => $edit_with_elementor_url]);
            } else {
                wp_send_json_success(['redirect_url' => get_edit_post_link($post_id, '')]);
            }
        } else {
            wp_send_json_error(['message' => 'Failed to create post.']);
        }

        exit;
    }

    /**
     * Override the page template for the primekit_library post type.
     *
     * We want to use Elementor's canvas template for the Theme Builder templates.
     *
     * @param string $single_template The path to the template.
     * @return string The path to the template.
     */
    public function load_canvas_template($single_template)
    {
        global $post;

        if ('primekit_library' == $post->post_type) {
            $elementor_2_0_canvas = ELEMENTOR_PATH . '/modules/page-templates/templates/canvas.php';

            if (file_exists($elementor_2_0_canvas)) {
                return $elementor_2_0_canvas;
            } else {
                return ELEMENTOR_PATH . '/includes/page-templates/canvas.php';
            }
        }

        return $single_template;
    }

    /**
     * Determines whether the given template type should be displayed or not.
     *
     * @since 1.0.0
     *
     * @param string $template_type The type of template to check. Can be one of:
     *     single_post, single_page, archive, 404, search, etc.
     *
     * @return bool True if the template should be displayed, false otherwise.
     */
    public static function should_display_template($template_type)
    {
        // Get the template ID based on the type (single_post, single_page, etc.)
        $template_id = self::get_template_id($template_type);


        if (!$template_id) {
            return false;
        }

        // Get the template type selected in `primekit_library`
        $selected_template = get_post_meta($template_id, 'primekit_themebuilder_select', true);

        if ($selected_template === $template_type) {
            return true;
        }

        return false;
    }

    /**
     * Overrides the shop archive template.
     *
     * If a shop archive template is assigned in the theme builder, this function will
     * load that template instead of the default shop archive template.
     *
     * @since 1.0.0
     */
    public function primekit_override_shop_archive()
    {
        if (function_exists('is_shop') && function_exists('is_product_taxonomy') && (is_shop() || is_product_taxonomy())) {
            if (self::should_display_template('shop_archive')) {
                add_filter('template_include', function () {
                    return PRIMEKIT_TB_PATH . 'Inc/Templates/primekit-shop-archive.php';
                }, 99);
            }
        }
    }

    /**
     * Overrides the shop single template.
     *
     * If a shop single template is assigned in the theme builder, this function will
     * load that template instead of the default shop single template.
     *
     * @since 1.0.0
     */
    public function primekit_override_shop_single()
    {
        if (is_singular('product')) {
            if (self::should_display_template('shop_single')) {
                add_filter('template_include', function () {
                    return PRIMEKIT_TB_PATH . 'Inc/Templates/primekit-shop-single.php';
                }, 99);
            }
        }
    }

    /**
     * Retrieves the shop archive content.
     *
     * If a shop archive template is assigned in the theme builder, this function will
     * load that template and echo its content.
     *
     * @since 1.0.0
     */
    public static function get_shop_archive_content()
    {
        $template_id = self::get_template_id('shop_archive');
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id);
    }

    /**
     * Retrieves the shop single content.
     *
     * If a shop single template is assigned in the theme builder, this function will
     * load that template and echo its content.
     *
     * @since 1.0.6
     */
    public static function get_shop_single_content()
    {
        $template_id = self::get_template_id('shop_single');
        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($template_id);
    }

}


