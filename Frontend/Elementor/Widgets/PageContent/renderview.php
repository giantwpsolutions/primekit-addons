<?php
/**
 * Render View for PrimeKit Page Content Widget
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Check if Elementor is in editor mode
if (\Elementor\Plugin::instance()->editor->is_edit_mode()) {
    $post_id = \Elementor\Plugin::instance()->editor->get_post_id();
    $editor = \Elementor\Plugin::instance()->editor;
    $is_edit_mode = $editor->is_edit_mode();
    $editor->set_edit_mode(false);
    $primekit_page_content = \Elementor\Plugin::instance()->frontend->get_builder_content($post_id, false);
    $editor->set_edit_mode($is_edit_mode);
} else {
    // For frontend, use the current post's content
    $primekit_page_content = apply_filters('the_content', get_the_content());
}

// Display the post content if available
if ($primekit_page_content) {
    ?>
    <div class="primekit-elementor-page-content-area">
        <?php echo $primekit_page_content; ?>
    </div>
    <?php
}
