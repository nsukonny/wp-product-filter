<?php
/**
 * Plugin Name: WP Product Filter
 * Plugin URI: toteam.org
 * Description: Display products filter by shortcode [wp_product_filter]
 * Version: 1.0.0
 * Author: NSukonny
 * Author URI: nsukonny.agency
 * Text Domain: wppf
 * Domain Path: /languages
 *
 * @package WPProductFilter
 */

namespace WPProductFilter;

defined('ABSPATH') || exit;

define('WPPF_PATH', plugin_dir_path(__FILE__));
define('WPPF_URL', plugin_dir_url(__FILE__));
define('WPPF_VERSION', '1.0.0');

require_once WPPF_PATH . 'includes/trait-singleton.php';
require_once WPPF_PATH . 'includes/class-loader.php';

Loader::init_autoload(__NAMESPACE__, __DIR__);

add_action('init', array(Init::class, 'instance'));
