<?php
namespace PrimeKit\Admin\Inc\Dashboard\AvailableWidgets;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

use PrimeKit\Admin\Inc\Dashboard\AvailableWidgets\RegularTab;
use PrimeKit\Admin\Inc\Dashboard\AvailableWidgets\WooCommerceTab;
use PrimeKit\Admin\Inc\Dashboard\AvailableWidgets\ProTab;
use PrimeKit\Frontend\Elementor\Inc\Functions;
use PrimeKit\Frontend\Elementor\Inc\Helpers;

class PrimeKitWidgets
{
    /**
     * PrimeKitWidgets constructor.
     *
     * Initializes the PrimeKitWidgets by setting up the classes and hooking into the WordPress
     * 'admin_menu' action to add the "Available Widgets" submenu.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        // Hook to add the submenu.
        add_action('admin_menu', [$this, 'add_widgets_submenu']);
        
    }

    /**
     * Adds the "Available Widgets" submenu page to the PrimeKit home menu.
     *
     * @since 1.0.0
     */
    public function add_widgets_submenu()
    {
        add_submenu_page(
            'primekit_home',             // Parent slug (the top-level menu slug)
            __('Available Widgets', 'primekit-addons'), // Page title
            __('Available Widgets', 'primekit-addons'), // Menu title
            'manage_options',                // Capability
            'primekit_available_widgets',    // Submenu slug
            [$this, 'render_available_widgets_page'],    // Callback function
            10
        );
    }
    
    /**
     * Retrieves the available tabs for the "Available Widgets" page.
     *
     * This function constructs an array of tabs, each with a label and a callback
     * function. The "Regular" tab is always included, while the "WooCommerce" tab
     * is added only if WooCommerce is active.
     *
     * @return array An associative array of tabs, where each key is a tab ID and 
     *               the value is an array containing 'label' and 'callback'.
     */
    protected function get_tabs() {
        $tabs = [
            'regular' => [
                'label' => 'Regular',
                'callback' => 'render_regular_widgets_list'
            ]
        ];
    
        // Conditionally add the WooCommerce tab if WooCommerce is active
        if (Functions::is_woocommerce_active()) {
            $tabs['woocommerce'] = [
                'label' => 'WooCommerce',
                'callback' => 'render_woocommerce_widgets_list'
            ];
        }
        //conditionally add the pro tab if pro is not active
        if (!Helpers::is_pro_active()) {
            $tabs['freePro'] = [
                'label' => 'Pro',
                'callback' => 'render_pro_widgets_list'
            ];
        }


        // Allow external code to add additional tabs
        $tabs = apply_filters('primekit_available_widgets_tabs_register', $tabs);

        return $tabs;
    }
    
    /**
     * Renders the "Available Widgets" page in the PrimeKit admin dashboard.
     *
     * This function outputs the HTML for the "Available Widgets" page, which includes
     * tabbed navigation for "Regular" and "WooCommerce" widgets. The content for each
     * tab is dynamically displayed based on the selected tab. The "Regular" tab lists
     * the regular widgets available in PrimeKit, while the "WooCommerce" tab provides
     * a placeholder for WooCommerce widgets.
     *
     * @since 1.0.0
     */
    public function render_available_widgets_page() {
        $tabs = $this->get_tabs(); // Retrieve the dynamically generated tabs.
    
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Available Widgets', 'primekit-addons'); ?></h1>
    
            <!-- Tab Navigation -->
            <h2 class="nav-tab-wrapper">
                <?php foreach ($tabs as $tab_id => $tab) : ?>
                    <a href="#<?php echo esc_attr($tab_id); ?>" class="nav-tab"><?php echo esc_html($tab['label']); ?></a>
                <?php endforeach; ?>
            </h2>
    
            <!-- Tab Content -->
            <?php foreach ($tabs as $tab_id => $tab) : ?>
                <div id="<?php echo esc_attr($tab_id); ?>" class="tab-content" style="<?php echo $tab_id === 'regular' ? '' : 'display: none;'; ?>">
                    <h3><?php echo esc_html($tab['label']) . ' ' . esc_html__('Widgets', 'primekit-addons'); ?></h3>
                    <?php 
                    // Execute the callback if it’s callable
                    if (is_callable($tab['callback'])) {
                        call_user_func($tab['callback']);
                    } elseif (is_callable([$this, $tab['callback']])) {
                        call_user_func([$this, $tab['callback']]);
                    } else {
                        echo '<p>' . esc_html__('Content not available or not found callback function.', 'primekit-addons') . '</p>';
                    }
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Renders a wrapper for displaying a list of widgets.
     *
     * This function can be used to render a list of available widgets in the
     * PrimeKit admin dashboard. It renders a heading with the given title and
     * displays the available widgets within a wrapper element. The content of
     * the wrapper is rendered via a callback function, which should be provided
     * when calling this function.
     *
     * @param string $title Optional. The title to display above the list of
     *                      widgets. Defaults to 'Widgets List'.
     * @param callable $callback Optional. The callback function to use for
     *                           rendering the list of widgets. If not provided,
     *                           a default message is displayed.
     *
     * @since 1.0.0
     */
    public function render_widgets_wrapper($title = 'Widgets List', $callback = null) {
        ?>
        <p><?php echo esc_html($title); ?></p>    
            <?php
            
            // Start the wrapper
            do_action( 'primekit_available_widgets_wrapper_start');
            // Check if the callback is provided and callable
            if (is_callable($callback)) {
                call_user_func($callback);
            } else {
                echo '<p>' . esc_html__('No widgets to display.', 'primekit-addons') . '</p>';
            }
            // End the wrapper
            do_action( 'primekit_available_widgets_wrapper_end');

            ?>     
        <?php
    }    
    
    /**
     * Renders a list of regular widgets.
     *
     * This function is a wrapper for {@see render_widgets_wrapper} that renders a
     * list of regular widgets. It displays a heading with the given title and
     * calls the `primekit_regular_widgets_display` method from the `RegularTab` class to
     * render the list of widgets.
     *
     * @since 1.0.0
     */
    public function render_regular_widgets_list() {
        $this->render_widgets_wrapper(
            esc_html__('List of regular widgets available in PrimeKit.', 'primekit-addons'),
            [RegularTab::class, 'primekit_regular_widgets_display']
        );
    }

        /**
        * Renders a list of pro widgets.
        *
        * This function is a wrapper for {@see render_widgets_wrapper} that renders a
        * list of pro widgets. It displays a heading with the given title and
        * calls the `primekit_pro_widgets_display` method from the `ProTab`
        * class to render the list of widgets.
        *
        * @since 1.0.0
        */
    public function render_pro_widgets_list() {
        $this->render_widgets_wrapper(
            esc_html__('List of pro widgets available in PrimeKit Pro.', 'primekit-addons'),
            [ProTab::class, 'primekit_pro_widgets_display']
        );
    }


    /**
     * Renders a list of WooCommerce widgets.
     *
     * This function is a wrapper for {@see render_widgets_wrapper} that renders a
     * list of WooCommerce widgets. It displays a heading with the given title and
     * calls the `primekit_woocommerce_widgets_display` method from the `WooCommerceTab`
     * class to render the list of widgets.
     *
     * @since 1.0.0
     */
    public function render_woocommerce_widgets_list() {
        $this->render_widgets_wrapper(
            esc_html__('List of WooCommerce widgets available in PrimeKit.', 'primekit-addons'),
            [WooCommerceTab::class, 'primekit_woocommerce_widgets_display']
        );
    }
    
    /**
     * Renders a single available widget.
     *
     * This function renders a single widget with an option to toggle it on or off.
     * It displays the widget title, icon, and a toggle switch. The toggle switch
     * is used to save the widget setting in the database.
     *
     * The function takes the following parameters:
     *  - $widget_name: The option name for the widget setting.
     *  - $title: The title of the widget.
     *  - $icon_url: The URL of the widget icon.
     *  - $is_free: Whether the widget is free or pro.
     *  - $widget_url: The URL of the widget.
     *
     * @param string $widget_name The option name for the widget setting.
     * @param string $title The title of the widget.
     * @param string $icon_url The URL of the widget icon.
     * @param bool   $is_free Whether the widget is free or pro.
     * @param string $widget_url The URL of the widget.
     *
     * @since 1.0.0
     */
    public static function primekit_available_widget($widget_name, $title, $icon_url, $is_free = true, $widget_url = '#', $default_enabled = null) {
        
        $widget_name = sanitize_key($widget_name);

        // Ensure $default_enabled is boolean or null (if required)
        $default_enabled = is_bool($default_enabled) || is_null($default_enabled) ? $default_enabled : null;
        // Get the widget option or use $default_enabled
        $option = ($default_enabled !== null) ? (bool) $default_enabled : (bool) get_option($widget_name, 1);        

        // Determine the availability text based on $is_free
        $availability_text = $is_free ? esc_html__('Free', 'primekit-addons') : esc_html__('Pro', 'primekit-addons');    
        ?>
        <div class="primekit-available-single-widget">
            <div class="primekit-available-single-widget-header">
                <div class="primekit-availability-text"><?php echo esc_html($availability_text); ?></div>            
                <div class="primekit-available-single-switch">                   
                    <label class="primekit-switch">
                        <input type="checkbox" name="<?php echo esc_attr($widget_name); ?>" value="1" <?php checked(1, $option, true); ?>>
                        <span class="primekit-slider primekit-round"></span>
                        <span class="primekit-switch-label primekit-switch-off"><?php echo esc_html__('off', 'primekit-addons'); ?></span>
                        <span class="primekit-switch-label primekit-switch-on"><?php echo esc_html__('on', 'primekit-addons'); ?></span>
                    </label>
                </div>
            </div>
            <div class="primekit-widget-icon">
                <a href="<?php echo esc_url($widget_url); ?>" target="_blank"><img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_html($title); ?>"></a>
            </div>
            <div class="primekit-widget-title">
                <h3><a href="<?php echo esc_url($widget_url); ?>" target="_blank"><?php echo esc_html($title); ?></a></h3>
            </div>
        </div>
        <?php
    }


}