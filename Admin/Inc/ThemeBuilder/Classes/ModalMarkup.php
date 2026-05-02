<?php
/**
 * ModalMarkup.php
 *
 * This file contains the ModalMarkup class, which is responsible for printing the
 * modal markup used for adding new templates to the Theme Builder.
 *
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Classes
 * @since 1.0.0
 */

namespace PrimeKit\Admin\Inc\ThemeBuilder\Classes;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Class ModalMarkup
 * 
 * Handles the printing of the modal markup used for adding new templates to the Theme Builder.
 * 
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Classes
 * @since 1.0.0
 */
class ModalMarkup
{
    public function __construct()
    {
        add_action('admin_footer', array($this, 'print_modal_markup'));
        // add_action('elementor/editor/footer', array($this, 'template_editor_modal_markup'));
        add_action('admin_post_primekit_save_template', array($this, 'save_template_meta'));
    }

    /**
     * Returns grouped <option> HTML for the display-condition select (matches Elementor HFB structure).
     */
    public static function get_condition_options_html() {
        $h  = '<option value="">' . esc_html__( 'Select', 'primekit-addons' ) . '</option>';

        $h .= '<optgroup label="' . esc_attr__( 'Basic', 'primekit-addons' ) . '">';
        $h .= '<option value="basic-global">'    . esc_html__( 'Entire Website',  'primekit-addons' ) . '</option>';
        $h .= '<option value="basic-singulars">' . esc_html__( 'All Singulars',   'primekit-addons' ) . '</option>';
        $h .= '<option value="basic-archives">'  . esc_html__( 'All Archives',    'primekit-addons' ) . '</option>';
        $h .= '</optgroup>';

        $h .= '<optgroup label="' . esc_attr__( 'Special Pages', 'primekit-addons' ) . '">';
        $h .= '<option value="special-404">'    . esc_html__( '404 Page',          'primekit-addons' ) . '</option>';
        $h .= '<option value="special-search">' . esc_html__( 'Search Page',       'primekit-addons' ) . '</option>';
        $h .= '<option value="special-blog">'   . esc_html__( 'Blog / Posts Page', 'primekit-addons' ) . '</option>';
        $h .= '<option value="special-front">'  . esc_html__( 'Front Page',        'primekit-addons' ) . '</option>';
        $h .= '<option value="special-date">'   . esc_html__( 'Date Archive',      'primekit-addons' ) . '</option>';
        $h .= '<option value="special-author">' . esc_html__( 'Author Archive',    'primekit-addons' ) . '</option>';
        if ( class_exists( 'WooCommerce' ) ) {
            $h .= '<option value="special-woo-shop">' . esc_html__( 'WooCommerce Shop Page', 'primekit-addons' ) . '</option>';
        }
        $h .= '</optgroup>';

        $h .= '<optgroup label="' . esc_attr__( 'Posts', 'primekit-addons' ) . '">';
        $h .= '<option value="post|all">'         . esc_html__( 'All Posts',         'primekit-addons' ) . '</option>';
        $h .= '<option value="post|all|archive">' . esc_html__( 'All Post Archives', 'primekit-addons' ) . '</option>';
        $h .= '</optgroup>';

        $h .= '<optgroup label="' . esc_attr__( 'Pages', 'primekit-addons' ) . '">';
        $h .= '<option value="page|all">' . esc_html__( 'All Pages', 'primekit-addons' ) . '</option>';
        $h .= '</optgroup>';

        if ( class_exists( 'WooCommerce' ) ) {
            $h .= '<optgroup label="' . esc_attr__( 'WooCommerce', 'primekit-addons' ) . '">';
            $h .= '<option value="product|all">'         . esc_html__( 'All Products',         'primekit-addons' ) . '</option>';
            $h .= '<option value="product|all|archive">' . esc_html__( 'All Product Archives', 'primekit-addons' ) . '</option>';
            $h .= '</optgroup>';
        }

        return $h;
    }

    /**
     * Returns grouped <option> HTML for the user-role select.
     */
    public static function get_user_role_options_html() {
        $h  = '<option value="">' . esc_html__( 'Select', 'primekit-addons' ) . '</option>';

        $h .= '<optgroup label="' . esc_attr__( 'Basic', 'primekit-addons' ) . '">';
        $h .= '<option value="all">'        . esc_html__( 'All',        'primekit-addons' ) . '</option>';
        $h .= '<option value="logged-in">'  . esc_html__( 'Logged In',  'primekit-addons' ) . '</option>';
        $h .= '<option value="logged-out">' . esc_html__( 'Logged Out', 'primekit-addons' ) . '</option>';
        $h .= '</optgroup>';

        $h .= '<optgroup label="' . esc_attr__( 'Advanced', 'primekit-addons' ) . '">';
        foreach ( get_editable_roles() as $slug => $data ) {
            $h .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $data['name'] ) . '</option>';
        }
        $h .= '</optgroup>';

        return $h;
    }

    /**
     * Prints the modal markup for adding new templates.
     * 
     * This function checks if the current screen is the Theme Builder page and
     * prints the modal markup if it is.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function print_modal_markup()
    {
        $screen = get_current_screen();
        if ('edit-primekit_library' !== $screen->id) return;

        $icon_url = esc_url( plugin_dir_url( __FILE__ ) . '../Assets/img/addons-icon.svg' );
        ?>

        <!-- Theme Builder Modal (shown on Theme Builder page) -->
        <div class="modal micromodal-slide primekit-theme-builder-modal-area" id="primekit-tb-modal" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1">
                <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="primekit-tb-modal-title">
                    <header class="modal__header primekit-modal-header">
                        <h2 class="modal__title primekit-modal-heading" id="primekit-tb-modal-title">
                            <img src="<?php echo $icon_url; ?>" alt="">
                            <?php esc_html_e( 'PrimeKit Theme Builder', 'primekit-addons' ); ?>
                        </h2>
                        <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                    </header>
                    <main class="modal__content primekit-theme-builder-modal-content" id="primekit-tb-modal-content">
                        <div class="primekit-tb-modal-content-area">
                            <div class="primekit-tb-modal-content-left">
                                <h2><?php esc_html_e( "Design Your Website's Theme Easily with PrimeKit", 'primekit-addons' ); ?></h2>
                                <p><?php esc_html_e( "PrimeKit's Theme Builder makes it simple to design your website's single pages, posts, archives, and WooCommerce product pages. Enjoy a smooth and user-friendly experience to build your site exactly the way you want, right within Elementor!", 'primekit-addons' ); ?></p>
                            </div>
                            <div class="primekit-tb-modal-content-right primekit-tb-modal-content-form">
                                <div class="primekit-tb-modal-content-form-heading">
                                    <h2><?php esc_html_e( 'Choose a Template Type', 'primekit-addons' ); ?></h2>
                                    <p class="primekit-tb-modal-content-form-note">
                                        <?php esc_html_e( 'Choose a template that best fits your needs.', 'primekit-addons' ); ?>
                                    </p>
                                </div>
                                <div class="primekit-tb-modal-content-form-fields">
                                    <form id="primekit-tb-modal-template-form" method="post"
                                        action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                        <input type="hidden" id="primekit-tb-post-type" name="post_type" value="primekit_library">
                                        <input type="hidden" name="action" value="primekit_save_template">
                                        <input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                                        <?php wp_nonce_field( 'primekit_tb_modal_action', 'primekit_tb_modal_nonce' ); ?>
                                        <div class="primekit-tb-modal-single-field">
                                            <label for="primekit-tb-modal-select-template-type"><?php esc_html_e( 'Template Type', 'primekit-addons' ); ?></label>
                                            <select name="primekit-tb-modal-select" id="primekit-tb-modal-select-template-type">
                                                <option value=""><?php esc_html_e( 'Select...', 'primekit-addons' ); ?></option>
                                                <option value="single_post"><?php esc_html_e( 'Single Post', 'primekit-addons' ); ?></option>
                                                <option value="single_page"><?php esc_html_e( 'Single Page', 'primekit-addons' ); ?></option>
                                                <option value="search_page"><?php esc_html_e( 'Search Page', 'primekit-addons' ); ?></option>
                                                <option value="404_page"><?php esc_html_e( '404 Page', 'primekit-addons' ); ?></option>
                                                <option value="archive_page"><?php esc_html_e( 'Archive Page', 'primekit-addons' ); ?></option>
                                                <?php if ( class_exists( 'WooCommerce' ) ) { ?>
                                                <option value="shop_single"><?php esc_html_e( 'Single Product', 'primekit-addons' ); ?></option>
                                                <option value="shop_archive"><?php esc_html_e( 'Shop Archive', 'primekit-addons' ); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="primekit-tb-modal-single-field">
                                            <label for="primekit-tb-modal-ftemplate-name"><?php esc_html_e( 'Name your template', 'primekit-addons' ); ?></label>
                                            <input type="text" name="primekit-tb-modal-ftemplate-name"
                                                id="primekit-tb-modal-ftemplate-name" placeholder="Template Name">
                                        </div>
                                        <div class="primekit-tb-modal-single-field">
                                            <button class="primekit-tb-modal-content-form-submit"
                                                id="primekit-tb-modal-content-form-submit"
                                                disabled><?php esc_html_e( 'Create Template', 'primekit-addons' ); ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <!-- wp.template: display-condition row -->
        <script type="text/html" id="tmpl-primekit-hf-display-condition">
        <div class="primekit-hf-rule-row" data-index="{{data.id}}">
            <button type="button" class="primekit-hf-rule-delete">&times;</button>
            <select class="primekit-hf-rule-condition primekit-hf-modal-select">
                <?php echo self::get_condition_options_html(); ?>
            </select>
        </div>
        </script>

        <!-- wp.template: user-role row -->
        <script type="text/html" id="tmpl-primekit-hf-user-condition">
        <div class="primekit-hf-rule-row" data-index="{{data.id}}">
            <button type="button" class="primekit-hf-rule-delete">&times;</button>
            <select class="primekit-hf-user-condition primekit-hf-modal-select">
                <?php echo self::get_user_role_options_html(); ?>
            </select>
        </div>
        </script>

        <!-- Header Footer Modal -->
        <div class="modal micromodal-slide" id="primekit-hf-modal" aria-hidden="true">
            <div class="modal__overlay" tabindex="-1">
                <div class="primekit-hf-modal-container" role="dialog" aria-modal="true" aria-labelledby="primekit-hf-modal-title">

                    <!-- Gradient header -->
                    <header class="primekit-hf-modal-header">
                        <div class="primekit-hf-modal-header-left">
                            <span class="primekit-hf-modal-header-icon">
                                <img src="<?php echo $icon_url; ?>" alt="">
                            </span>
                            <h2 class="primekit-hf-modal-title" id="primekit-hf-modal-title">
                                <?php esc_html_e( 'Add New Template', 'primekit-addons' ); ?>
                            </h2>
                        </div>
                        <button class="primekit-hf-modal-close" aria-label="Close" data-micromodal-close>&times;</button>
                    </header>

                    <!-- Form body -->
                    <form id="primekit-hf-modal-template-form" class="primekit-hf-modal-body">

                        <input type="hidden" id="primekit-hf-edit-post-id" value="">

                        <input type="text"
                            id="primekit-hf-modal-ftemplate-name"
                            class="primekit-hf-modal-name-input"
                            placeholder="<?php esc_attr_e( 'Add title', 'primekit-addons' ); ?>">

                        <div class="primekit-hf-modal-options-box">
                            <div class="primekit-hf-modal-options-header">
                                <span><?php esc_html_e( 'PrimeKit Header & Footer Builder Options', 'primekit-addons' ); ?></span>
                            </div>

                            <div class="primekit-hf-modal-field-row">
                                <label class="primekit-hf-modal-field-label">
                                    <?php esc_html_e( 'Type of Template', 'primekit-addons' ); ?>
                                </label>
                                <div class="primekit-hf-modal-field-input">
                                    <select id="primekit-hf-modal-select-type" class="primekit-hf-modal-select">
                                        <option value=""><?php esc_html_e( 'Select', 'primekit-addons' ); ?></option>
                                        <option value="header"><?php esc_html_e( 'Header', 'primekit-addons' ); ?></option>
                                        <option value="footer"><?php esc_html_e( 'Footer', 'primekit-addons' ); ?></option>
                                    </select>
                                </div>
                            </div>

                            <!-- Display On — repeater rows -->
                            <div class="primekit-hf-modal-field-row primekit-hf-multirow">
                                <label class="primekit-hf-modal-field-label">
                                    <?php esc_html_e( 'Display On', 'primekit-addons' ); ?>
                                </label>
                                <div class="primekit-hf-modal-field-input">
                                    <div class="primekit-hf-rule-rows" id="primekit-hf-display-rows">
                                        <div class="primekit-hf-rule-row" data-index="0">
                                            <button type="button" class="primekit-hf-rule-delete primekit-hf-hidden">&times;</button>
                                            <select class="primekit-hf-rule-condition primekit-hf-modal-select">
                                                <?php echo self::get_condition_options_html(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="primekit-hf-rule-actions">
                                        <a href="#" class="primekit-hf-add-rule-btn" id="primekit-hf-add-display-rule" data-rule-id="0">
                                            <?php esc_html_e( '+ Add Display Rule', 'primekit-addons' ); ?>
                                        </a>
                                        <a href="#" class="primekit-hf-add-rule-btn primekit-hf-exclude-btn" id="primekit-hf-show-exclusion">
                                            <?php esc_html_e( '+ Add Exclusion Rule', 'primekit-addons' ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Exclusion Rules — hidden until triggered -->
                            <div class="primekit-hf-modal-field-row primekit-hf-multirow" id="primekit-hf-exclusion-section" style="display:none">
                                <label class="primekit-hf-modal-field-label">
                                    <?php esc_html_e( 'Exclusion Rules', 'primekit-addons' ); ?>
                                </label>
                                <div class="primekit-hf-modal-field-input">
                                    <div class="primekit-hf-rule-rows" id="primekit-hf-exclusion-rows">
                                        <div class="primekit-hf-rule-row" data-index="0">
                                            <button type="button" class="primekit-hf-rule-delete">&times;</button>
                                            <select class="primekit-hf-rule-condition primekit-hf-modal-select">
                                                <?php echo self::get_condition_options_html(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="primekit-hf-rule-actions">
                                        <a href="#" class="primekit-hf-add-rule-btn primekit-hf-exclude-btn" id="primekit-hf-add-exclusion-rule" data-rule-id="0">
                                            <?php esc_html_e( '+ Add Exclusion Rule', 'primekit-addons' ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Roles — repeater rows -->
                            <div class="primekit-hf-modal-field-row primekit-hf-multirow">
                                <label class="primekit-hf-modal-field-label">
                                    <?php esc_html_e( 'User Roles', 'primekit-addons' ); ?>
                                </label>
                                <div class="primekit-hf-modal-field-input">
                                    <div class="primekit-hf-rule-rows" id="primekit-hf-user-rows">
                                        <div class="primekit-hf-rule-row" data-index="0">
                                            <button type="button" class="primekit-hf-rule-delete primekit-hf-hidden">&times;</button>
                                            <select class="primekit-hf-user-condition primekit-hf-modal-select">
                                                <?php echo self::get_user_role_options_html(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="primekit-hf-rule-actions">
                                        <a href="#" class="primekit-hf-add-rule-btn" id="primekit-hf-add-user-rule" data-rule-id="0">
                                            <?php esc_html_e( '+ Add User Rule', 'primekit-addons' ); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Canvas Template -->
                            <div class="primekit-hf-modal-field-row">
                                <label class="primekit-hf-modal-field-label primekit-hf-canvas-label">
                                    <?php esc_html_e( 'Enable Layout for Elementor Canvas Template?', 'primekit-addons' ); ?>
                                </label>
                                <div class="primekit-hf-modal-field-input">
                                    <input type="checkbox" id="primekit-hf-canvas-template" class="primekit-hf-canvas-checkbox">
                                </div>
                            </div>

                        </div>

                        <!-- Footer actions -->
                        <div class="primekit-hf-modal-footer">
                            <button type="submit"
                                id="primekit-hf-modal-content-form-submit"
                                class="primekit-hf-modal-submit"
                                disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                <?php esc_html_e( 'Edit with Elementor', 'primekit-addons' ); ?>
                            </button>
                            <button type="button"
                                id="primekit-hf-modal-save-btn"
                                class="primekit-hf-modal-save-btn"
                                disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                <?php esc_html_e( 'Save', 'primekit-addons' ); ?>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Prints the template editor modal markup.
     * 
     * This function checks if the current screen is the Theme Builder page and
     * prints the modal markup if it is.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function template_editor_modal_markup()
    {
        if (isset($_GET['action']) && $_GET['action'] === 'elementor') {
            ?>

            <div class="modal micromodal-slide primekit-theme-builder-modal-area" id="primekit-tb-editor-modal" aria-hidden="true">
                <div class="modal__overlay" tabindex="-1">
                    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="primekit-tb-modal-title">

                        <!--Header-->
                        <header class="modal__header primekit-modal-header">
                            <h2 class="modal__title primekit-modal-heading" id="primekit-tb-editor-modal-title">
                                <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../Assets/img/addons-icon.svg'); ?>"
                                    alt="">
                                <?php esc_html_e('Template Elements Condition', 'primekit-addons'); ?>
                            </h2>
                            <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                        </header><!--/ Header-->

                        <!--Body-->
                        <main class="modal__content primekit-theme-builder-modal-content" id="primekit-tb-editor-modal-content">

                            <div class="primekit-tb-modal-content-area">
                                <div class="primekit-tb-modal-content-heading">
                                    <h2><?php esc_html_e('Where would you like to place your template?', 'primekit-addons'); ?></h2>
                                    <p>
                                        <?php esc_html_e('Specify the conditions under which your template will be applied across your website. For instance, selecting \'Entire Site\' will ensure the template is visible throughout your entire website.', 'primekit-addons'); ?>
                                </div>

                                <!--Template condition form-->
                                <div class="primekit-tb-modal-condition-form">

                                    <form action="#" id="primekit-tb-modal-condition-form">
                                        <div class="primekit-tb-modal-condition-wrapper" id="primekit-tb-modal-condition-wrapper">
                                            <!--Condition will be added here-->
                                        </div>
                                        <button
                                            class="primekit-tb-modal-condition-repeater-btn"><?php esc_html_e('+ Add Condition', 'primekit-addons'); ?></button>
                                    </form>
                                </div>

                            </div>
                        </main><!--/ Body-->

                        <footer class="modal__footer primekit-tb-modal-content-footer">
                            <button class="modal__btn modal__btn-primary"
                                id="primekit-tb-modal-content-form-submit"><?php esc_html_e('Save and Continue', 'primekit-addons'); ?></button>
                            <button class="modal__btn" aria-label="Close this dialog window"
                                data-micromodal-close><?php esc_html_e('Cancel', 'primekit-addons'); ?></button>
                        </footer>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Saves the template metadata.
     * 
     * This function checks if the nonce is valid and if the post ID is valid
     * before saving the template metadata.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function save_template_meta()
    {
        error_log('Form submitted');
        if (!isset($_POST['primekit_tb_modal_nonce']) || !wp_verify_nonce($_POST['primekit_tb_modal_nonce'], 'primekit_tb_modal_action')) {
            wp_die('Nonce check failed');
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        error_log('Post ID: ' . $post_id);

        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_die('Permission check failed');
        }

        $template_type = isset($_POST['primekit-tb-modal-select']) ? sanitize_text_field($_POST['primekit-tb-modal-select']) : '';
        error_log('Template Type: ' . $template_type);
        update_post_meta($post_id, '_primekit_template_type', $template_type);  // Ensure this meta key is the same used in your meta box

        wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit'));
        exit;
    }


}