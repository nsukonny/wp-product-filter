<?php
/**
 * Helpers for Products
 *
 * @since 1.0.0
 *
 * @package WPProductFilter
 *
 * @domain  wp-product-filter
 */

namespace WPProductFilter;

defined('ABSPATH') || exit;

class Product
{

    /**
     * Get list of products from api-data.json file
     *
     * @param array $product Product data
     * @param string $color Color option
     * @param string $size Size option
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function is_available(array $product, string $color, string $size = ''): bool
    {
        if (empty($product['combinations']) || empty($color)) {
            return false;
        }

        $all_combinations_keys = array_keys($product['combinations']);
        $found_combinations = array();
        foreach ($all_combinations_keys as $combination_key) {
            if (!empty($size) && $combination_key === $color . '_' . $size) {
                $found_combinations[$combination_key] = $product['combinations'][$color . '_' . $size] ?? null;
                break;
            } else if (empty($size) && str_starts_with($combination_key, $color . '_')) {
                $found_combinations[$combination_key] = $product['combinations'][$combination_key] ?? null;
            }
        }

        foreach ($found_combinations as $key => $combination) {
            if ($combination['available']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if filter option is disabled
     *
     * @param array $product Product data
     * @param string $color Color option
     * @param string $size Size option
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function is_disabled(array $product, string $color, string $size = ''): bool
    {
        if (!self::is_available($product, $color, $size)) {
            return true;
        }

        if (!empty($size) && !empty($product['compatibility'][$color]) && !in_array($size, $product['compatibility'][$color])) {
            return true;
        }

        return false;
    }
}
