<?php
/**
 * MetaBox.php
 *
 * This file contains the MetaBox class, which is responsible for adding
 * the custom meta box to the Theme Builder page and saving the meta box data.
 *
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Classes
 * @since 1.0.0
 */

namespace PrimeKit\Admin\Inc\ThemeBuilder\Classes;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Class MetaBox
 * 
 * Handles the addition of the custom meta box to the Theme Builder page and saving the meta box data.
 * 
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Classes
 * @since 1.0.0
 */
class MetaBox
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
        add_action('save_post', array($this, 'save_meta_box_data'));
    }

    /**
     * Summary of add_custom_meta_box
     * @return void
     */
    public function add_custom_meta_box()
    {
        add_meta_box(
            'primekit_themebuilder_select',            // ID of the meta box
            __('Choose Template Type', 'primekit-addons'), // Title of the meta box
            array($this, 'meta_box_callback'),       // Callback function
            'primekit_library',                       // Post type
            'advanced',                                 // Context
            'default'                               // Priority
        );
    }

    /**
     * Callback function for the custom meta box.
     * 
     * This function is called when the custom meta box is displayed on the Theme Builder page.
     * It displays the template selection and display condition fields.
     * 
     * @param mixed $post The post object.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function meta_box_callback($post)
    {
        wp_nonce_field('primekit_custom_box', 'primekit_custom_box_nonce');

        $template_value  = get_post_meta($post->ID, 'primekit_themebuilder_select', true);
        $is_hf           = in_array($template_value, ['header', 'footer'], true);

        // New array-based meta
        $display_rules   = get_post_meta($post->ID, 'primekit_hf_display_rules',   true);
        $exclusion_rules = get_post_meta($post->ID, 'primekit_hf_exclusion_rules',  true);
        $user_roles      = get_post_meta($post->ID, 'primekit_hf_user_roles',       true);
        $canvas          = get_post_meta($post->ID, 'primekit_hf_canvas',           true);

        // Backward-compat with old single-value format
        if (!is_array($display_rules)) {
            $legacy = get_post_meta($post->ID, 'primekit_hf_condition', true);
            $display_rules = $legacy ? [$legacy] : [];
        }
        if (!is_array($exclusion_rules)) $exclusion_rules = [];
        if (!is_array($user_roles))      $user_roles      = [];

        // HFB-style keys + legacy for tag label display
        $condition_options = [
            'basic-global'        => __('Entire Website',    'primekit-addons'),
            'basic-singulars'     => __('All Singulars',     'primekit-addons'),
            'basic-archives'      => __('All Archives',      'primekit-addons'),
            'special-404'         => __('404 Page',          'primekit-addons'),
            'special-search'      => __('Search Page',       'primekit-addons'),
            'special-blog'        => __('Blog / Posts Page', 'primekit-addons'),
            'special-front'       => __('Front Page',        'primekit-addons'),
            'special-date'        => __('Date Archive',      'primekit-addons'),
            'special-author'      => __('Author Archive',    'primekit-addons'),
            'post|all'            => __('All Posts',         'primekit-addons'),
            'post|all|archive'    => __('Post Archives',     'primekit-addons'),
            'page|all'            => __('All Pages',         'primekit-addons'),
            // Legacy keys
            'entire_site'  => __('Entire Site',    'primekit-addons'),
            'front_page'   => __('Front Page Only', 'primekit-addons'),
            'single_post'  => __('Single Posts',    'primekit-addons'),
            'single_page'  => __('Single Pages',    'primekit-addons'),
            'archive'      => __('Archive Pages',   'primekit-addons'),
            'search'       => __('Search Results',  'primekit-addons'),
            '404'          => __('404 Page',        'primekit-addons'),
        ];
        if (class_exists('WooCommerce')) {
            $condition_options['special-woo-shop']    = __('WooCommerce Shop Page', 'primekit-addons');
            $condition_options['product|all']         = __('All Products',          'primekit-addons');
            $condition_options['product|all|archive'] = __('Product Archives',      'primekit-addons');
        }

        // Build role options from get_editable_roles() for the Advanced section
        $role_options = [
            'all'        => __('All',        'primekit-addons'),
            'logged-in'  => __('Logged In',  'primekit-addons'),
            'logged-out' => __('Logged Out', 'primekit-addons'),
        ];
        foreach (get_editable_roles() as $slug => $data) {
            $role_options[$slug] = translate_user_role($data['name']);
        }

        echo '<table class="form-table"><tbody>';

        // Template type row
        echo '<tr>';
        echo '<th><label for="primekit_themebuilder_select">' . esc_html__('Type:', 'primekit-addons') . '</label></th>';
        echo '<td>';
        echo '<select id="primekit_themebuilder_select" name="primekit_themebuilder_select">';
        echo '<option value="">'                                                                              . esc_html__('Select...', 'primekit-addons')     . '</option>';
        echo '<option value="header"'      . selected($template_value, 'header', false)      . '>' . esc_html__('Header', 'primekit-addons')         . '</option>';
        echo '<option value="footer"'      . selected($template_value, 'footer', false)      . '>' . esc_html__('Footer', 'primekit-addons')         . '</option>';
        echo '<option value="single_post"' . selected($template_value, 'single_post', false) . '>' . esc_html__('Single Post', 'primekit-addons')   . '</option>';
        echo '<option value="single_page"' . selected($template_value, 'single_page', false) . '>' . esc_html__('Single Page', 'primekit-addons')   . '</option>';
        echo '<option value="search_page"' . selected($template_value, 'search_page', false) . '>' . esc_html__('Search Page', 'primekit-addons')   . '</option>';
        echo '<option value="404_page"'    . selected($template_value, '404_page', false)    . '>' . esc_html__('404 Page', 'primekit-addons')       . '</option>';
        echo '<option value="archive_page"'. selected($template_value, 'archive_page', false). '>' . esc_html__('Archive Page', 'primekit-addons')  . '</option>';
        if (class_exists('WooCommerce')) {
            echo '<option value="shop_single"'  . selected($template_value, 'shop_single', false)  . '>' . esc_html__('Single Product', 'primekit-addons') . '</option>';
            echo '<option value="shop_archive"' . selected($template_value, 'shop_archive', false) . '>' . esc_html__('Shop Archive', 'primekit-addons')   . '</option>';
        }
        echo '</select>';
        echo '</td>';
        echo '</tr>';

        // HF-only rows
        $hf_style = $is_hf ? '' : ' style="display:none"';

        // --- Display Rules ---
        echo '<tr class="primekit-hf-meta-row"' . $hf_style . '>';
        echo '<th>' . esc_html__('Display On:', 'primekit-addons') . '</th>';
        echo '<td>';
        echo '<select id="primekit-mb-display-select" class="primekit-mb-select">';
        echo ModalMarkup::get_condition_options_html();
        echo '</select>';
        echo '<div class="primekit-mb-rule-tags" id="primekit-mb-display-tags">';
        foreach ($display_rules as $rule) {
            $lbl = isset($condition_options[$rule]) ? $condition_options[$rule] : $rule;
            echo '<span class="primekit-hf-rule-tag primekit-hf-tag-display" data-value="' . esc_attr($rule) . '">';
            echo '<span class="primekit-hf-rule-tag-label">' . esc_html($lbl) . '</span>';
            echo '<button type="button" class="primekit-hf-rule-tag-remove">&times;</button></span>';
        }
        echo '</div>';
        echo '<input type="hidden" name="primekit_hf_display_rules" id="primekit-mb-display-rules-input" value="' . esc_attr(wp_json_encode($display_rules)) . '">';
        echo '<div class="primekit-mb-rule-actions">';
        echo '<button type="button" class="primekit-hf-add-rule-btn" id="primekit-mb-add-display-rule">' . esc_html__('+ Add Display Rule', 'primekit-addons') . '</button>';
        echo '<button type="button" class="primekit-hf-add-rule-btn primekit-hf-exclude-btn" id="primekit-mb-add-exclusion-rule">' . esc_html__('+ Add Exclusion Rule', 'primekit-addons') . '</button>';
        echo '</div>';
        echo '<div class="primekit-mb-rule-tags primekit-hf-exclusion-tags" id="primekit-mb-exclusion-tags">';
        foreach ($exclusion_rules as $rule) {
            $lbl = isset($condition_options[$rule]) ? $condition_options[$rule] : $rule;
            echo '<span class="primekit-hf-rule-tag primekit-hf-tag-exclusion" data-value="' . esc_attr($rule) . '">';
            echo '<span class="primekit-hf-rule-tag-label">' . esc_html($lbl) . '</span>';
            echo '<button type="button" class="primekit-hf-rule-tag-remove">&times;</button></span>';
        }
        echo '</div>';
        echo '<input type="hidden" name="primekit_hf_exclusion_rules" id="primekit-mb-exclusion-rules-input" value="' . esc_attr(wp_json_encode($exclusion_rules)) . '">';
        echo '</td></tr>';

        // --- User Roles ---
        echo '<tr class="primekit-hf-meta-row"' . $hf_style . '>';
        echo '<th>' . esc_html__('User Roles:', 'primekit-addons') . '</th>';
        echo '<td>';
        echo '<select id="primekit-mb-role-select" class="primekit-mb-select">';
        echo ModalMarkup::get_user_role_options_html();
        echo '</select>';
        echo '<div class="primekit-mb-rule-tags" id="primekit-mb-role-tags">';
        foreach ($user_roles as $role) {
            $lbl = isset($role_options[$role]) ? $role_options[$role] : $role;
            echo '<span class="primekit-hf-rule-tag primekit-hf-tag-user" data-value="' . esc_attr($role) . '">';
            echo '<span class="primekit-hf-rule-tag-label">' . esc_html($lbl) . '</span>';
            echo '<button type="button" class="primekit-hf-rule-tag-remove">&times;</button></span>';
        }
        echo '</div>';
        echo '<input type="hidden" name="primekit_hf_user_roles" id="primekit-mb-user-roles-input" value="' . esc_attr(wp_json_encode($user_roles)) . '">';
        echo '<button type="button" class="primekit-hf-add-rule-btn" id="primekit-mb-add-user-rule">' . esc_html__('+ Add User Rule', 'primekit-addons') . '</button>';
        echo '</td></tr>';

        // --- Canvas Template ---
        echo '<tr class="primekit-hf-meta-row"' . $hf_style . '>';
        echo '<th>' . esc_html__('Elementor Canvas Template:', 'primekit-addons') . '</th>';
        echo '<td><input type="checkbox" name="primekit_hf_canvas" value="1"' . checked($canvas, '1', false) . '> ';
        echo esc_html__('Enable Elementor Canvas layout for this template', 'primekit-addons') . '</td></tr>';

        echo '</tbody></table>';

        ?>
        <script>
        jQuery(function($) {
            // Show/hide HF rows on type change
            function toggleHFRows() {
                var t = $('#primekit_themebuilder_select').val();
                $('.primekit-hf-meta-row').toggle(t === 'header' || t === 'footer');
            }
            $('#primekit_themebuilder_select').on('change', toggleHFRows);

            // Generic tag adder for metabox
            function mbAddTag(selectId, tagsId, inputId, tagClass) {
                var $sel = $('#' + selectId);
                var val  = $sel.val();
                var lbl  = $sel.find('option:selected').text();
                if (!val) return;
                var $tags = $('#' + tagsId);
                if ($tags.find('[data-value="' + val + '"]').length) return;
                var $tag = $('<span class="primekit-hf-rule-tag ' + tagClass + '" data-value="' + val + '">' +
                    '<span class="primekit-hf-rule-tag-label">' + lbl + '</span>' +
                    '<button type="button" class="primekit-hf-rule-tag-remove">&times;</button></span>');
                $tag.find('.primekit-hf-rule-tag-remove').on('click', function() {
                    $tag.remove(); mbSync(tagsId, inputId);
                });
                $tags.append($tag);
                mbSync(tagsId, inputId);
            }

            function mbSync(tagsId, inputId) {
                var vals = [];
                $('#' + tagsId + ' .primekit-hf-rule-tag').each(function() { vals.push($(this).data('value')); });
                $('#' + inputId).val(JSON.stringify(vals));
            }

            // Wire existing tag remove buttons
            $('.primekit-hf-rule-tag-remove').on('click', function() {
                var $tag = $(this).closest('.primekit-hf-rule-tag');
                var $container = $tag.closest('.primekit-mb-rule-tags');
                $tag.remove();
                if ($container.attr('id') === 'primekit-mb-display-tags')    mbSync('primekit-mb-display-tags',    'primekit-mb-display-rules-input');
                if ($container.attr('id') === 'primekit-mb-exclusion-tags')  mbSync('primekit-mb-exclusion-tags',  'primekit-mb-exclusion-rules-input');
                if ($container.attr('id') === 'primekit-mb-role-tags')       mbSync('primekit-mb-role-tags',       'primekit-mb-user-roles-input');
            });

            $('#primekit-mb-add-display-rule').on('click',   function() { mbAddTag('primekit-mb-display-select',  'primekit-mb-display-tags',    'primekit-mb-display-rules-input',   'primekit-hf-tag-display');   mbSync('primekit-mb-display-tags',   'primekit-mb-display-rules-input'); });
            $('#primekit-mb-add-exclusion-rule').on('click', function() { mbAddTag('primekit-mb-display-select',  'primekit-mb-exclusion-tags',  'primekit-mb-exclusion-rules-input', 'primekit-hf-tag-exclusion'); mbSync('primekit-mb-exclusion-tags', 'primekit-mb-exclusion-rules-input'); });
            $('#primekit-mb-add-user-rule').on('click',      function() { mbAddTag('primekit-mb-role-select',     'primekit-mb-role-tags',       'primekit-mb-user-roles-input',      'primekit-hf-tag-user');      mbSync('primekit-mb-role-tags',      'primekit-mb-user-roles-input'); });
        });
        </script>
        <?php
    }
    
    /**
     * Saves the meta box data.
     * 
     * This function checks if the nonce is valid and if the post ID is valid
     * before saving the meta box data.
     * 
     * @param int $post_id The ID of the post.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function save_meta_box_data($post_id)
    {
        if (!isset($_POST['primekit_custom_box_nonce']) || !wp_verify_nonce($_POST['primekit_custom_box_nonce'], 'primekit_custom_box')) {
            return;
        }

        $post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';
        if ('primekit_library' === $post_type) {
            if (!current_user_can('edit_page', $post_id)) return;
        } else {
            if (!current_user_can('edit_post', $post_id)) return;
        }

        $new_template_value = isset($_POST['primekit_themebuilder_select']) ? sanitize_text_field($_POST['primekit_themebuilder_select']) : '';
        update_post_meta($post_id, 'primekit_themebuilder_select', $new_template_value);

        if (in_array($new_template_value, ['header', 'footer'], true)) {
            $display_rules   = isset($_POST['primekit_hf_display_rules'])   ? json_decode(sanitize_text_field(wp_unslash($_POST['primekit_hf_display_rules'])),   true) : [];
            $exclusion_rules = isset($_POST['primekit_hf_exclusion_rules']) ? json_decode(sanitize_text_field(wp_unslash($_POST['primekit_hf_exclusion_rules'])), true) : [];
            $user_roles      = isset($_POST['primekit_hf_user_roles'])      ? json_decode(sanitize_text_field(wp_unslash($_POST['primekit_hf_user_roles'])),      true) : [];
            $canvas          = isset($_POST['primekit_hf_canvas']) && $_POST['primekit_hf_canvas'] === '1' ? '1' : '';

            update_post_meta($post_id, 'primekit_hf_display_rules',   is_array($display_rules)   ? $display_rules   : []);
            update_post_meta($post_id, 'primekit_hf_exclusion_rules', is_array($exclusion_rules) ? $exclusion_rules : []);
            update_post_meta($post_id, 'primekit_hf_user_roles',      is_array($user_roles)      ? $user_roles      : []);
            update_post_meta($post_id, 'primekit_hf_canvas',          $canvas);
        } else {
            delete_post_meta($post_id, 'primekit_hf_display_rules');
            delete_post_meta($post_id, 'primekit_hf_exclusion_rules');
            delete_post_meta($post_id, 'primekit_hf_user_roles');
            delete_post_meta($post_id, 'primekit_hf_canvas');
        }
    }



}
