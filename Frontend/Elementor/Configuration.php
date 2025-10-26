<?php
/**
 * Configuration class for PrimeKit Elementor Addons.
 *
 * This class handles the initialization and configuration of the PrimeKit Elementor Addons.
 * It ensures compatibility with the required Elementor version and manages the loading of 
 * required assets and functionalities.
 *
 * @package PrimeKit\Frontend\Elementor
 * @since 1.0.0
 */
namespace PrimeKit\Frontend\Elementor;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

use PrimeKit\Frontend\Elementor\Inc\Functions;
use PrimeKit\Frontend\Elementor\Assets\Assets;
use PrimeKit\Frontend\Elementor\Inc\PostViewTracker;
use PrimeKit\Frontend\Elementor\Globals\WrapperURL;
use PrimeKit\Frontend\Elementor\Globals\CustomCSS;
use PrimeKit\Frontend\Elementor\Globals\CSSTransform;
use PrimeKit\Frontend\Elementor\Globals\NestedTabsExtend;
use PrimeKit\Frontend\Elementor\Globals\PreLoader;
use PrimeKit\Frontend\Elementor\Inc\Helpers;


/**
 * Class Configuration
 *
 * This class handles the initialization and configuration of the PrimeKit Elementor Addons.
 * It ensures compatibility with the required Elementor version and manages the loading of 
 * required assets and functionalities.
 * 
 * @package PrimeKit\Frontend\Elementor
 * @since 1.0.0
 */
class Configuration
{


    protected $functions;
    protected $assets;
    protected $Post_View_Tracker;
    protected $WrapperURL;
    protected $CustomCSS;
    protected $CSSTransform;
    protected $NestedTabsExtend;
    protected $PreLoader;

    /**
     * plugin Version
     */

    public $version = PRIMEKIT_VERSION;

    /**
     * Minimum Elementor Version
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.19.0';

    /**
     * Minimum PHP Version
     */
    const MINIMUM_PHP_VERSION = '8.0';

    /**
     * Instance
     */
    private static $_instance = null;

    /**
     * Ensures only one instance of the class is loaded or can be loaded.
     */
    public static function instance()
    {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Perform some compatibility checks to make sure basic requirements are meet.
     */
    public function __construct()
    {

        // set the constants.
        $this->setConstants();

        if ($this->is_compatible()) {
            add_action('elementor/init', [$this, 'init']);
        }

        //classes Initialization.
        $this->classes_init();

    }


    /**
     * Compatibility Checks
     */
    public function is_compatible()
    {

        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
            return false;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return false;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return false;
        }

        return true;
    }

    /**
     * setConstants.
     */

    public function setConstants()
    {
        define('PRIMEKIT_ELEMENTOR_ASSETS', plugin_dir_url(__FILE__) . 'Assets');
        define('PRIMEKIT_ELEMENTOR_PATH', plugin_dir_path(__FILE__));

    }

    /**
     * Warning when the site doesn't have Elementor installed or activated.
     */
    public function admin_notice_missing_main_plugin()
    {
        // Verify the nonce if 'activate' is present in the URL
        if (isset($_GET['activate']) && check_admin_referer('activate-plugin_' . plugin_basename(__FILE__))) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            // translators: 1 Plugin name, 2 Elementor plugin name, 3 Required Elementor version
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'primekit-addons'),
            esc_html(PRIMEKIT_NAME),
            esc_html__('Elementor', 'primekit-addons'),
            esc_html(self::MINIMUM_ELEMENTOR_VERSION)
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message));
    }

    /**
     * Warning when the site doesn't have a minimum required Elementor version.
     */
    public function admin_notice_minimum_elementor_version()
    {
        // Verify the nonce if 'activate' is present in the URL
        if (isset($_GET['activate']) && check_admin_referer('activate-plugin_' . plugin_basename(__FILE__))) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            // translators: 1 Plugin name, 2 Elementor plugin name, 3 Required Elementor version
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'primekit-addons'),
            esc_html(PRIMEKIT_NAME),
            esc_html__('Elementor', 'primekit-addons'),
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post($message));
    }

    /**
     * Warning when the site doesn't have a minimum required PHP version.
     */
    public function admin_notice_minimum_php_version()
    {

        // Verify the nonce if 'activate' is present in the URL
        if (isset($_GET['activate']) && check_admin_referer('activate-plugin_' . plugin_basename(__FILE__))) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'primekit-addons'),
            '<strong>' . PRIMEKIT_NAME . '</strong>',
            '<strong>' . esc_html__('PHP', 'primekit-addons') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post($message));
    }

    /**
     * Initializes the classes used by the plugin.
     *
     * This function instantiates the functions and assets classes.
     *
     * @since 1.0.0
     */
    public function classes_init()
    {

        $this->functions = new Functions();
        $this->assets = new Assets();
        $this->Post_View_Tracker = new PostViewTracker();
        $this->WrapperURL = new WrapperURL();
        $this->CustomCSS = new CustomCSS();
        $this->CSSTransform = new CSSTransform();
        $this->NestedTabsExtend = new NestedTabsExtend();
        //$this->PreLoader = new PreLoader(); // not ready yet
    }


    /**
     * Load the addons functionality only after Elementor is initialized.
     */
    public function init()
    {
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }



    /**
     * Register all the widgets.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     *
     * @return void
     */

    public function register_widgets($widgets_manager)
    {

        $namespace_base = '\PrimeKit\Frontend\Elementor\Widgets\\';

        // Register all widgets
        $this->register_general_widgets($widgets_manager, $namespace_base);

        // Register WooCommerce widgets if WooCommerce is active
        if (Functions::is_woocommerce_active()) {
            $this->register_woocommerce_widgets($widgets_manager, $namespace_base);
        }

        // Register Pro widgets if Pro is active
        if (Helpers::is_pro_active()) {
            return;
        }else { 
            $this->register_pro_locked_widgets($widgets_manager, $namespace_base);
        }

    }

    /**
     * Registers the general widgets.
     */
    private function register_general_widgets($widgets_manager, $namespace_base)
    {
        $widgets = [
            'primekit_shape_anim_widget_field' => 'AnimatedShape\Main',
            'primekit_anim_text_widget_field' => 'AnimatedText\Main',
            'primekit_archive_title_field' => 'ArchiveTitle\Main',
            'primekit_author_bio_widget_field' => 'AuthorBio\Main',
            'primekit_back_top_widget_field' => 'BackToTop\Main',
            'primekit_before_after_widget_field' => 'BeforeAfterImg\Main',
            'primekit_blockquote_widget_field' => 'Blockquote\Main',
            'primekit_blog_grid_widget_field' => 'BlogGrid\Main',
            'primekit_blog_list_widget_field' => 'BlogList\Main',
            'primekit_blog_fancy_widget_field' => 'BlogPostFancy\Main',
            'primekit_breadcrumb_widget_field' => 'BreadCrumb\Main',
            'primekit_call_button_widget_field' => 'CallButton\Main',
            'primekit_business_hours_field' => 'BusinessHours\Main',
            'primekit_card_info_widget_field' => 'CardInfo\Main',
            'primekit_cat_list_widget_field' => 'CatInfo\Main',
            'primekit_circular_skill_widget_field' => 'CircularSkills\Main',
            'primekit_icon_box_widget_field' => 'IconBox\Main',
            'primekit_page_title_widget_field' => 'PageTitle\Main',
            'primekit_post_title_widget_field' => 'PostTitle\Main',
            'primekit_site_logo_widget_field' => 'SiteLogo\Main',
            'primekit_site_title_tagline_field' => 'SiteTitle\Main',
            'primekit_testi_caro_widget_field' => 'Testimonials\Main',
            'primekit_wp_menu_widget_field' => 'WpMenu\Main',
            'primekit_posts_slider_field' => 'PostsSlider\Main',
            'primekit_template_slider_field' => 'TemplateSlider\Main',
            'primekit_modern_post_grid_field' => 'ModernPostGrid\Main',
            'primekit_contact_form7_widget_field' => 'ContactForm7\Main',
            'primekit_comment_form_widget_field' => 'CommentForm\Main',
            'primekit_contact_info_widget_field' => 'ContactInfo\Main',
            'primekit_lottie_icon_widget_field' => 'Lottie\Main',
            'primekit_mailchimp_switch_field' => 'MailChimp\Main',
            'primekit_cost_estimation_field' => 'CostEstimation\Main',
            'primekit_count_down_widget_field' => 'CountDown\Main',
            'primekit_counter_up_widget_field' => 'Counter\Main',
            'primekit_cta_widget_field' => 'CTA\Main',
            'primekit_dual_button_widget_field' => 'DualButton\Main',
            'primekit_fetch_posts_field' => 'FetchPosts\Main',
            'primekit_feat_img_widget_field' => 'FeturedImg\Main',
            'primekit_flip_box_widget_field' => 'FlipBox\Main',
            'primekit_gravity_form_field' => 'GravityForms\Main',
            'primekit_image_gallery_field' => 'ImageGallery\Main',
            'primekit_img_hover_widget_field' => 'ImgHover\Main',
            'primekit_img_text_scroll_widget_field' => 'ImgScroll\Main',
            'primekit_loading_screen_widget_field' => 'LoadingPage\Main',
            'primekit_popular_posts_field' => 'PopularPosts\Main',
            'primekit_popup_widget_field' => 'Popup\Main',
            'primekit_portfolio_widget_field' => 'Portfolio\Main',
            'primekit_post_content_widget_field' => 'PostContent\Main',
            'primekit_page_content_widget_field' => 'PageContent\Main',
            'primekit_post_meta_widget_field' => 'PostInfo\Main',
            'primekit_pricing_table_widget_field' => 'PricingTable\Main',
            'primekit_recent_post_widget_field' => 'RecentPost\Main',
            'primekit_related_post_widget_field' => 'RelatedPost\Main',
            'primekit_search_form_widget_field' => 'SearchForm\Main',
            'primekit_search_icon_widget_field' => 'SearchIcon\Main',
            'primekit_sec_title_widget_field' => 'SectionTitle\Main',
            'primekit_single_img_scroll_field' => 'SingleImgScroll\Main',
            'primekit_skill_bar_widget_field' => 'SkillBar\Main',
            'primekit_social_share_widget_field' => 'SocialShare\Main',
            'primekit_sticker_text_field' => 'StickerText\Main',
            'primekit_tag_info_widget_field' => 'TagInfo\Main',
            'primekit_team_member_widget_field' => 'TeamMember\Main',
            'primekit_copyright_field' => 'Copyright\Main',
            'primekit_advanced_list_field' => 'AdvancedList\Main',
            'primekit_glass_card_field' => 'GlassCard\Main',
        ];
        foreach ($widgets as $option_name => $widget_class) {
            $is_enabled = get_option($option_name, 1); // Get the option value (default to enabled)

            if ($is_enabled) {
                $full_class_name = $namespace_base . $widget_class; // Combine base namespace with class path
                $widgets_manager->register(new $full_class_name());
            }
        }
    }
    /**
     * Registers WooCommerce-specific widgets.
     */
    private function register_woocommerce_widgets($widgets_manager, $namespace_base)
    {
        $woocommerce_widgets = [
            'primekit_wc_add_to_cart_icon_field' => 'WooCommerce\ProductAddToCart\Main',
            'primekit_wc_product_cart_icon_field' => 'WooCommerce\ProductCartIcon\Main',
            'primekit_wc_cart_page_field' => 'WooCommerce\ProductCartPage\Main',
            'primekit_wc_checkout_page_field' => 'WooCommerce\ProductCheckout\Main',
            'primekit_wc_product_img_field' => 'WooCommerce\ProductImg\Main',
            'primekit_wc_product_meta_field' => 'WooCommerce\ProductMeta\Main',
            'primekit_wc_product_price_field' => 'WooCommerce\ProductPrice\Main',
            'primekit_wc_product_related_field' => 'WooCommerce\ProductRelated\Main',
            'primekit_wc_product_short_desc_field' => 'WooCommerce\ProductShortDesc\Main',
            'primekit_wc_product_tabs_field' => 'WooCommerce\ProductTabs\Main',
            'primekit_wc_product_title_field' => 'WooCommerce\ProductTitle\Main',
            'primekit_wc_my_account_field' => 'WooCommerce\WooAccount\Main',
            'primekit_wc_product_bread_crumb_field' => 'WooCommerce\WooBreadCrumb\Main',
        ];

        foreach ($woocommerce_widgets as $option_name => $widget_class) {
            $is_enabled = get_option($option_name, 1); // Get the option value (default to enabled)

            if ($is_enabled) {
                $full_class_name = $namespace_base . $widget_class; // Combine base namespace with class path
                $widgets_manager->register(new $full_class_name());
            }
        }
    }

    /**
     * Registers Pro-specific widgets.
     */
    private function register_pro_locked_widgets($widgets_manager, $namespace_base)
    {
        $pro_widgets = [
            'primekit_advanced_accordion_locked_widget_field' => 'AdvancedAccordionLocked\Main',
        ];

        foreach ($pro_widgets as $option_name => $widget_class) {
            $is_enabled = get_option($option_name, 1); // Get the option value (default to enabled)

            if ($is_enabled) {
                $full_class_name = $namespace_base . $widget_class; // Combine base namespace with class path
                $widgets_manager->register(new $full_class_name());
            }
        }
    }

}