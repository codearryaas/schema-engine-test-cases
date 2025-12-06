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

        // Should match on any page
        $this->go_to('/');
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Whole site should match on homepage');

        $post_id = $this->factory->post->create();
        $this->go_to(get_permalink($post_id));
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Whole site should match on single post');
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

        // Set front page
        update_option('show_on_front', 'page');
        $front_page_id = $this->factory->post->create(array('post_type' => 'page'));
        update_option('page_on_front', $front_page_id);

        $this->go_to(get_permalink($front_page_id));
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Should match on front page');

        // Should not match on other pages
        $other_page_id = $this->factory->post->create(array('post_type' => 'page'));
        $this->go_to(get_permalink($other_page_id));
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'Should not match on other pages');
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

        // Default posts page
        $this->go_to('/');
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Should match on blog home');

        // Should not match on single post
        $post_id = $this->factory->post->create();
        $this->go_to(get_permalink($post_id));
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'Should not match on single post');
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

        $this->go_to('/?s=test');
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Should match on search page');

        $this->go_to('/');
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'Should not match on homepage');
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

        $user_id = $this->factory->user->create();
        $this->go_to(get_author_posts_url($user_id));
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Should match on author archive');

        $this->go_to('/');
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'Should not match on homepage');
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

        // Set front page
        update_option('show_on_front', 'page');
        $front_page_id = $this->factory->post->create(array('post_type' => 'page'));
        update_option('page_on_front', $front_page_id);

        // Should NOT match on front page
        $this->go_to(get_permalink($front_page_id));
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertFalse($result, 'Should not match on front page with not_equal_to');

        // Should match on other pages
        $other_page_id = $this->factory->post->create(array('post_type' => 'page'));
        $this->go_to(get_permalink($other_page_id));
        $result = apply_filters('schema_engine_evaluate_rule', false, $rule);
        $this->assertTrue($result, 'Should match on other pages with not_equal_to');
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

        // Should match on regular pages
        $post_id = $this->factory->post->create();
        $this->go_to(get_permalink($post_id));
        $result = \Schema_Engine_Conditions::matches_conditions($conditions);
        $this->assertTrue($result, 'Should match on regular post');

        // Should NOT match on author archive
        $user_id = $this->factory->user->create();
        $this->go_to(get_author_posts_url($user_id));
        $result = \Schema_Engine_Conditions::matches_conditions($conditions);
        $this->assertFalse($result, 'Should not match on author archive');
    }
}
