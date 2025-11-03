<?php
/**
 * Render View for PrimeKit Page Content Widget
 *
 * Supports both Classic/Gutenberg and Elementor pages.
 * Fixes issue where inline Elementor CSS appeared as text.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Elementor\Plugin as Elementor;

// Detect Elementor editor mode
$in_editor = Elementor::instance()->editor->is_edit_mode();
$primekit_page_content = '';

/**
 * Step 1: Get the page content based on context
 */
if ( $in_editor ) {
    // In Elementor Editor — display the latest published page (preview content)
    $latest_page = new \WP_Query([
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
    ]);

    if ( $latest_page->have_posts() ) {
        $latest_page->the_post();
        $primekit_page_content = apply_filters( 'the_content', get_the_content() );
        wp_reset_postdata();
    } else {
        $primekit_page_content = esc_html__( 'No content available.', 'primekit-addons' );
    }

} else {
    // Frontend mode — use current page/post content
    $post = get_post();

    if ( $post ) {
        // If this page is built with Elementor, render full Elementor output
        $is_elementor = \Elementor\Plugin::$instance->documents->get( $post->ID )
            && \Elementor\Plugin::$instance->documents->get( $post->ID )->is_built_with_elementor();

        if ( $is_elementor ) {
            // Use Elementor’s native renderer to include proper <style> and structure
            $primekit_page_content = Elementor::$instance->frontend->get_builder_content_for_display( $post->ID );
        } else {
            // Use normal WordPress content filter
            $primekit_page_content = apply_filters( 'the_content', $post->post_content );
        }
    }
}

/**
 * Step 2: Output content safely — allow <style> tags
 */
if ( $primekit_page_content ) :
    // Allow <style> tags (prevents Elementor inline CSS from being printed as plain text)
    $allowed = wp_kses_allowed_html( 'post' );
    $allowed['style'] = [
        'id'    => true,
        'type'  => true,
        'media' => true,
        'scoped' => true,
        'data-elementor-inline' => true,
        'data-type' => true,
    ];
    ?>
    <div class="primekit-elementor-page-content-area">
        <?php echo wp_kses( $primekit_page_content, $allowed ); ?>
    </div>
<?php endif; ?>
