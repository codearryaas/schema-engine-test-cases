<?php
/**
 * Test Data Helper Class
 * 
 * Provides static methods to access dummy testing data.
 */

if (!defined('ABSPATH')) {
    exit;
}

class Schema_Engine_Test_Data
{

    /**
     * Get specific schema type data
     * 
     * @param string $type The schema type (e.g., 'Article', 'Recipe')
     * @return array The mock field data
     */
    public static function get_schema_data($type)
    {
        $schemas = include SCHEMA_ENGINE_TEST_CASES_DIR . 'tests/data/schemas.php';
        return isset($schemas[$type]) ? $schemas[$type] : array();
    }

    /**
     * Get condition rule data
     * 
     * @param string $rule_type (e.g., 'location_rules', 'user_rules')
     * @return array
     */
    public static function get_condition_data($rule_type)
    {
        $conditions = include SCHEMA_ENGINE_TEST_CASES_DIR . 'tests/data/conditions.php';
        return isset($conditions[$rule_type]) ? $conditions[$rule_type] : array();
    }

    /**
     * Get mock context data
     * 
     * @param string $context_name
     * @return array
     */
    public static function get_context($context_name)
    {
        $conditions = include SCHEMA_ENGINE_TEST_CASES_DIR . 'tests/data/conditions.php';
        return isset($conditions['mock_contexts'][$context_name]) ? $conditions['mock_contexts'][$context_name] : array();
    }

    /**
     * Get setting data
     * 
     * @param string $section (e.g., 'general', 'social')
     * @return array
     */
    public static function get_settings($section)
    {
        $settings = include SCHEMA_ENGINE_TEST_CASES_DIR . 'tests/data/settings.php';
        return isset($settings[$section]) ? $settings[$section] : array();
    }
}
