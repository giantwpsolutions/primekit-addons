<?php
/**
 * Library API class for PrimeKit Addons
 *
 * @package PrimeKit
 */

namespace PrimeKit\Admin\Inc\Templates;

use Elementor\TemplateLibrary\Source_Base;

defined('ABSPATH') || die();

class Library_Source extends Source_Base
{
    const LIBRARY_CACHE_KEY = 'primekit_library_cache';
    const API_TEMPLATES_INFO_URL = 'https://demo.primekitaddons.com/wp-json/primekit/v1/templates';
    const API_TEMPLATE_DATA_URL = 'https://demo.primekitaddons.com/wp-json/primekit/v1/json';

    public function get_id()
    {
        return 'primekit-library';
    }

    public function get_title()
    {
        return __('PrimeKit Library', 'primekit');
    }

    public function register_data()
    {
    }

    public function save_item($template_data)
    {
        return new \WP_Error('invalid_request', __('Cannot save template to PrimeKit Library', 'primekit'));
    }

    public function update_item($new_data)
    {
        return new \WP_Error('invalid_request', __('Cannot update template in PrimeKit Library', 'primekit'));
    }

    public function delete_template($template_id)
    {
        return new \WP_Error('invalid_request', __('Cannot delete template from PrimeKit Library', 'primekit'));
    }

    public function export_template($template_id)
    {
        return new \WP_Error('invalid_request', __('Cannot export template from PrimeKit Library', 'primekit'));
    }

    public function get_items($args = [])
    {
        $library_data = self::get_library_data();
        $templates = [];

        if (!empty($library_data['templates'])) {
            foreach ($library_data['templates'] as $template_data) {
                $templates[] = $this->prepare_template($template_data);
            }
        }

        return $templates;
    }

    public function get_tags()
    {
        $library_data = self::get_library_data();

        return (!empty($library_data['tags']) ? $library_data['tags'] : []);
    }

    public function get_type_tags()
    {
        $library_data = self::get_library_data();

        return (!empty($library_data['type_tags']) ? $library_data['type_tags'] : []);
    }

    private function prepare_template(array $template_data)
    {
        return [
            'template_id' => $template_data['id'],
            'title'       => $template_data['title'],
            'type'        => $template_data['type'],
            'thumbnail'   => $template_data['thumbnail'],
            'date'        => $template_data['created_at'],
            'tags'        => $template_data['tags'],
            'isPro'       => $template_data['is_pro'],
            'url'         => $template_data['url'],
        ];
    }

    private static function request_library_data($force_update = false)
    {
        $data = get_option(self::LIBRARY_CACHE_KEY);

        if (!empty($data) && !$force_update) {
            return $data;
        }

        // Always use remote API
        $response = wp_remote_get(self::API_TEMPLATES_INFO_URL, [
            'timeout' => 30,
            'sslverify' => false
        ]);

        if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);

            if (!empty($data) && is_array($data)) {
                update_option(self::LIBRARY_CACHE_KEY, $data, 'no');
                return $data;
            }
        }

        // Return empty array if remote request fails
        update_option(self::LIBRARY_CACHE_KEY, []);
        return false;
    }

    public static function get_library_data($force_update = true)
    {
        self::request_library_data($force_update);

        $data = get_option(self::LIBRARY_CACHE_KEY);

        if (empty($data)) {
            return [];
        }

        return $data;
    }

    public function get_item($template_id)
    {
        $templates = $this->get_items();

        return $templates[$template_id];
    }

    public static function request_template_data($template_id)
    {
        if (empty($template_id)) {
            return;
        }

        $api_url = self::API_TEMPLATE_DATA_URL . '/' . $template_id;
        $response = wp_remote_get($api_url, [
            'timeout' => 30,
            'sslverify' => false
        ]);

        return wp_remote_retrieve_body($response);
    }

    public function get_data(array $args)
    {
        $data = self::request_template_data($args['template_id']);

        $data = json_decode($data, true);

        if (empty($data) || empty($data['content'])) {
            throw new \Exception(__('Template does not have any content', 'primekit'));
        }

        $data['content'] = $this->replace_elements_ids($data['content']);

        // Import images immediately before processing
        $data['content'] = $this->import_images($data['content']);

        $data['content'] = $this->process_export_import_content($data['content'], 'on_import');

        $post_id = $args['editor_post_id'];
        $document = \Elementor\Plugin::instance()->documents->get($post_id);

        if ($document) {
            $data['content'] = $document->get_elements_raw_data($data['content'], true);
        }

        return $data;
    }

    /**
     * Import images from remote URLs to local media library
     */
    private function import_images($content)
    {
        if (!is_array($content)) {
            return $content;
        }

        // Cache for downloaded images to avoid duplicates
        static $image_cache = [];

        foreach ($content as $key => $value) {
            if (is_array($value)) {
                $content[$key] = $this->import_images($value);
            } elseif (is_string($value) && $this->is_image_url($value)) {
                // Only import remote demo site images
                if (strpos($value, 'demo.primekitaddons.com') !== false) {
                    // Check cache first to avoid re-downloading same image
                    if (isset($image_cache[$value])) {
                        $content[$key] = $image_cache[$value];
                    } else {
                        $local_url = $this->download_image($value);
                        if ($local_url) {
                            $image_cache[$value] = $local_url;
                            $content[$key] = $local_url;
                        }
                    }
                }
            }
        }

        return $content;
    }

    /**
     * Check if string is an image URL
     */
    private function is_image_url($url)
    {
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        return in_array($extension, $image_extensions);
    }

    /**
     * Download image and import to media library
     */
    private function download_image($url)
    {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $tmp = download_url($url);

        if (is_wp_error($tmp)) {
            return false;
        }

        $file_array = [
            'name' => basename(parse_url($url, PHP_URL_PATH)),
            'tmp_name' => $tmp
        ];

        $id = media_handle_sideload($file_array, 0);

        @unlink($tmp);

        if (is_wp_error($id)) {
            return false;
        }

        return wp_get_attachment_url($id);
    }
}
