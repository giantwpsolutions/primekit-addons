<?php
/**
 * Column.php
 *
 * This file contains the Column class, which is responsible for adding
 * custom columns to the Theme Builder page and populating them with data.
 *
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Admin
 * @since 1.0.0
 */

namespace PrimeKit\Admin\Inc\ThemeBuilder\Admin;

//don't load directly
if (!defined('ABSPATH')) exit;

/**
 * Class Column
 * 
 * Handles the addition of custom columns to the Theme Builder page and populating them with data.
 * 
 * @package PrimeKit\Admin\Inc\ThemeBuilder\Admin
 * @since 1.0.0
 */
class Column {
    public function __construct()
    {
        // Add custom columns to the 'primekit_library' post type
        add_filter('manage_primekit_library_posts_columns', array($this, 'add_custom_columns'));

        // Populate the custom columns with data
        add_action('manage_primekit_library_posts_custom_column', array($this, 'populate_custom_columns'), 10, 2);

        // Make the 'Type' column sortable
        add_filter('manage_edit-primekit_library_sortable_columns', array($this, 'make_columns_sortable'));

        // Handle sorting by the 'Type' column
        add_action('pre_get_posts', array($this, 'sort_by_type_column'));

        // Replace Edit link for H/F templates so it opens our modal instead
        add_filter('post_row_actions', array($this, 'modify_hf_edit_action'), 10, 2);
    }

    /**
     * Adds custom columns to the 'primekit_library' post type.
     * 
     * This function adds a new column for the 'Type' column and removes the default 'Title' column.
     * 
     * @param array $columns The existing columns.
     * 
     * @return array The modified columns.
     * 
     * @since 1.0.0
     */
    public function add_custom_columns($columns)
    {
      
        $new_columns = array(
            'cb' => $columns['cb'],  // Checkbox column
            'title' => esc_html__('Title', 'primekit-addons'),
            'primekit_type' => esc_html__('Type', 'primekit-addons'), 
        );
    
        // Merge the rest of the columns after 'Type'
        unset($columns['title']); 
        return array_merge($new_columns, $columns); 
    }
    

    /**
     * Populates the custom columns with data from 'primekit_themebuilder_select'.
     * 
     * This function checks if the current column is the 'Type' column and displays
     * the corresponding template type label.
     * 
     * @param string $column The column name.
     * @param int $post_id The ID of the post.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function populate_custom_columns($column, $post_id)
    {
        if ($column !== 'primekit_type') return;

        $type_value = get_post_meta($post_id, 'primekit_themebuilder_select', true);

        $type_labels = [
            'header'       => __('Header',         'primekit-addons'),
            'footer'       => __('Footer',         'primekit-addons'),
            'single_post'  => __('Single Post',    'primekit-addons'),
            'single_page'  => __('Single Page',    'primekit-addons'),
            'search_page'  => __('Search Page',    'primekit-addons'),
            '404_page'     => __('404 Page',       'primekit-addons'),
            'archive_page' => __('Archive Page',   'primekit-addons'),
            'shop_single'  => __('Single Product', 'primekit-addons'),
            'shop_archive' => __('Shop Archive',   'primekit-addons'),
        ];

        $condition_labels = [
            // HFB-style keys
            'basic-global'    => __('Entire Website',   'primekit-addons'),
            'basic-singulars' => __('All Singulars',    'primekit-addons'),
            'basic-archives'  => __('All Archives',     'primekit-addons'),
            'special-404'     => __('404 Page',         'primekit-addons'),
            'special-search'  => __('Search Page',      'primekit-addons'),
            'special-blog'    => __('Blog Page',        'primekit-addons'),
            'special-front'   => __('Front Page',       'primekit-addons'),
            'special-date'    => __('Date Archive',     'primekit-addons'),
            'special-author'  => __('Author Archive',   'primekit-addons'),
            'special-woo-shop'=> __('WooCommerce Shop', 'primekit-addons'),
            'post|all'        => __('All Posts',        'primekit-addons'),
            'post|all|archive'=> __('Post Archives',    'primekit-addons'),
            'page|all'        => __('All Pages',        'primekit-addons'),
            'product|all'     => __('All Products',     'primekit-addons'),
            'product|all|archive' => __('Product Archives', 'primekit-addons'),
            // Legacy keys (backward compat)
            'entire_site' => __('Entire Site',    'primekit-addons'),
            'front_page'  => __('Front Page',     'primekit-addons'),
            'single_post' => __('Single Posts',   'primekit-addons'),
            'single_page' => __('Single Pages',   'primekit-addons'),
            'archive'     => __('Archive',        'primekit-addons'),
            'search'      => __('Search',         'primekit-addons'),
            '404'         => __('404',            'primekit-addons'),
        ];

        if (empty($type_value) || !isset($type_labels[$type_value])) {
            echo esc_html__('Unknown Type', 'primekit-addons');
            return;
        }

        $label = $type_labels[$type_value];

        // Append condition badge(s) for header/footer templates
        if (in_array($type_value, ['header', 'footer'], true)) {
            $display_rules = get_post_meta($post_id, 'primekit_hf_display_rules', true);

            // Backward compat: old single-value string
            if (!is_array($display_rules)) {
                $legacy        = get_post_meta($post_id, 'primekit_hf_condition', true);
                $display_rules = $legacy ? [$legacy] : ['basic-global'];
            }

            echo esc_html($label) . ' ';
            foreach ($display_rules as $rule) {
                $cond_label = isset($condition_labels[$rule]) ? $condition_labels[$rule] : $rule;
                echo '<span style="background:#e2e8f0;padding:2px 7px;border-radius:3px;font-size:11px;color:#555;margin-right:3px">' . esc_html($cond_label) . '</span>';
            }
        } else {
            echo esc_html($label);
        }
    }
    

    /**
     * Makes the 'Type' column sortable.
     * 
     * This function adds the 'Type' column to the sortable columns array.
     * 
     * @param array $columns The existing sortable columns.
     * 
     * @return array The modified sortable columns.
     * 
     * @since 1.0.0
     */
    public function make_columns_sortable($columns)
    {
        $columns['primekit_type'] = 'primekit_type';
        return $columns;
    }

    /**
     * Handles sorting by the 'Type' column.
     * 
     * This function checks if the current query is the main query and if the
     * 'orderby' parameter is set to 'primekit_type'. If so, it sets the meta key
     * and order to sort by the 'primekit_themebuilder_select' meta field.
     * 
     * @param \WP_Query $query The current query.
     * 
     * @return void
     * 
     * @since 1.0.0
     */
    public function sort_by_type_column($query)
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ('primekit_type' === $query->get('orderby')) {
            $query->set('meta_key', 'primekit_themebuilder_select');
            $query->set('orderby', 'meta_value');
        }
    }

    /**
     * Replaces the default "Edit" row action for header/footer templates
     * with a JS-triggered link that opens the HF modal pre-filled.
     */
    public function modify_hf_edit_action($actions, $post)
    {
        if ($post->post_type !== 'primekit_library') return $actions;

        $type = get_post_meta($post->ID, 'primekit_themebuilder_select', true);
        if (!in_array($type, ['header', 'footer'], true)) return $actions;

        if (isset($actions['edit'])) {
            $actions['edit'] = sprintf(
                '<a href="#" class="primekit-hf-edit-link" data-post-id="%d">%s</a>',
                $post->ID,
                __('Edit', 'primekit-addons')
            );
        }

        return $actions;
    }
}
