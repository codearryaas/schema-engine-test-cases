<?php
/**
 * User Role Condition Test
 *
 * @package Schema_Engine
 */

use Brain\Monkey;
use Brain\Monkey\Functions;

use Brain\Monkey\Filters;

class UserRoleConditionTest extends PHPUnit\Framework\TestCase
{


    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        require_once SCHEMA_ENGINE_PLUGIN_DIR . 'includes/class-schema-engine-conditions.php';

        $pro_conditions_path = dirname(dirname(dirname(__DIR__))) . '/schema-engine-pro/includes/class-schema-engine-pro-conditions.php';
        if (file_exists($pro_conditions_path)) {
            require_once $pro_conditions_path;

            // Only set up filter if Pro class exists
            if (class_exists('Schema_Engine_Pro_Conditions')) {
                Filters\expectApplied('schema_engine_evaluate_rule')
                    ->zeroOrMoreTimes()
                    ->andReturnUsing(array(Schema_Engine_Pro_Conditions::get_instance(), 'evaluate_pro_rules'));
            }
        }
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        Schema_Engine_Test_Mocks::reset();
        parent::tearDown();
    }

    public function test_user_role_condition_logged_out()
    {
        $conditions = array(
            'groups' => array(
                array(
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('administrator'),
                        ),
                    ),
                ),
            ),
        );

        Schema_Engine_Test_Mocks::$is_user_logged_in = false;
        Schema_Engine_Test_Mocks::$is_author = false;

        $this->assertFalse(Schema_Engine_Conditions::matches_conditions($conditions));
    }

    public function test_user_role_condition_match()
    {
        if (!class_exists('Schema_Engine_Pro_Conditions')) {
            $this->markTestSkipped('Pro plugin required for user_role conditions');
        }

        $conditions = array(
            'groups' => array(
                array(
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('administrator'),
                        ),
                    ),
                ),
            ),
        );

        $user = new stdClass();
        $user->roles = array('administrator');

        // User role conditions require is_author() to be true
        Schema_Engine_Test_Mocks::$is_author = true;
        Schema_Engine_Test_Mocks::$queried_object = $user;
        Schema_Engine_Test_Mocks::$is_user_logged_in = true;
        Schema_Engine_Test_Mocks::$current_user = $user;

        $this->assertTrue(Schema_Engine_Conditions::matches_conditions($conditions));
    }

    public function test_user_role_condition_mismatch()
    {
        $conditions = array(
            'groups' => array(
                array(
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('administrator'),
                        ),
                    ),
                ),
            ),
        );

        $user = new stdClass();
        $user->roles = array('subscriber');

        Schema_Engine_Test_Mocks::$is_user_logged_in = true;
        Schema_Engine_Test_Mocks::$current_user = $user;
        Schema_Engine_Test_Mocks::$is_author = false;

        $this->assertFalse(Schema_Engine_Conditions::matches_conditions($conditions));
    }

    public function test_user_role_condition_not_equal()
    {
        $conditions = array(
            'groups' => array(
                array(
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'not_equal_to',
                            'value' => array('administrator'),
                        ),
                    ),
                ),
            ),
        );

        $user = new stdClass();
        $user->roles = array('subscriber');

        Schema_Engine_Test_Mocks::$is_user_logged_in = true;
        Schema_Engine_Test_Mocks::$current_user = $user;
        Schema_Engine_Test_Mocks::$is_author = false;

        // Logic: evaluate_user_role_rule returns false (mismatch).
        // operator 'not_equal_to' -> !false = true.
        $this->assertTrue(Schema_Engine_Conditions::matches_conditions($conditions));
    }

    public function test_user_role_condition_author_archive_match()
    {
        $conditions = array(
            'groups' => array(
                array(
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('editor'),
                        ),
                    ),
                ),
            ),
        );

        $author = new stdClass();
        $author->roles = array('editor');

        Schema_Engine_Test_Mocks::$is_author = true;
        Schema_Engine_Test_Mocks::$queried_object = $author;

        $this->assertTrue(Schema_Engine_Conditions::matches_conditions($conditions));
    }

    public function test_user_role_condition_author_archive_mismatch()
    {
        $conditions = array(
            'groups' => array(
                array(
                    'rules' => array(
                        array(
                            'conditionType' => 'user_role',
                            'operator' => 'equal_to',
                            'value' => array('administrator'),
                        ),
                    ),
                ),
            ),
        );

        $author = new stdClass();
        $author->roles = array('subscriber');

        Schema_Engine_Test_Mocks::$is_author = true;
        Schema_Engine_Test_Mocks::$queried_object = $author;

        $this->assertFalse(Schema_Engine_Conditions::matches_conditions($conditions));
    }
}
