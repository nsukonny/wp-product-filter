<?php
/**
 * Init ajax hooks
 *
 * @since 1.0.0
 *
 * @package WPProductFilter
 *
 * @domain  wp-product-filter
 */

namespace WPProductFilter;

use Singleton;

defined('ABSPATH') || exit;

class Ajax
{

    use Singleton;

    /**
     * Init core of the plugin
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function init(): void
    {
        add_action('wp_ajax_wpp_get_products', array($this, 'ajax_get_products'));
        add_action('wp_ajax_nopriv_wpp_get_products', array($this, 'ajax_get_products'));
    }

    /**
     * Ajax handler to get products
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function ajax_get_products(): void
    {
        check_ajax_referer('product_variants_nonce', 'nonce');

        $product_id = isset($_POST['product_id']) ? sanitize_text_field(wp_unslash($_POST['product_id'])) : '';

        if (empty($product_id)) {
            wp_send_json_error(['message' => 'Product ID is required'], 400);
        }

        $data = my_product_api_get_data();

        if (!$data || empty($data['products'][$product_id])) {
            wp_send_json_error(['message' => 'Product not found'], 404);
        }

        wp_send_json_success($data['products'][$product_id]);
    }
}
