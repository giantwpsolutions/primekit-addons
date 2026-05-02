<?php 
namespace PrimeKit\Admin\Inc\Dashboard\Settings;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class PrimeKit {

    public function __construct() {
        // Menu is now registered by VueAdminPage — no duplicate needed here.
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
           
            <div id="primekit-custom-header" class="primekit-custom-header">
                <!-- Banner -->
                <div class="primekit-banner-area">
                    <h1><?php echo esc_html__("Welcome to", "primekit-addons"); ?> <?php echo esc_html(PRIMEKIT_NAME); ?></h1>  
                    <p class="primekit-banner-version"><?php echo esc_html__("Version: ", "primekit-addons"); ?> <?php echo esc_html(PRIMEKIT_VERSION); ?></p>              
                    <!-- Buttons -->
                    <div class="primekit-resource-buttons">                
                                            
                        <a href="https://demo.primekitaddons.com/addons-widgets/" target="_blank" class="button"><?php echo esc_html__("Demos", "primekit-addons"); ?></a>
                        
                        <a href="https://primekitaddons.com/documentation/" class="button" target="_blank"><?php echo esc_html__("Documentation", "primekit-addons"); ?></a>
                       
                        <a href="https://primekitaddons.com/contact-us/" target="_blank" class="button"><?php echo esc_html__("Support", "primekit-addons"); ?></a>     
                        
                        <a href="<?php echo esc_url(admin_url( 'admin.php?page=primekit_available_widgets' )); ?>" class="button"><?php echo esc_html__( 'Avilable Widgets', 'primekit-addons' ); ?><span class="dashicons dashicons-arrow-right-alt"></span></a>
                    </div>
                </div>         
            </div>
    
        </div>
        <?php
    }
}