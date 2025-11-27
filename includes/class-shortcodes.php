<?php
/**
 * Support shortcodes in the plugin text
 *
 * @since 1.0.0
 */

namespace WPProductFilter;

use Singleton;

defined('ABSPATH') || exit;

class Shortcodes
{

    use Singleton;

    /**
     * Init shortcodes
     *
     * @since 1.0.0
     */
    public function init(): void
    {
        add_shortcode('wp_product_filter', array($this, 'render_filter'));
    }

    /**
     * Draw filter with shortcodes
     *
     * @return string
     */
    public static function render_filter(): string
    {
        $args = array(
            'products' => API::get_products_list(),
        );

        return Loader::load_template('wrapper', false, $args);
    }

}
