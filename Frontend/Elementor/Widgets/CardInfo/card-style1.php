<?php
/**
 * Render View file for PrimeKit Card Info
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

$primekit_badge_text = $primekit_settings['primekit_elementor_card_info_badge_text'];
$primekit_heading_text = $primekit_settings['primekit_elementor_card_info_heading_text'];
$primekit_description = $primekit_settings['primekit_elementor_card_info_desc'];
$primekit_button_text = $primekit_settings['primekit_elementor_card_info_btn_text'];
$primekit_card_icon = $primekit_settings['primekit_elementor_card_info_icon'];  
$primekit_button_icon = $primekit_settings['primekit_elementor_card_info_btn_icon']; 
$primekit_button_url = $primekit_settings['primekit_elementor_card_info_btn_url'];
?>

<!-- Card Info Area-->
<div class="primekit-card-style-one-area">
    <div class="primekit-card-info-wrap">

    <!-- Dynamic Image -->
    <?php if ('image' === $primekit_settings['primekit_elementor_card_info_type'] && !empty($primekit_settings['primekit_elementor_card_info_image']['url'])): ?>
        <figure class="primekit-card-image">
            <img fetchpriority="high" src="<?php echo esc_url($primekit_settings['primekit_elementor_card_info_image']['url']); ?>" alt="<?php echo esc_attr($primekit_heading_text); ?>">
            <?php if (!empty($primekit_badge_text)): ?>
                <div class="primekit-badge"><?php echo esc_html($primekit_badge_text); ?></div>
            <?php endif; ?>
        </figure>
    <?php endif; ?>

    <!-- Dynamic Card Icon -->
    <?php if ('icon' === $primekit_settings['primekit_elementor_card_info_type'] && !empty($primekit_card_icon['value'])): ?>
        <div class="primekit-card-icon">
            <?php \Elementor\Icons_Manager::render_icon($primekit_card_icon, ['aria-hidden' => 'true']); ?>
            <?php if (!empty($primekit_badge_text)): ?>
                <div class="primekit-badge"><?php echo esc_html($primekit_badge_text); ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="primekit-card-content">
        <?php if (!empty($primekit_heading_text)): ?>
            <h3 class="primekit-card-title"><?php echo esc_html($primekit_heading_text); ?></h3>
        <?php endif; ?>

    <!-- Dynamic Description -->
    <?php if (!empty($primekit_description)): ?>
            <div class="primekit-card-text">
                <p><?php echo wp_kses_post($primekit_description); ?></p>
            </div>
    <?php endif; ?>

    <!-- Dynamic Button with Icon -->
    <?php if ('yes' === $primekit_settings['primekit_elementor_card_info_btn_show'] && (!empty($primekit_button_text) || !empty($primekit_button_icon))): ?>
            <div class="primekit-card-button">
                <a href="<?php echo esc_url($primekit_button_url['url']); ?>" <?php echo $primekit_button_url['is_external'] ? 'target="_blank"' : ''; ?> <?php echo $primekit_button_url['nofollow'] ? 'rel="nofollow"' : ''; ?>><?php echo esc_html($primekit_button_text); ?> <span class="primekit-btn-icon"><?php \Elementor\Icons_Manager::render_icon($primekit_button_icon, ['aria-hidden' => 'true', 'class' => 'primekit-button-icon']); ?></span></a>
            </div>
    <?php endif; ?>
        </div><!-- end content -->
    </div>
</div><!--/ Card Info Area-->
