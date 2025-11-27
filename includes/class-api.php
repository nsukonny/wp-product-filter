<?php
/**
 * Work with API of the plugin
 *
 * @since 1.0.0
 *
 * @package WPProductFilter
 *
 * @domain  wp-product-filter
 */

namespace WPProductFilter;

defined('ABSPATH') || exit;

class API
{

    /**
     * Get list of products from api-data.json file
     *
     * @return array
     *
     * @since 1.0.0
     */
    public static function get_products_list(): array
    {
        $products = get_transient('wppf_products_list');
        if ($products !== false) {
            return apply_filters('wppf-get_products_list', $products);
        }

        $file_path = WPPF_PATH . '/api-data.json';

        if (!file_exists($file_path)) {
            return array();
        }

        $json_data = file_get_contents($file_path);
        $data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return array();
        }

        if (empty($data['products']) || !is_array($data['products'])) {
            return array();
        }

        $products = $data['products'];
        set_transient('wppf_products_list', $products, 10 * MINUTE_IN_SECONDS);

        return apply_filters('wppf-get_products_list', $products);
    }
}
