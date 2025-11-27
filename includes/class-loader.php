<?php
/**
 * Loader for includes, classes and assets
 */

namespace WPProductFilter;

class Loader
{

    use \Singleton;

    /**
     * @var array
     */
    private static $autoload_namespaces = array();

    public function init(): void
    {
        $this->load_assets();
    }

    /**
     * Init classes, assets and other
     *
     * @param $namespace
     * @param $dir_path
     *
     * @since 1.0.0
     */
    public static function init_autoload($namespace, $dir_path): void
    {
        self::$autoload_namespaces[] = array(
            'namespace' => $namespace,
            'dirpath' => $dir_path,
        );

        spl_autoload_register(__CLASS__ . '::autoload');

        add_action('init', array(self::class, 'instance'));
    }

    /**
     * Include files from called class
     *
     * @param string $class Namespace for needed class
     *
     * @since 1.0.0
     */
    public static function autoload(string $class): void
    {
        $class_explode = explode('\\', $class);
        if (!self::is_for_framework($class_explode)) {
            return;
        }

        $namespaces = array_column(self::$autoload_namespaces, 'namespace');
        $namespace_key = array_search($class_explode[0], $namespaces, true);
        $includes_path = self::$autoload_namespaces[$namespace_key]['dirpath'] . '/includes';
        $file_path = '';
        $file_types = array('class', 'trait', 'abstract');
        $file_name = strtolower(str_replace('_', '-', array_pop($class_explode))) . '.php';

        foreach ($class_explode as $key => $classname) {
            if (0 !== $key) {
                $file_path .= '/' . strtolower(str_replace('_', '-', $classname));
            }
        }

        foreach ($file_types as $type) {
            $filename = $includes_path . $file_path . '/' . $type . '-' . $file_name;

            if (file_exists($filename)) {
                require_once $filename;

                return;
            }
        }
    }

    /**
     * Check if we need use framework for that call
     *
     * @param array $class_explode Called class name exploaded by \
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public static function is_for_framework(array $class_explode): bool
    {
        if (empty(self::$autoload_namespaces)) {
            return false;
        }

        $namespaces = array_column(self::$autoload_namespaces, 'namespace');

        return in_array($class_explode[0], $namespaces, true);
    }

    /**
     * Load template file
     *
     * @param string $template_name Template name.
     * @param bool $is_echo Echo the template or return as string.
     * @param array $args Arguments to pass to template.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public static function load_template(string $template_name, bool $is_echo = false, array $args = array()): string
    {
        ob_start();

        $theme_template = locate_template(array('templates/' . $template_name . '.php'), false, false) ?: WPPF_PATH . 'templates/' . $template_name . '.php';

        $theme_template = apply_filters('wp_product_filter_' . $template_name . '_template', $theme_template, $template_name);

        load_template($theme_template, false, $args);

        if ($is_echo) {
            echo ob_get_clean();

            return '';
        }

        return ob_get_clean();
    }

    /**
     * Find and enqueue admin styles and scripts
     *
     * @since 1.0.0
     */
    public function enqueue_admin_scripts(): void
    {
        $namespace = strtolower(self::$autoload_namespaces[0]['namespace']);

        $enqueue = $this->enqueue_style($namespace, 'assets/css/admin.min.css');
        if (!$enqueue) {
            $this->enqueue_style($namespace, 'assets/css/admin.css');
        }

        $localize = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '_ajax_nonce' => wp_create_nonce('_wppf_nonce'),
        );
        $deps = array(
            'jquery',
            'jquery-ui-sortable',
        );
        $enqueue = $this->enqueue_script($namespace, 'assets/js/admin.min.js', $localize, $deps);
        if (!$enqueue) {
            $this->enqueue_script($namespace, 'assets/js/admin.js', $localize, $deps);
        }
    }

    /**
     * Enqueue styles and scripts
     *
     * @since 1.0.0
     */
    public function enqueue_scripts(): void
    {
        $namespace = strtolower(self::$autoload_namespaces[0]['namespace']);
        $enqueue = $this->enqueue_style(WPPF_URL, 'assets/css/style.min.css');
        if (!$enqueue) {
            $this->enqueue_style($namespace, 'assets/css/style.css');
        }

        $localize = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '_ajax_nonce' => wp_create_nonce('_wppf_nonce'),
        );
        $enqueue = $this->enqueue_script($namespace, 'assets/js/script.min.js', $localize);
        if (!$enqueue) {
            $this->enqueue_script($namespace, 'assets/js/script.js', $localize);
        }
    }

    /**
     * Find and load default assets
     *
     * @since 1.0.0
     */
    private function load_assets(): void
    {
        if (!function_exists('is_admin')) {
            return;
        }

        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'), 8);
        } else {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 8);
        }

        add_action('plugins_loaded', array($this, 'load_text_domain'));
    }

    /**
     * Check js script and add it to system
     *
     * @param string $slug Slug name for enqueue.
     * @param string $css_file Path to js file from plugin folder.
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function enqueue_style(string $slug, string $css_file): bool
    {
        if (file_exists(WPPF_PATH . '/' . $css_file)) {
            wp_enqueue_style(
                $slug,
                WPPF_URL . $css_file,
                array(),
                WPPF_VERSION
            );

            return true;
        }

        return false;
    }

    /**
     * Check js script and add it to system
     *
     * @param string $slug Slug name for enqueue.
     * @param string $js_file Path to js file from plugin folder.
     * @param array $localize
     * @param string[] $deps
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function enqueue_script(
        string $slug,
        string $js_file,
        array  $localize = array(),
        array  $deps = array('jquery')
    ): bool
    {
        if (file_exists(WPPF_PATH . $js_file)) {
            wp_register_script(
                $slug,
                WPPF_URL . $js_file,
                $deps,
                WPPF_VERSION
            );

            wp_enqueue_script($slug);

            if (!empty($localize)) {
                wp_localize_script($slug, 'framework', $localize);
            }

            return true;
        }

        return false;
    }
}
