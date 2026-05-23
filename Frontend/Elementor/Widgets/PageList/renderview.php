<?php
/**
 * Render View for PrimeKit Page List Widget
 */

if (!defined('ABSPATH')) exit;

$settings = $this->get_settings_for_display();
$items    = $settings['primekit_page_list_items'];
$layout   = $settings['primekit_page_list_layout'] ?? 'list';
$heading  = $settings['primekit_page_list_heading'] ?? '';
$htag     = $settings['primekit_page_list_heading_tag'] ?? 'h3';

if (empty($items)) return;

$wrap_class = 'primekit-page-list-wrap';
if ($layout === 'grid') {
    $wrap_class .= ' primekit-page-list-grid';
}

$allowed_tags = ['h2', 'h3', 'h4', 'h5', 'h6', 'p'];
$htag = in_array($htag, $allowed_tags) ? $htag : 'h3';
?>

<div class="primekit-page-list-widget">

    <?php if (!empty($heading)) : ?>
    <<?php echo $htag; ?> class="primekit-page-list-heading"><?php echo esc_html($heading); ?></<?php echo $htag; ?>>
    <?php endif; ?>

    <nav class="<?php echo esc_attr($wrap_class); ?>">
        <?php foreach ($items as $item) :
            // Resolve link
            $link_url    = '';
            $link_target = '_self';

            if (!empty($item['link']['url'])) {
                $link_url    = esc_url($item['link']['url']);
                $link_target = !empty($item['link']['is_external']) ? '_blank' : '_self';
            }

            // Per-item background (only if user explicitly set one)
            $bg_style = '';
            if (!empty($item['bg_type']) && $item['bg_type'] === 'gradient') {
                $from     = !empty($item['bg_gradient_from']) ? $item['bg_gradient_from'] : '#6c63ff';
                $to       = !empty($item['bg_gradient_to'])   ? $item['bg_gradient_to']   : '#c836f8';
                $bg_style = "background: linear-gradient(135deg, {$from}, {$to});";
            } elseif (!empty($item['bg_color']) && $item['bg_color'] !== 'transparent') {
                $bg_style = "background: {$item['bg_color']};";
            }

            $tag       = $link_url ? 'a' : 'span';
            $tag_attrs = '';
            if ($link_url) {
                $tag_attrs = sprintf('href="%s" target="%s"', esc_url($link_url), esc_attr($link_target));
            }
        ?>
        <<?php echo $tag; ?> <?php echo $tag_attrs; ?>
            class="primekit-page-list-item elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>"
            <?php if ($bg_style) : ?>style="<?php echo esc_attr($bg_style); ?>"<?php endif; ?>>

            <?php if (!empty($item['show_icon']) && $item['show_icon'] === 'yes' && !empty($item['selected_icon']['value'])) : ?>
            <span class="primekit-page-list-icon">
                <?php \Elementor\Icons_Manager::render_icon($item['selected_icon'], ['aria-hidden' => 'true']); ?>
            </span>
            <?php endif; ?>

            <span class="primekit-page-list-text">
                <span class="primekit-page-list-title-row">
                    <?php if (!empty($item['text'])) : ?>
                    <span class="primekit-page-list-title"><?php echo esc_html($item['text']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($item['show_label']) && $item['show_label'] === 'yes' && !empty($item['label_text'])) :
                        $label_color = !empty($item['label_color']) ? $item['label_color'] : '#ffffff';
                        $label_bg    = !empty($item['label_bg'])    ? $item['label_bg']    : '#22c55e';
                    ?>
                    <span class="primekit-page-list-label"
                          style="color:<?php echo esc_attr($label_color); ?>;background:<?php echo esc_attr($label_bg); ?>;">
                        <?php echo esc_html($item['label_text']); ?>
                    </span>
                    <?php endif; ?>
                </span>
                <?php if (!empty($item['sub_title'])) : ?>
                <span class="primekit-page-list-subtitle"><?php echo esc_html($item['sub_title']); ?></span>
                <?php endif; ?>
            </span>

        </<?php echo $tag; ?>>
        <?php endforeach; ?>
    </nav>

</div>
