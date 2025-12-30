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
                update_option(self::LIBRARY_CACHE_KEY, $data, false);
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

        // Import remote images and update URLs (preserving original paths)
        $data['content'] = $this->import_remote_images($data['content']);

        $post_id = $args['editor_post_id'];
        $document = \Elementor\Plugin::instance()->documents->get($post_id);

        if ($document) {
            $data['content'] = $document->get_elements_raw_data($data['content'], true);
        }

        return $data;
    }

    /**
     * Import remote images and replace URLs with local ones
     *
     * @param array $content Template content
     * @return array Modified content with local image URLs
     */
    private function import_remote_images($content)
    {
        // Require WordPress media handling functions
        if (!function_exists('media_sideload_image')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

        return $this->process_content_images($content);
    }

    /**
     * Recursively process content and download images
     *
     * @param mixed $data Content data (array or value)
     * @return mixed Processed data
     */
    private function process_content_images($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Check if this is an image URL that needs downloading
                if ($key === 'url' && is_string($value) && $this->is_remote_image_url($value)) {
                    // Download the image and get the new local URL
                    $local_url = $this->download_image($value);
                    if ($local_url) {
                        $data[$key] = $local_url;

                        // If there's an 'id' field, update it with the new attachment ID
                        if (isset($data['id'])) {
                            $attachment_id = attachment_url_to_postid($local_url);
                            if ($attachment_id) {
                                $data['id'] = $attachment_id;
                            }
                        }
                    }
                } else {
                    // Recursively process nested arrays
                    $data[$key] = $this->process_content_images($value);
                }
            }
        }

        return $data;
    }

    /**
     * Check if URL is a remote image that needs downloading
     *
     * @param string $url URL to check
     * @return bool True if remote image URL
     */
    private function is_remote_image_url($url)
    {
        if (!is_string($url) || empty($url)) {
            return false;
        }

        // Check if it's a valid URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // Check if it's an image extension
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (!in_array($extension, $image_extensions)) {
            return false;
        }

        // Check if it's from the remote server (not already local)
        $site_url = get_site_url();
        return (strpos($url, $site_url) === false);
    }

    /**
     * Download remote image to local media library
     *
     * @param string $image_url Remote image URL
     * @return string|false Local image URL on success, false on failure
     */
    private function download_image($image_url)
    {
        // Check if we already downloaded this image (avoid duplicates)
        $existing_id = $this->get_cached_image_id($image_url);
        if ($existing_id) {
            $local_url = wp_get_attachment_url($existing_id);
            if ($local_url) {
                return $local_url;
            }
        }

        // Parse the URL to get the path structure
        $parsed_url = parse_url($image_url);
        $path_parts = explode('/', trim($parsed_url['path'], '/'));

        // Get filename
        $filename = array_pop($path_parts);

        // Extract year/month from remote path (e.g., wp-content/uploads/2024/10/image.jpg)
        $year_month = '';
        if (count($path_parts) >= 2) {
            $month = array_pop($path_parts);
            $year = array_pop($path_parts);

            // Validate year/month format
            if (is_numeric($year) && is_numeric($month) && strlen($year) === 4 && strlen($month) <= 2) {
                $year_month = $year . '/' . $month;
            }
        }

        // Download file to temp location
        $temp_file = download_url($image_url);

        if (is_wp_error($temp_file)) {
            error_log('PrimeKit: Failed to download image: ' . $image_url . ' - ' . $temp_file->get_error_message());
            return false;
        }

        // Set up the target directory preserving original year/month
        $upload_dir = wp_upload_dir(null, false, true);

        if (!empty($year_month)) {
            // Use the original year/month from remote URL
            $target_dir = $upload_dir['basedir'] . '/' . $year_month;
            $target_url = $upload_dir['baseurl'] . '/' . $year_month;
        } else {
            // Fallback to current date if we can't parse year/month
            $target_dir = $upload_dir['path'];
            $target_url = $upload_dir['url'];
        }

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        // Move file to target directory
        $target_file = $target_dir . '/' . $filename;

        if (!@rename($temp_file, $target_file)) {
            @unlink($temp_file);
            error_log('PrimeKit: Failed to move file to: ' . $target_file);
            return false;
        }

        // Get file type
        $wp_filetype = wp_check_filetype($filename, null);

        // Prepare attachment data
        $attachment = [
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];

        // Insert attachment
        $attachment_id = wp_insert_attachment($attachment, $target_file);

        if (is_wp_error($attachment_id)) {
            @unlink($target_file);
            error_log('PrimeKit: Failed to insert attachment: ' . $filename);
            return false;
        }

        // Generate attachment metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $target_file);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        // Cache the mapping
        $this->cache_image_id($image_url, $attachment_id);

        // Return the local URL
        $local_url = $target_url . '/' . $filename;

        error_log('PrimeKit: Successfully downloaded: ' . $filename . ' to ' . $year_month . ' -> ' . $local_url);

        return $local_url;
    }

    /**
     * Cache image URL to attachment ID mapping
     *
     * @param string $remote_url Remote image URL
     * @param int $attachment_id Local attachment ID
     */
    private function cache_image_id($remote_url, $attachment_id)
    {
        $cache_key = 'primekit_image_' . md5($remote_url);
        set_transient($cache_key, $attachment_id, WEEK_IN_SECONDS);
    }

    /**
     * Get cached attachment ID for remote URL
     *
     * @param string $remote_url Remote image URL
     * @return int|false Attachment ID or false if not cached
     */
    private function get_cached_image_id($remote_url)
    {
        $cache_key = 'primekit_image_' . md5($remote_url);
        return get_transient($cache_key);
    }

}
