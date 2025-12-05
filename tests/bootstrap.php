<?php
/**
 * PHPUnit Bootstrap File
 *
 * Sets up the test environment for Schema Engine
 */

// Define test constants
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

if (!defined('WP_CONTENT_DIR')) {
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}

if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
}

// Define Schema Engine plugin path (load from actual plugin directory)
$schema_engine_dir = dirname(dirname(__DIR__)) . '/schema-engine';
if (!is_dir($schema_engine_dir)) {
    die('Error: Schema Engine plugin not found. Please ensure it is installed in the plugins directory.');
}

if (!defined('SCHEMA_ENGINE_VERSION')) {
    define('SCHEMA_ENGINE_VERSION', '1.0.0');
}

if (!defined('SCHEMA_ENGINE_PLUGIN_DIR')) {
    define('SCHEMA_ENGINE_PLUGIN_DIR', $schema_engine_dir . '/');
}

if (!defined('SCHEMA_ENGINE_PLUGIN_URL')) {
    define('SCHEMA_ENGINE_PLUGIN_URL', 'http://example.com/wp-content/plugins/schema-engine/');
}

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Initialize Brain Monkey for WordPress function mocking
Brain\Monkey\setUp();

// Mock WordPress translation functions
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('_e')) {
    function _e($text, $domain = 'default') {
        echo $text;
    }
}

if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr__')) {
    function esc_attr__($text, $domain = 'default') {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data, $options = 0, $depth = 512) {
        return json_encode($data, $options, $depth);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return strip_tags($str);
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data) {
        return $data;
    }
}

if (!function_exists('esc_url_raw')) {
    function esc_url_raw($url) {
        // Remove invalid protocols like javascript:, data:, vbscript:
        $url = preg_replace('/^(javascript|data|vbscript):/i', '', $url);
        
        // Use filter_var for basic sanitization
        $sanitized = filter_var($url, FILTER_SANITIZE_URL);
        
        // Validate URL format
        if (filter_var($sanitized, FILTER_VALIDATE_URL) === false && !empty($sanitized)) {
            // If not a valid URL but has content, it might be a path
            return $sanitized;
        }
        
        return $sanitized ?: '';
    }
}

// Load plugin classes needed for tests
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/class-schema-engine-conditions.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/admin/rest-api/class-rest-api.php';

// Load schema builder interface and base classes
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/interface-schema-builder.php';

// Load schema type classes
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-article-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-faq-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-job-posting-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-localbusiness-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-organization-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-person-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-product-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-review-schema.php';
require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/output/types/class-video-schema.php';
