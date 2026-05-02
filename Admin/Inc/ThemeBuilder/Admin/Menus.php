<?php
/**
 * Menus.php
 *
 * This file contains the Menus class, which is responsible for adding
 * the custom meta box to the Theme Builder page and saving the meta box data.
 *
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Admin
 * @since 1.0.0
 */

namespace PrimeKit\Admin\Inc\ThemeBuilder\Admin;

if (!defined('ABSPATH'))
    exit;

/**
 * Class Menus
 * 
 * Handles the addition of the custom meta box to the Theme Builder page and saving the meta box data.
 * 
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Admin
 * @since 1.0.0
 */
class Menus
{

    public function __construct()
    {
        add_action('admin_menu',   array($this, 'add_header_footer_submenu'), 21);
        add_action('admin_menu',   array($this, 'add_themebuilder_submenu'), 25);
        add_action('pre_get_posts', array($this, 'filter_header_footer_posts'));
        add_action('pre_get_posts', array($this, 'filter_theme_builder_posts'));
        add_filter('submenu_file', array($this, 'set_header_footer_active'), 10, 2);
        add_action('admin_head',   array($this, 'override_hf_page_title'));
    }

    /**
     * On the Header Footer list page, override the H1 heading and browser tab title
     * so they read "Header and Footer" instead of "Theme Builder".
     */
    public function override_hf_page_title()
    {
        if (empty($_GET['post_type']) || $_GET['post_type'] !== 'primekit_library') return;
        if (empty($_GET['primekit_filter']) || $_GET['primekit_filter'] !== 'hf') return;
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Change visible H1 heading
            var h1 = document.querySelector('.wrap h1.wp-heading-inline');
            if (h1) {
                var node = h1.firstChild;
                if (node && node.nodeType === 3) node.textContent = 'Header and Footer ';
            }
            // Change browser tab title
            document.title = document.title.replace('Theme Builder', 'Header and Footer');
        });
        </script>
        <?php
    }

    /**
     * Highlight "Header Footer" submenu when the hf filter is active,
     * and keep "Theme Builder" highlighted otherwise.
     */
    public function set_header_footer_active( $submenu_file, $parent_file ) {
        if ( $parent_file !== 'primekit_home' ) return $submenu_file;

        if ( ! empty( $_GET['post_type'] ) && $_GET['post_type'] === 'primekit_library' ) {
            if ( ! empty( $_GET['primekit_filter'] ) && $_GET['primekit_filter'] === 'hf' ) {
                return 'edit.php?post_type=primekit_library&primekit_filter=hf';
            }
            return 'edit.php?post_type=primekit_library';
        }

        return $submenu_file;
    }

    /**
     * Adds the Theme Builder submenu to the PrimeKit menu.
     * 
     * This function adds a new submenu page to the PrimeKit menu, linking to the
     * post type 'primekit_library' (Theme Builder).
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function add_themebuilder_submenu()
    {
        
        add_submenu_page(
            'primekit_home',                   // Parent slug
            __('Theme Builder', 'primekit-addons'), // Page title
            __('Theme Builder', 'primekit-addons'), // Menu title
            'manage_options',                       // Capability
            'edit.php?post_type=primekit_library'   // Menu slug (linking to post type)
        );
    }

    /**
     * Modifies the Theme Builder menu.
     * 
     * This function removes the default Theme Builder menu and modifies the
     * submenu page to be the default page for the post type 'primekit_library'.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function add_header_footer_submenu()
    {
        add_submenu_page(
            'primekit_home',
            __('Header Footer', 'primekit-addons'),
            __('Header Footer', 'primekit-addons'),
            'manage_options',
            'edit.php?post_type=primekit_library&primekit_filter=hf'
        );
    }

    /** Filter the HF list to show ONLY header/footer templates. */
    public function filter_header_footer_posts( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) return;
        if ( empty( $_GET['primekit_filter'] ) || $_GET['primekit_filter'] !== 'hf' ) return;
        if ( empty( $_GET['post_type'] ) || $_GET['post_type'] !== 'primekit_library' ) return;

        $query->set( 'meta_query', [ [
            'key'     => 'primekit_themebuilder_select',
            'value'   => [ 'header', 'footer' ],
            'compare' => 'IN',
        ] ] );
    }

    /** Filter the Theme Builder list to EXCLUDE header/footer templates. */
    public function filter_theme_builder_posts( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) return;
        if ( ! empty( $_GET['primekit_filter'] ) ) return; // HF filter handles that page
        if ( empty( $_GET['post_type'] ) || $_GET['post_type'] !== 'primekit_library' ) return;

        $query->set( 'meta_query', [
            'relation' => 'OR',
            [
                'key'     => 'primekit_themebuilder_select',
                'value'   => [ 'header', 'footer' ],
                'compare' => 'NOT IN',
            ],
            [
                'key'     => 'primekit_themebuilder_select',
                'compare' => 'NOT EXISTS',
            ],
        ] );
    }

    public function modify_theme_builder_menu()
    {
        global $submenu;


       // remove_menu_page('edit.php?post_type=primekit_library');

    

        if (isset($submenu['primekit_home'])) {
            foreach ($submenu['primekit_home'] as $key => $menu_item) {
                if ($menu_item[2] === 'edit.php?post_type=primekit_library') {
                    $current_file = basename($_SERVER['PHP_SELF']);
                    if ($current_file === 'post-new.php' && isset($_GET['post_type']) === 'primekit_library') {
                        // Highlight the new submenu as active when adding a new post
                        $submenu['primekit_home'][$key][4] = 'current';
                    } elseif ($current_file === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'primekit_library') {
                        // Highlight the new submenu as active when on the edit posts screen
                        $submenu['primekit_home'][$key][4] = 'current';
                    }
                }
            }
        }
    }
}