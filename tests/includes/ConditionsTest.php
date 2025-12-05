<?php
/**
 * Tests for Schema_Engine_Conditions class
 *
 * @package Schema_Engine
 */

namespace Schema_Engine\Tests\Includes;

use PHPUnit\Framework\TestCase;
use Schema_Engine_Conditions;

class ConditionsTest extends TestCase {
	
	public function test_has_rules_returns_false_for_empty_conditions() {
		$this->assertFalse( Schema_Engine_Conditions::has_rules( array() ) );
		$this->assertFalse( Schema_Engine_Conditions::has_rules( null ) );
	}
	
	public function test_has_rules_returns_true_for_grouped_conditions() {
		$conditions = array(
			'groups' => array(
				array(
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'value' => array( 'post' ),
						),
					),
				),
			),
		);
		
		$this->assertTrue( Schema_Engine_Conditions::has_rules( $conditions ) );
	}
	
	public function test_has_rules_returns_false_for_empty_groups() {
		$conditions = array(
			'groups' => array(
				array(
					'rules' => array(),
				),
			),
		);
		
		$this->assertFalse( Schema_Engine_Conditions::has_rules( $conditions ) );
	}
	
	public function test_has_rules_returns_true_for_legacy_post_types() {
		$conditions = array(
			'postTypes' => array( 'post', 'page' ),
		);
		
		$this->assertTrue( Schema_Engine_Conditions::has_rules( $conditions ) );
	}
	
	public function test_has_rules_returns_true_for_legacy_specific_posts() {
		$conditions = array(
			'specificPosts' => array( 1, 2, 3 ),
		);
		
		$this->assertTrue( Schema_Engine_Conditions::has_rules( $conditions ) );
	}
	
	public function test_matches_conditions_returns_false_for_empty_conditions() {
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( array(), 1, 'post' ) );
	}
	
	public function test_matches_grouped_conditions_with_post_type_equal_to() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'equal_to',
							'value' => array( 'post', 'page' ),
						),
					),
				),
			),
		);
		
		// Should match post type 'post'
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'post' ) );
		
		// Should match post type 'page'
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'page' ) );
		
		// Should not match post type 'product'
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'product' ) );
	}
	
	public function test_matches_grouped_conditions_with_post_type_not_equal_to() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'not_equal_to',
							'value' => array( 'post' ),
						),
					),
				),
			),
		);
		
		// Should not match post type 'post'
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'post' ) );
		
		// Should match post type 'page'
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'page' ) );
	}
	
	public function test_matches_grouped_conditions_with_singular_post() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'singular',
							'operator' => 'equal_to',
							'value' => array( 10, 20, 30 ),
						),
					),
				),
			),
		);
		
		// Should match post ID 10
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'post' ) );
		
		// Should match post ID 20
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 20, 'post' ) );
		
		// Should not match post ID 50
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 50, 'post' ) );
	}
	
	public function test_matches_grouped_conditions_with_singular_post_string_ids() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'singular',
							'operator' => 'equal_to',
							'value' => array( '10', '20' ), // String IDs
						),
					),
				),
			),
		);
		
		// Should match post ID 10 (int) with value '10' (string)
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'post' ) );
	}
	
	public function test_matches_grouped_conditions_with_whole_site() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'whole_site',
							'operator' => 'equal_to',
							'value' => array(),
						),
					),
				),
			),
		);
		
		// Whole site should always match
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'post' ) );
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, null, null ) );
	}
	
	public function test_matches_grouped_conditions_with_multiple_rules_and_logic() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'equal_to',
							'value' => array( 'post' ),
						),
						array(
							'conditionType' => 'singular',
							'operator' => 'equal_to',
							'value' => array( 10 ),
						),
					),
				),
			),
		);
		
		// Should match when both rules are true (post type AND post ID)
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'post' ) );
		
		// Should not match when only one rule is true
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'page' ) );
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 20, 'post' ) );
	}
	
	public function test_matches_grouped_conditions_with_multiple_rules_or_logic() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'or',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'equal_to',
							'value' => array( 'post' ),
						),
						array(
							'conditionType' => 'singular',
							'operator' => 'equal_to',
							'value' => array( 10 ),
						),
					),
				),
			),
		);
		
		// Should match when either rule is true
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 5, 'post' ) );
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'page' ) );
		
		// Should not match when both rules are false
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 5, 'page' ) );
	}
	
	public function test_matches_grouped_conditions_with_multiple_groups_and_logic() {
		$conditions = array(
			'logic' => 'and',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'equal_to',
							'value' => array( 'post' ),
						),
					),
				),
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'singular',
							'operator' => 'equal_to',
							'value' => array( 10 ),
						),
					),
				),
			),
		);
		
		// Should match when all groups are true
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'post' ) );
		
		// Should not match when any group is false
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'page' ) );
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 20, 'post' ) );
	}
	
	public function test_matches_grouped_conditions_with_multiple_groups_or_logic() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'equal_to',
							'value' => array( 'post' ),
						),
					),
				),
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'singular',
							'operator' => 'equal_to',
							'value' => array( 10 ),
						),
					),
				),
			),
		);
		
		// Should match when any group is true
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 5, 'post' ) );
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'page' ) );
		
		// Should not match when all groups are false
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 5, 'page' ) );
	}
	
	public function test_matches_legacy_flat_conditions_with_post_types() {
		$conditions = array(
			'postTypes' => array( 'post', 'page' ),
		);
		
		// Should match post type 'post'
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'post' ) );
		
		// Should match post type 'page'
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'page' ) );
		
		// Should not match post type 'product'
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'product' ) );
	}
	
	public function test_matches_legacy_flat_conditions_with_specific_posts() {
		$conditions = array(
			'specificPosts' => array( 10, 20, 30 ),
		);
		
		// Should match post ID 10
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'post' ) );
		
		// Should match post ID 20
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 20, 'post' ) );
		
		// Should not match post ID 50
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 50, 'post' ) );
	}
	
	public function test_matches_legacy_flat_conditions_with_string_ids() {
		$conditions = array(
			'specificPosts' => array( '10', '20' ), // String IDs
		);
		
		// Should match post ID 10 (int) with value '10' (string)
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'post' ) );
	}
	
	public function test_handles_empty_rule_values() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'equal_to',
							'value' => array(), // Empty value
						),
					),
				),
			),
		);
		
		// Should not match with empty values
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'post' ) );
	}
	
	public function test_handles_invalid_condition_type() {
		$conditions = array(
			'logic' => 'or',
			'groups' => array(
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'invalid_type',
							'operator' => 'equal_to',
							'value' => array( 'test' ),
						),
					),
				),
			),
		);
		
		// Should not match with invalid condition type
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 1, 'post' ) );
	}
	
	public function test_complex_nested_conditions() {
		$conditions = array(
			'logic' => 'and',
			'groups' => array(
				array(
					'logic' => 'or',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'equal_to',
							'value' => array( 'post', 'page' ),
						),
						array(
							'conditionType' => 'singular',
							'operator' => 'equal_to',
							'value' => array( 100 ),
						),
					),
				),
				array(
					'logic' => 'and',
					'rules' => array(
						array(
							'conditionType' => 'post_type',
							'operator' => 'not_equal_to',
							'value' => array( 'product' ),
						),
					),
				),
			),
		);
		
		// Group 1: post_type=post OR singular=100 -> TRUE
		// Group 2: post_type!=product -> TRUE
		// Result: TRUE AND TRUE = TRUE
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'post' ) );
		
		// Group 1: post_type=product OR singular=100 -> FALSE
		// Group 2: post_type!=product -> FALSE
		// Result: FALSE AND FALSE = FALSE
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'product' ) );
		
		// Group 1: post_type=custom OR singular=100 -> FALSE
		// Group 2: post_type!=product -> TRUE
		// Result: FALSE AND TRUE = FALSE
		$this->assertFalse( Schema_Engine_Conditions::matches_conditions( $conditions, 10, 'custom' ) );
		
		// Group 1: post_type=custom OR singular=100 -> TRUE (singular matches)
		// Group 2: post_type!=product -> TRUE
		// Result: TRUE AND TRUE = TRUE
		$this->assertTrue( Schema_Engine_Conditions::matches_conditions( $conditions, 100, 'custom' ) );
	}
}
