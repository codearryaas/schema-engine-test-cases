<?php
/**
 * Tests for User Profile Matching Logic
 *
 * @package Schema_Engine_Test_Cases
 */

namespace SchemaEngine\Tests\Includes;

use PHPUnit\Framework\TestCase;

/**
 * Test user profile condition matching with operator support
 */
class UserProfileMatchingTest extends TestCase
{
    /**
     * Pro user profile instance
     */
    protected $user_profile;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!defined('SCHEMA_ENGINE_PRO_VERSION')) {
            $this->markTestSkipped('Pro version required');
        }

        // Load Pro user profile class
        $pro_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/class-schema-engine-pro-user-profile.php';
        if (file_exists($pro_path)) {
            require_once $pro_path;
        }

        if (!class_exists('\\Schema_Engine_Pro_User_Profile')) {
            $this->markTestSkipped('Schema_Engine_Pro_User_Profile class not found');
        }
    }

    /**
     * Test user_role condition matches in user profile context
     */
    public function test_user_role_matches_in_profile()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'administrator'));

        $conditions = array(
            'groups' => array(
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('administrator')
                        )
                    )
                )
            )
        );

        // Use reflection to access private method
        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        $this->assertTrue($result, 'Should match user with administrator role');
    }

    /**
     * Test location condition with author_archive in user profile
     */
    public function test_location_author_archive_matches_in_profile()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'editor'));

        $conditions = array(
            'groups' => array(
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'location',
                            'operator' => 'equal_to',
                            'value' => array('author_archive')
                        )
                    )
                )
            )
        );

        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        $this->assertTrue($result, 'author_archive should always match in user profile context');
    }

    /**
     * Test location condition with whole_site in user profile
     */
    public function test_location_whole_site_matches_in_profile()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'editor'));

        $conditions = array(
            'groups' => array(
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'location',
                            'operator' => 'equal_to',
                            'value' => array('whole_site')
                        )
                    )
                )
            )
        );

        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        $this->assertTrue($result, 'whole_site should always match in user profile context');
    }

    /**
     * Test not_equal_to operator inverts result
     */
    public function test_not_equal_to_operator_inverts_result()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'administrator'));

        $conditions = array(
            'groups' => array(
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'not_equal_to',
                            'value' => array('administrator')
                        )
                    )
                )
            )
        );

        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        $this->assertFalse($result, 'not_equal_to should invert the match result');
    }

    /**
     * Test complex AND logic: whole_site AND NOT author_archive
     */
    public function test_complex_and_logic_with_not_operator()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'editor'));

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

        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        // whole_site = true, author_archive = true -> inverted to false
        // true AND false = false
        $this->assertFalse($result, 'Should not match: whole_site AND NOT author_archive');
    }

    /**
     * Test OR logic between groups
     */
    public function test_or_logic_between_groups()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'subscriber'));

        $conditions = array(
            'groups' => array(
                // Group 1: administrator role (won't match)
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('administrator')
                        )
                    )
                ),
                // Group 2: subscriber role (will match)
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('subscriber')
                        )
                    )
                )
            )
        );

        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        $this->assertTrue($result, 'Should match when any group matches (OR logic)');
    }

    /**
     * Test AND logic within group
     */
    public function test_and_logic_within_group()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'editor'));

        $conditions = array(
            'groups' => array(
                array(
                    'logic' => 'and',
                    'rules' => array(
                        array(
                            'conditionType' => 'location',
                            'operator' => 'equal_to',
                            'value' => array('author_archive')
                        ),
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('editor', 'administrator')
                        )
                    )
                )
            )
        );

        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        $this->assertTrue($result, 'Should match when all rules in group match (AND logic)');
    }

    /**
     * Test empty conditions returns false
     */
    public function test_empty_conditions_returns_false()
    {
        $user = $this->factory->user->create_and_get(array('role' => 'editor'));

        $conditions = array(
            'groups' => array()
        );

        $reflection = new \ReflectionClass('\\Schema_Engine_Pro_User_Profile');
        $method = $reflection->getMethod('user_matches_conditions');
        $method->setAccessible(true);

        $instance = $reflection->getMethod('get_instance')->invoke(null);
        $result = $method->invoke($instance, $user, $conditions);

        $this->assertFalse($result, 'Empty conditions should return false');
    }
}
