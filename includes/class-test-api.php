<?php
/**
 * Test Dashboard - REST API Endpoint
 * Provides test execution and results via REST API
 *
 * @package Schema_Engine
 */

if (!defined('ABSPATH')) {
    exit;
}

class Schema_Engine_Test_API
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
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route('schema-engine/v1', '/tests/run', array(
            'methods' => 'POST',
            'callback' => array($this, 'run_tests'),
            'permission_callback' => array($this, 'check_permissions'),
        ));

        register_rest_route('schema-engine/v1', '/tests/list', array(
            'methods' => 'GET',
            'callback' => array($this, 'list_tests'),
            'permission_callback' => array($this, 'check_permissions'),
        ));

        register_rest_route('schema-engine/v1', '/tests/stats', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_stats'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
    }

    public function check_permissions()
    {
        return current_user_can('manage_options');
    }

    public function list_tests()
    {
        $tests = array();
        
        // Get test files from the test-cases plugin directory
        $test_cases_dir = SCHEMA_ENGINE_TEST_CASES_DIR;
        
        // Get schema type tests
        $schema_files = glob( $test_cases_dir . 'tests/output/types/*Test.php' );
        foreach ( $schema_files as $file ) {
            $filename = basename( $file );
            $test_name = str_replace( array( 'Test.php', 'SchemaTest.php' ), '', $filename );
            
            $tests[] = array(
                'file'     => $filename,
                'name'     => $test_name,
                'path'     => $file,
                'type'     => 'schema',
                'category' => 'Schema Types',
                'plugin'   => 'free',
            );
        }
        
        // Get conditions tests
        $condition_files = glob( $test_cases_dir . 'tests/includes/*Test.php' );
        foreach ( $condition_files as $file ) {
            $filename = basename( $file );
            $test_name = str_replace( 'Test.php', '', $filename );
            
            $tests[] = array(
                'file'     => $filename,
                'name'     => $test_name,
                'path'     => $file,
                'type'     => 'conditions',
                'category' => 'Template Conditions',
                'plugin'   => 'free',
            );
        }
        
        // Get settings/admin tests
        $admin_files = glob( $test_cases_dir . 'tests/admin/*Test.php' );
        foreach ( $admin_files as $file ) {
            $filename = basename( $file );
            $test_name = str_replace( 'Test.php', '', $filename );
            
            $tests[] = array(
                'file'     => $filename,
                'name'     => $test_name,
                'path'     => $file,
                'type'     => 'settings',
                'category' => 'Settings & Admin',
                'plugin'   => 'free',
            );
        }

        return rest_ensure_response( array(
            'success' => true,
            'tests'   => $tests,
            'total'   => count( $tests ),
        ) );
    }

    public function run_tests($request)
    {
        $test_filter = $request->get_param('filter') ?: '';
        
        // Use test-cases plugin directory for test execution
        $plugin_dir = SCHEMA_ENGINE_TEST_CASES_DIR;
        $config_file = $plugin_dir . 'phpunit.xml';
        
        // Verify phpunit.xml exists
        if ( ! file_exists( $config_file ) ) {
            return rest_ensure_response( array(
                'success' => false,
                'message' => 'PHPUnit configuration file not found: ' . $config_file,
            ) );
        }
        
        // Find PHP CLI binary (not php-fpm)
        $php_binary = $this->get_php_cli_path();
        
        $command = sprintf(
            'cd %s && %s vendor/bin/phpunit --configuration %s',
            escapeshellarg($plugin_dir),
            escapeshellarg($php_binary),
            escapeshellarg($config_file)
        );

        if (!empty($test_filter)) {
            $command .= ' --filter ' . escapeshellarg($test_filter);
        }

        $command .= ' --testdox --colors=never 2>&1';

        // Execute tests
        $output = array();
        $return_var = 0;
        exec($command, $output, $return_var);

        // Parse output
        $result = $this->parse_test_output($output);
        $result['success'] = ($return_var === 0);
        $result['exit_code'] = $return_var;
        $result['command'] = $command; // Debug: show command executed

        return rest_ensure_response($result);
    }

    /**
     * Get PHP CLI executable path
     * Handles cases where PHP_BINARY points to php-fpm
     */
    private function get_php_cli_path()
    {
        // Check if PHP_BINARY is php-fpm
        if (defined('PHP_BINARY') && strpos(PHP_BINARY, 'php-fpm') === false) {
            return PHP_BINARY;
        }

        // Try to find php CLI in common locations
        $possible_paths = array(
            '/usr/local/bin/php',
            '/usr/bin/php',
            '/opt/homebrew/bin/php',
            dirname(PHP_BINARY) . '/php', // Same directory as php-fpm
        );

        foreach ($possible_paths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        // Try using 'which php' command
        $which_output = array();
        exec('which php 2>&1', $which_output, $which_return);
        if ($which_return === 0 && !empty($which_output[0])) {
            return trim($which_output[0]);
        }

        // Fallback to 'php' and hope it's in PATH
        return 'php';
    }

    public function get_stats()
    {
        // Get test history from options
        $history = get_option('schema_engine_test_history', array());
        
        // Calculate stats
        $stats = array(
            'total_runs' => count($history),
            'last_run' => !empty($history) ? end($history) : null,
            'success_rate' => 0,
            'average_time' => 0,
        );

        if (!empty($history)) {
            $successful = array_filter($history, function($run) {
                return $run['success'];
            });
            
            $stats['success_rate'] = (count($successful) / count($history)) * 100;
            
            $total_time = array_reduce($history, function($carry, $run) {
                return $carry + ($run['time'] ?? 0);
            }, 0);
            
            $stats['average_time'] = $total_time / count($history);
        }

        return rest_ensure_response($stats);
    }

    private function parse_test_output($output)
    {
        $output_text = implode("\n", $output);
        
        // Extract test results
        $tests_run = 0;
        $assertions = 0;
        $failures = 0;
        $errors = 0;
        $time = 0;

        // Parse summary line
        if (preg_match('/OK \((\d+) tests?, (\d+) assertions?\)/', $output_text, $matches)) {
            $tests_run = (int)$matches[1];
            $assertions = (int)$matches[2];
        } elseif (preg_match('/Tests: (\d+), Assertions: (\d+), Failures: (\d+)/', $output_text, $matches)) {
            $tests_run = (int)$matches[1];
            $assertions = (int)$matches[2];
            $failures = (int)$matches[3];
        } elseif (preg_match('/Tests: (\d+), Assertions: (\d+), Errors: (\d+)/', $output_text, $matches)) {
            $tests_run = (int)$matches[1];
            $assertions = (int)$matches[2];
            $errors = (int)$matches[3];
        }

        // Extract time
        if (preg_match('/Time: ([\d.]+)/', $output_text, $matches)) {
            $time = (float)$matches[1];
        }

        // Extract individual test results
        $test_results = array();
        foreach ($output as $line) {
            if (preg_match('/✔\s+(.+)/', $line, $matches) || preg_match('/✓\s+(.+)/', $line, $matches)) {
                $test_results[] = array(
                    'name' => trim($matches[1]),
                    'status' => 'passed',
                );
            } elseif (preg_match('/✘\s+(.+)/', $line, $matches) || preg_match('/✗\s+(.+)/', $line, $matches)) {
                $test_results[] = array(
                    'name' => trim($matches[1]),
                    'status' => 'failed',
                );
            }
        }

        // Save to history
        $history = get_option('schema_engine_test_history', array());
        $history[] = array(
            'timestamp' => time(),
            'tests' => $tests_run,
            'assertions' => $assertions,
            'failures' => $failures,
            'errors' => $errors,
            'time' => $time,
            'success' => ($failures === 0 && $errors === 0),
        );

        // Keep last 50 runs
        if (count($history) > 50) {
            $history = array_slice($history, -50);
        }

        update_option('schema_engine_test_history', $history);

        return array(
            'tests' => $tests_run,
            'assertions' => $assertions,
            'failures' => $failures,
            'errors' => $errors,
            'time' => $time,
            'output' => $output_text,
            'test_results' => $test_results,
        );
    }
}

// Initialize
Schema_Engine_Test_API::get_instance();
