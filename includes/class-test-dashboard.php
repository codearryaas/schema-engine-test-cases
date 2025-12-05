<?php
/**
 * Test Dashboard Admin Page
 *
 * @package Schema_Engine
 */

if (!defined('ABSPATH')) {
    exit;
}

class Schema_Engine_Test_Dashboard
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_page'), 100);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function add_menu_page()
    {
        // Always show test dashboard for test cases plugin
        add_menu_page(
            __('Schema Tests', 'schema-engine-test-cases'),
            __('Schema Tests', 'schema-engine-test-cases'),
            'manage_options',
            'schema-engine-test-dashboard',
            array($this, 'render_page'),
            'dashicons-analytics',
            100
        );
    }

    public function enqueue_assets($hook)
    {
        if ('toplevel_page_schema-engine-test-dashboard' !== $hook) {
            return;
        }

        $asset_file = SCHEMA_ENGINE_TEST_CASES_DIR . 'build/test-dashboard/index.asset.php';
        
        if (!file_exists($asset_file)) {
            return;
        }

        $asset = include $asset_file;

        wp_enqueue_style(
            'schema-engine-test-dashboard',
            SCHEMA_ENGINE_TEST_CASES_URL . 'build/test-dashboard/style-index.css',
            array('wp-components'),
            $asset['version']
        );

        wp_enqueue_script(
            'schema-engine-test-dashboard',
            SCHEMA_ENGINE_TEST_CASES_URL . 'build/test-dashboard/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );

        wp_set_script_translations(
            'schema-engine-test-dashboard',
            'schema-engine-test-cases'
        );
    }

    public function render_page()
    {
        ?>
        <div class="wrap">
            <div id="schema-engine-test-dashboard"></div>
        </div>
        <?php
    }
}

// Initialize if tests API is loaded
if (class_exists('Schema_Engine_Test_API')) {
    Schema_Engine_Test_Dashboard::get_instance();
}
