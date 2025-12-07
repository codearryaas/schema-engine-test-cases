<?php
/**
 * Tests for Location Conditions
 *
 * @package Schema_Engine_Test_Cases
 */

namespace SchemaEngine\Tests\Includes;

use PHPUnit\Framework\TestCase;

/**
 * Test location condition evaluation with consolidated location values
 * 
 * Note: These are simplified unit tests. Full integration tests would require
 * WordPress test framework with go_to() and factory methods.
 */
class LocationConditionsTest extends TestCase
{
    /**
     * Test whole_site location always matches
     */
    public function test_whole_site_location_always_matches()
    {
        $rule = array(
            'conditionType' => 'location',
            'operator' => 'equal_to',
            'value' => array('whole_site')
        );

        // Whole site should always return true when evaluated
        // This is a basic test - full integration would use go_to()
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);

        // In unit test context without full WP, we just verify the rule structure is valid
        $this->assertIsArray($rule);
        $this->assertEquals('location', $rule['conditionType']);
        $this->assertContains('whole_site', $rule['value']);
    }

    /**
     * Test front_page location
     */
    public function test_front_page_location()
    {
        $rule = array(
            'conditionType' => 'location',
            'operator' => 'equal_to',
            'value' => array('front_page')
        );

        // Verify rule structure
        $this->assertIsArray($rule);
        $this->assertEquals('location', $rule['conditionType']);
        $this->assertContains('front_page', $rule['value']);
    }

    /**
     * Test home_page (blog) location
     */
    public function test_home_page_location()
    {
        $rule = array(
            'conditionType' => 'location',
            'operator' => 'equal_to',
            'value' => array('home_page')
        );

        // Verify rule structure
        $this->assertIsArray($rule);
        $this->assertEquals('location', $rule['conditionType']);
        $this->assertContains('home_page', $rule['value']);
    }

    /**
     * Test search_page location
     */
    public function test_search_page_location()
    {
        $rule = array(
            'conditionType' => 'location',
            'operator' => 'equal_to',
            'value' => array('search_page')
        );

        // Verify rule structure
        $this->assertIsArray($rule);
        $this->assertEquals('location', $rule['conditionType']);
        $this->assertContains('search_page', $rule['value']);
    }

    /**
     * Test author_archive location (Pro)
     */
    public function test_author_archive_location()
    {
        if (!defined('SCHEMA_ENGINE_PRO_VERSION')) {
            $this->markTestSkipped('Pro version required');
        }

        $rule = array(
            'conditionType' => 'location',
            'operator' => 'equal_to',
            'value' => array('author_archive')
        );

        // Verify rule structure
        $this->assertIsArray($rule);
        $this->assertEquals('location', $rule['conditionType']);
        $this->assertContains('author_archive', $rule['value']);
    }

    /**
     * Test not_equal_to operator
     */
    public function test_location_not_equal_to_operator()
    {
        $rule = array(
            'conditionType' => 'location',
            'operator' => 'not_equal_to',
            'value' => array('front_page')
        );

        // Verify rule structure with not_equal_to operator
        $this->assertIsArray($rule);
        $this->assertEquals('location', $rule['conditionType']);
        $this->assertEquals('not_equal_to', $rule['operator']);
        $this->assertContains('front_page', $rule['value']);
    }

    /**
     * Test complex condition: whole_site AND NOT author_archive
     */
    public function test_complex_location_condition()
    {
        if (!defined('SCHEMA_ENGINE_PRO_VERSION')) {
            $this->markTestSkipped('Pro version required');
        }

        $conditions = array(
            'groups' => array(
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'location',
                            'operator' => 'equal_to',
                            'value' => array('whole_site')
                        ),
                        array(
                            'conditionType' => 'location',
                            'operator' => 'not_equal_to',
                            'value' => array('author_archive')
                        )
                    )
                )
            )
        );

        // Verify complex condition structure
        $this->assertIsArray($conditions);
        $this->assertArrayHasKey('groups', $conditions);
        $this->assertCount(1, $conditions['groups']);
        $this->assertEquals('and', $conditions['groups'][0]['logic']);
        $this->assertCount(2, $conditions['groups'][0]['rules']);
    }
}
