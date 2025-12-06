<?php
/**
 * Tests for User Role Restriction to Author Archives
 *
 * @package Schema_Engine_Test_Cases
 */

namespace SchemaEngine\Tests\Includes;

use PHPUnit\Framework\TestCase;

/**
 * Test that user_role condition only works on author archives
 */
class UserRoleRestrictionTest extends TestCase
{
    /**
     * Setup Pro conditions class
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!defined('SCHEMA_ENGINE_PRO_VERSION')) {
            $this->markTestSkipped('Pro version required for user role tests');
        }

        // Ensure Pro conditions class is loaded
        $pro_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/class-schema-engine-pro-conditions.php';
        if (file_exists($pro_path)) {
            require_once $pro_path;
        }
    }

    /**
     * Test user_role works on author archive
     */
    public function test_user_role_matches_on_author_archive()
    {
        // Create user with administrator role
        $user_id = $this->factory->user->create(array('role' => 'administrator'));

        // Go to author archive
        $this->go_to(get_author_posts_url($user_id));

        $rule = array(
            'conditionType' => 'user_role',
            'operator' => 'equal_to',
            'value' => array('administrator')
        );

        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'User role should match on author archive');
    }

    /**
     * Test user_role does NOT work on regular posts
     */
    public function test_user_role_does_not_match_on_regular_post()
    {
        // Create user with administrator role
        $user_id = $this->factory->user->create(array('role' => 'administrator'));

        // Create post by this user
        $post_id = $this->factory->post->create(array('post_author' => $user_id));

        // Go to single post (NOT author archive)
        $this->go_to(get_permalink($post_id));

        $rule = array(
            'conditionType' => 'user_role',
            'operator' => 'equal_to',
            'value' => array('administrator')
        );

        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'User role should NOT match on regular post');
    }

    /**
     * Test user_role does NOT work on homepage
     */
    public function test_user_role_does_not_match_on_homepage()
    {
        $this->go_to('/');

        $rule = array(
            'conditionType' => 'user_role',
            'operator' => 'equal_to',
            'value' => array('administrator')
        );

        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'User role should NOT match on homepage');
    }

    /**
     * Test user_role checks author's role, not current user's role
     */
    public function test_user_role_checks_author_not_current_user()
    {
        // Create author with editor role
        $author_id = $this->factory->user->create(array('role' => 'editor'));

        // Create and login as administrator
        $admin_id = $this->factory->user->create(array('role' => 'administrator'));
        wp_set_current_user($admin_id);

        // Go to editor's author archive
        $this->go_to(get_author_posts_url($author_id));

        // Check for administrator role (current user)
        $rule_admin = array(
            'conditionType' => 'user_role',
            'operator' => 'equal_to',
            'value' => array('administrator')
        );

        $result = apply_filters('schema_engine_evaluate_rule', false, $rule_admin);
        $this->assertFalse($result, 'Should NOT match current user role (administrator)');

        // Check for editor role (author being viewed)
        $rule_editor = array(
            'conditionType' => 'user_role',
            'operator' => 'equal_to',
            'value' => array('editor')
        );

        $result = apply_filters('schema_engine_evaluate_rule', false, $rule_editor);
        $this->assertTrue($result, 'Should match author role (editor)');
    }

    /**
     * Test user_role with multiple roles
     */
    public function test_user_role_with_multiple_roles()
    {
        // Create user with contributor role
        $user_id = $this->factory->user->create(array('role' => 'contributor'));

        $this->go_to(get_author_posts_url($user_id));

        $rule = array(
            'conditionType' => 'user_role',
            'operator' => 'equal_to',
            'value' => array('administrator', 'editor', 'contributor')
        );

        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Should match when author has one of multiple roles');
    }

    /**
     * Test user_role does not match wrong role
     */
    public function test_user_role_does_not_match_wrong_role()
    {
        // Create user with subscriber role
        $user_id = $this->factory->user->create(array('role' => 'subscriber'));

        $this->go_to(get_author_posts_url($user_id));

        $rule = array(
            'conditionType' => 'user_role',
            'operator' => 'equal_to',
            'value' => array('administrator', 'editor')
        );

        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'Should not match when author has different role');
    }

    /**
     * Test recommended pattern: author_archive + user_role
     */
    public function test_recommended_pattern_author_archive_and_user_role()
    {
        // Create administrator user
        $admin_id = $this->factory->user->create(array('role' => 'administrator'));

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
                            'value' => array('administrator', 'editor')
                        )
                    )
                )
            )
        );

        // Should match on admin's author archive
        $this->go_to(get_author_posts_url($admin_id));
        $result = \Schema_Engine_Conditions::matches_conditions($conditions);
        $this->assertTrue($result, 'Should match admin author archive');

        // Should NOT match on subscriber's author archive
        $subscriber_id = $this->factory->user->create(array('role' => 'subscriber'));
        $this->go_to(get_author_posts_url($subscriber_id));
        $result = \Schema_Engine_Conditions::matches_conditions($conditions);
        $this->assertFalse($result, 'Should not match subscriber author archive');

        // Should NOT match on regular post
        $post_id = $this->factory->post->create(array('post_author' => $admin_id));
        $this->go_to(get_permalink($post_id));
        $result = \Schema_Engine_Conditions::matches_conditions($conditions);
        $this->assertFalse($result, 'Should not match on regular post');
    }
}
