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
        check_ajax_referer('_wppf_nonce');

        $data['products'] = API::get_products_list();

        if (empty($data['products'])) {
            wp_send_json_error(['message' => 'Products not found'], 404);
        }

        wp_send_json_success($data);
    }
}
