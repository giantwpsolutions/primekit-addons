<?php
/**
 * Render View for PrimeKit Page Content Widget
 * - Works with Classic/Gutenberg AND Elementor-built pages
 * - Prevents raw CSS being printed as text
 * - Avoids recursion error in Elementor editor
 */

if ( ! defined('ABSPATH') ) exit;

use Elementor\Plugin as Elementor;

$elementor     = Elementor::instance();
$in_editor     = $elementor->editor->is_edit_mode();
$current_doc   = $elementor->documents->get_current();
$template_id   = $current_doc ? $current_doc->get_main_id() : 0;

/**
 * Figure out the "target" post to render:
 * - In editor, prefer the previewed post/page (if any)
 * - Otherwise, use the currently queried object
 */
$target_id = 0;

// Elementor editor often sets ?elementor-preview=<ID> for the preview context.
if ( isset($_GET['elementor-preview']) ) {
    $target_id = absint( $_GET['elementor-preview'] );
}

// Fallback to queried object
if ( ! $target_id ) {
    $target_id = get_the_ID();
}

$primekit_page_content = '';

/**
 * Guard against rendering the same template into itself in the editor.
 * If the target to render equals the currently edited template, Elementor
 * will throw "Template ID cannot be the same..." or render blank.
 */
if ( $in_editor && $template_id && $target_id === $template_id ) {
    // Friendly notice in editor; do nothing on frontend for this case.
    echo '<div class="primekit-elementor-page-content-area" style="padding:12px;border:1px dashed #ddd;border-radius:6px;background:#fafafa;">
        <strong>PrimeKit Page Content:</strong> This widget won’t render the template you are currently editing.
        Select a preview document (gear icon → Preview Settings) or drop this widget into a different template/page.
    </div>';
    return;
}

/**
 * Render pipeline:
 * - If target post is Elementor-built → use Elementor’s renderer (includes CSS)
 * - Else → use the normal the_content() pipeline
 */
$is_elementor_built = false;
if ( $target_id ) {
    $doc = $elementor->documents->get( $target_id );
    $is_elementor_built = $doc && $doc->is_built_with_elementor();
}

if ( $is_elementor_built ) {
    // Use Elementor’s native renderer; this outputs proper HTML + inline <style> blocks.
    $primekit_page_content = $elementor->frontend->get_builder_content_for_display( $target_id );
} else {
    // Classic/Gutenberg path: let WordPress run shortcodes/embeds/blocks.
    // We temporarily remove wptexturize to avoid hyphen → en-dash issues inside allowed <style> blocks.
    remove_filter( 'the_content', 'wptexturize' );
    $post_obj = get_post( $target_id );
    $primekit_page_content = $post_obj ? apply_filters( 'the_content', $post_obj->post_content ) : '';
    add_filter( 'the_content', 'wptexturize' );
}

/**
 * Output with sanitization that ALLOWS <style> tags.
 * Using wp_kses_post() alone will STRIP <style>, causing raw CSS text to appear.
 */
if ( $primekit_page_content ) {
    // Base allowlist for posts
    $allowed = wp_kses_allowed_html( 'post' );
    // Permit <style> so Elementor’s inline CSS stays as CSS (not printed as text)
    $allowed['style'] = [
        'id'                   => true,
        'type'                 => true,
        'media'                => true,
        'scoped'               => true,
        'data-elementor-inline'=> true,
        'data-type'            => true,
    ];

    // If some external filter already stripped <style>, you might still see CSS text at the very top.
    // As a last-resort band-aid, you can strip a leading raw-CSS blob that lost its <style> tag:
    // if ( strpos( $primekit_page_content, '.elementor-' ) !== false && strpos( $primekit_page_content, '<style' ) === false ) {
    //     $primekit_page_content = preg_replace( '/^\s*(?:\.[\w\-\.\#\s:>\[\]\(\)"\'=,]+?\{[^}]*\}\s*)+/m', '', $primekit_page_content, 1 );
    // }

    echo '<div class="primekit-elementor-page-content-area">';
    echo wp_kses( $primekit_page_content, $allowed );
    echo '</div>';
}
