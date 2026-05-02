<?php
namespace PrimeKit\Admin\Inc\Dashboard\VueApp;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers the main admin menu page and serves the Vue SPA.
 */
class VueAdminPage {

    public function __construct() {
        add_action( 'admin_menu',            [ $this, 'register_menu'          ], 20 );
        add_action( 'admin_menu',            [ $this, 'register_widgets_menu'  ], 22 );
        add_action( 'admin_menu',            [ $this, 'register_settings_menu' ], 27 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets'         ] );
        add_action( 'admin_head',            [ $this, 'admin_css'              ] );
        add_action( 'current_screen',        [ $this, 'suppress_notices'       ] );
        add_filter( 'admin_footer_text',     [ $this, 'suppress_footer_text'   ] );
        add_filter( 'update_footer',         [ $this, 'suppress_footer_text'   ], 11 );
        add_filter( 'submenu_file',          [ $this, 'set_active_submenu'     ], 10, 2 );
    }

    /** Highlight the correct submenu item based on pk_route query param. */
    public function set_active_submenu( $submenu_file, $parent_file ) {
        if ( $parent_file !== 'primekit_home' ) return $submenu_file;

        $route = sanitize_text_field( $_GET['pk_route'] ?? '' );

        if ( $route === 'widgets' )  return 'admin.php?page=primekit_home&pk_route=widgets';
        if ( $route === 'settings' ) return 'admin.php?page=primekit_home&pk_route=settings';

        return $submenu_file;
    }

    public function register_menu() {
        add_menu_page(
            __( 'PrimeKit Addons', 'primekit-addons' ),
            __( 'PrimeKit',        'primekit-addons' ),
            'manage_options',
            'primekit_home',
            [ $this, 'render_page' ],
            PRIMEKIT_ADMIN_ASSETS . '/img/primekit-icon.svg',
            22
        );

        $this->remove_menu_separator();
    }

    /** Priority 22 — appears after Header Footer (21) */
    public function register_widgets_menu() {
        add_submenu_page(
            'primekit_home',
            __( 'Widgets', 'primekit-addons' ),
            __( 'Widgets', 'primekit-addons' ),
            'manage_options',
            'admin.php?page=primekit_home&pk_route=widgets'
        );
    }

    /** Priority 27 — appears last, after Theme Builder (25) */
    public function register_settings_menu() {
        add_submenu_page(
            'primekit_home',
            __( 'Settings', 'primekit-addons' ),
            __( 'Settings', 'primekit-addons' ),
            'manage_options',
            'admin.php?page=primekit_home&pk_route=settings'
        );
    }

/** Remove the WP admin sidebar separator around the PrimeKit menu group. */
    private function remove_menu_separator() {
        global $menu;
        foreach ( $menu as $key => $item ) {
            if ( isset( $item[4] ) && $item[4] === 'wp-menu-separator'
                && in_array( $key, [ 20, 21, 23, 25 ], true ) ) {
                unset( $menu[ $key ] );
            }
        }
    }

    public function render_page() {
        echo '<div id="primekit-app"></div>';
    }

    /** Return empty string to hide WP's own footer text on PrimeKit pages. */
    public function suppress_footer_text( $text ) {
        $screen = get_current_screen();
        if ( $screen && strpos( $screen->id, 'primekit' ) !== false ) {
            return '';
        }
        return $text;
    }

    /** Remove all admin notices on PrimeKit pages. */
    public function suppress_notices( $screen ) {
        if ( ! $screen || strpos( $screen->id, 'primekit' ) === false ) return;

        remove_all_actions( 'admin_notices' );
        remove_all_actions( 'all_admin_notices' );
        remove_all_actions( 'network_admin_notices' );
        remove_all_actions( 'user_admin_notices' );
    }

    /** Page-level CSS: strip WP wrapper padding only for the Vue SPA page. */
    public function admin_css() {
        $screen = get_current_screen();
        if ( ! $screen ) return;

        // Full reset only for the Vue SPA (Dashboard / Widgets / Extensions etc.)
        if ( $screen->id === 'toplevel_page_primekit_home' ) {
            ?>
            <style>
                #wpwrap,
                #wpcontent,
                #wpbody,
                #wpbody-content,
                #wpbody-content .wrap {
                    padding:    0 !important;
                    margin-top: 0 !important;
                    border:     none !important;
                }
                #primekit-app {
                    min-height: calc(100vh - 32px);
                    display: flex;
                    flex-direction: column;
                }
            </style>
            <?php
        }
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'primekit' ) === false ) return;

        $dist = plugin_dir_path( PRIMEKIT_FILE ) . 'Admin/dist/assets/';
        $url  = plugin_dir_url( PRIMEKIT_FILE )  . 'Admin/dist/assets/';

        $js_file  = '';
        $css_file = '';

        if ( is_dir( $dist ) ) {
            foreach ( scandir( $dist ) as $file ) {
                if ( str_ends_with( $file, '.js'  ) && str_starts_with( $file, 'index' ) ) $js_file  = $file;
                if ( str_ends_with( $file, '.css' ) && str_starts_with( $file, 'index' ) ) $css_file = $file;
            }
        }

        if ( $js_file ) {
            wp_enqueue_script( 'primekit-vue-admin', $url . $js_file, [], PRIMEKIT_VERSION, true );
        }
        if ( $css_file ) {
            wp_enqueue_style( 'primekit-vue-admin', $url . $css_file, [], PRIMEKIT_VERSION );
        }

        wp_localize_script( 'primekit-vue-admin', 'primekitAdmin', [
            'restUrl'      => rest_url( 'primekit/v1' ),
            'nonce'        => wp_create_nonce( 'wp_rest' ),
            'version'      => PRIMEKIT_VERSION,
            'totalWidgets' => 70,
            'siteUrl'      => get_site_url(),
            'adminUrl'     => admin_url(),
            'iconsUrl'     => PRIMEKIT_ADMIN_ASSETS . '/img/icons/',
        ] );
    }
}
