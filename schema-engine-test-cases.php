<?php
/**
 * Plugin Name: Schema Engine Test Cases
 * Plugin URI: https://toolpress.net/schema-engine
 * Description: Comprehensive test suite for Schema Engine plugin (Free & Pro) with visual test dashboard and automated PHPUnit testing.
 * Version: 1.0.0
 * Author: ToolPress
 * Author URI: https://toolpress.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: schema-engine-test-cases
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Tested up to: 6.4
 *
 * @package Schema_Engine_Test_Cases
 */

if (!defined('ABSPATH')) {
	exit;
}

// Plugin constants
define('SCHEMA_ENGINE_TEST_CASES_VERSION', '1.0.0');
define('SCHEMA_ENGINE_TEST_CASES_FILE', __FILE__);
define('SCHEMA_ENGINE_TEST_CASES_DIR', plugin_dir_path(__FILE__));
define('SCHEMA_ENGINE_TEST_CASES_URL', plugin_dir_url(__FILE__));
define('SCHEMA_ENGINE_TEST_CASES_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Schema_Engine_Test_Cases
{

	/**
	 * Singleton instance
	 *
	 * @var Schema_Engine_Test_Cases
	 */
	private static $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return Schema_Engine_Test_Cases
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct()
	{
		$this->check_dependencies();
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Check if required plugins are active
	 */
	private function check_dependencies()
	{
		add_action('admin_notices', array($this, 'dependency_notices'));
	}

	/**
	 * Show admin notice if Schema Engine is not active
	 */
	public function dependency_notices()
	{
		if (!class_exists('Schema_Engine')) {
			?>
			<div class="notice notice-error">
				<p>
					<strong><?php esc_html_e('Schema Engine Test Cases:', 'schema-engine-test-cases'); ?></strong>
					<?php esc_html_e('This plugin requires Schema Engine plugin to be installed and activated.', 'schema-engine-test-cases'); ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Load plugin dependencies
	 */
	private function load_dependencies()
	{
		// Load test API
		require_once SCHEMA_ENGINE_TEST_CASES_DIR . 'includes/class-test-api.php';

		// Load test dashboard
		require_once SCHEMA_ENGINE_TEST_CASES_DIR . 'includes/class-test-dashboard.php';

		// Load diagnostic tool (moved from main plugin)
		require_once SCHEMA_ENGINE_TEST_CASES_DIR . 'includes/class-schema-engine-diagnostic.php';
	}

	/**
	 * Initialize WordPress hooks
	 */
	private function init_hooks()
	{
		// Activation hook
		register_activation_hook(__FILE__, array($this, 'activate'));

		// Deactivation hook
		register_deactivation_hook(__FILE__, array($this, 'deactivate'));

		// Load text domain
		add_action('plugins_loaded', array($this, 'load_textdomain'));
	}

	/**
	 * Plugin activation
	 */
	public function activate()
	{
		// Check if Schema Engine is active
		if (!class_exists('Schema_Engine')) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(
				esc_html__('Schema Engine Test Cases requires Schema Engine plugin to be installed and activated.', 'schema-engine-test-cases'),
				esc_html__('Plugin Activation Error', 'schema-engine-test-cases'),
				array('back_link' => true)
			);
		}

		// Set default options
		add_option('schema_engine_test_cases_version', SCHEMA_ENGINE_TEST_CASES_VERSION);
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate()
	{
		// Cleanup if needed
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain()
	{
		load_plugin_textdomain(
			'schema-engine-test-cases',
			false,
			dirname(plugin_basename(__FILE__)) . '/languages'
		);
	}
}

// Initialize plugin
Schema_Engine_Test_Cases::get_instance();
