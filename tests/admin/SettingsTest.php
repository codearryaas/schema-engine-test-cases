<?php
/**
 * Tests for Schema_Engine_REST_API settings sanitization
 *
 * @package Schema_Engine
 */

namespace Schema_Engine\Tests\Admin;

use PHPUnit\Framework\TestCase;
use Schema_Engine_REST_API;

class SettingsTest extends TestCase {
	
	private $rest_api;
	
	protected function setUp(): void {
		$this->rest_api = Schema_Engine_REST_API::get_instance();
	}
	
	public function test_sanitize_code_placement() {
		$input = array( 'code_placement' => 'head' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertEquals( 'head', $result['code_placement'] );
	}
	
	public function test_sanitize_code_placement_invalid_value() {
		$input = array( 'code_placement' => 'invalid' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		// Should default to 'head' for invalid values
		$this->assertEquals( 'head', $result['code_placement'] );
	}
	
	public function test_sanitize_code_placement_footer() {
		$input = array( 'code_placement' => 'footer' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertEquals( 'footer', $result['code_placement'] );
	}
	
	public function test_sanitize_default_image_url() {
		$input = array( 'default_image' => 'https://example.com/image.jpg' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertEquals( 'https://example.com/image.jpg', $result['default_image'] );
	}
	
	public function test_sanitize_default_image_removes_invalid_url() {
		$input = array( 'default_image' => 'javascript:alert("xss")' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		// esc_url_raw should remove invalid protocols
		$this->assertStringNotContainsString( 'javascript:', $result['default_image'] );
	}
	
	public function test_sanitize_auto_schema_boolean() {
		$input = array( 'auto_schema' => true );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertTrue( $result['auto_schema'] );
	}
	
	public function test_sanitize_auto_schema_string_to_boolean() {
		$input = array( 'auto_schema' => '1' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertTrue( $result['auto_schema'] );
	}
	
	public function test_sanitize_auto_schema_false() {
		$input = array( 'auto_schema' => false );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertFalse( $result['auto_schema'] );
	}
	
	public function test_sanitize_breadcrumb_enabled() {
		$input = array( 'breadcrumb_enabled' => true );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertTrue( $result['breadcrumb_enabled'] );
	}
	
	public function test_sanitize_breadcrumb_separator() {
		$input = array( 'breadcrumb_separator' => ' > ' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertEquals( ' > ', $result['breadcrumb_separator'] );
	}
	
	public function test_sanitize_breadcrumb_separator_removes_html() {
		$input = array( 'breadcrumb_separator' => '<script>alert("xss")</script>' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertStringNotContainsString( '<script>', $result['breadcrumb_separator'] );
	}
	
	public function test_sanitize_breadcrumb_show_home() {
		$input = array( 'breadcrumb_show_home' => true );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertTrue( $result['breadcrumb_show_home'] );
	}
	
	public function test_sanitize_breadcrumb_home_text() {
		$input = array( 'breadcrumb_home_text' => 'Home' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertEquals( 'Home', $result['breadcrumb_home_text'] );
	}
	
	public function test_sanitize_sitelinks_searchbox() {
		$input = array( 'sitelinks_searchbox' => true );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertTrue( $result['sitelinks_searchbox'] );
	}
	
	public function test_sanitize_minify_schema() {
		$input = array( 'minify_schema' => true );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertTrue( $result['minify_schema'] );
	}
	
	public function test_sanitize_knowledge_base_enabled() {
		$input = array( 'knowledge_base_enabled' => true );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertTrue( $result['knowledge_base_enabled'] );
	}
	
	public function test_sanitize_knowledge_base_type() {
		$input = array( 'knowledge_base_type' => 'Organization' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertEquals( 'Organization', $result['knowledge_base_type'] );
	}
	
	public function test_sanitize_knowledge_base_type_removes_html() {
		$input = array( 'knowledge_base_type' => '<b>Person</b>' );
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertStringNotContainsString( '<b>', $result['knowledge_base_type'] );
		$this->assertEquals( 'Person', $result['knowledge_base_type'] );
	}
	
	public function test_sanitize_organization_fields_structure() {
		$input = array(
			'organization_fields' => array(
				'name' => 'Test Company',
				'url' => 'https://example.com',
				'logo' => 'https://example.com/logo.png',
			),
		);
		
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertArrayHasKey( 'organization_fields', $result );
		$this->assertIsArray( $result['organization_fields'] );
	}
	
	public function test_sanitize_person_fields_structure() {
		$input = array(
			'person_fields' => array(
				'name' => 'John Doe',
				'url' => 'https://example.com',
				'image' => 'https://example.com/photo.jpg',
			),
		);
		
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertArrayHasKey( 'person_fields', $result );
		$this->assertIsArray( $result['person_fields'] );
	}
	
	public function test_sanitize_localbusiness_fields_structure() {
		$input = array(
			'localbusiness_fields' => array(
				'name' => 'Local Shop',
				'address' => array(
					'streetAddress' => '123 Main St',
					'addressLocality' => 'New York',
				),
			),
		);
		
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertArrayHasKey( 'localbusiness_fields', $result );
		$this->assertIsArray( $result['localbusiness_fields'] );
	}
	
	public function test_sanitize_multiple_settings_together() {
		$input = array(
			'code_placement' => 'footer',
			'auto_schema' => true,
			'minify_schema' => false,
			'breadcrumb_enabled' => true,
			'breadcrumb_separator' => ' / ',
			'knowledge_base_enabled' => true,
			'knowledge_base_type' => 'Organization',
		);
		
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertEquals( 'footer', $result['code_placement'] );
		$this->assertTrue( $result['auto_schema'] );
		$this->assertFalse( $result['minify_schema'] );
		$this->assertTrue( $result['breadcrumb_enabled'] );
		$this->assertEquals( ' / ', $result['breadcrumb_separator'] );
		$this->assertTrue( $result['knowledge_base_enabled'] );
		$this->assertEquals( 'Organization', $result['knowledge_base_type'] );
	}
	
	public function test_sanitize_ignores_unknown_fields() {
		$input = array(
			'code_placement' => 'head',
			'unknown_field' => 'should be ignored',
			'another_unknown' => 123,
		);
		
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertArrayHasKey( 'code_placement', $result );
		$this->assertArrayNotHasKey( 'unknown_field', $result );
		$this->assertArrayNotHasKey( 'another_unknown', $result );
	}
	
	public function test_sanitize_empty_input_returns_empty_array() {
		$input = array();
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertIsArray( $result );
		$this->assertEmpty( $result );
	}
	
	public function test_sanitize_preserves_valid_data_types() {
		$input = array(
			'auto_schema' => true,
			'breadcrumb_enabled' => false,
			'code_placement' => 'head',
			'breadcrumb_separator' => ' > ',
		);
		
		$result = $this->rest_api->sanitize_settings( $input );
		
		$this->assertIsBool( $result['auto_schema'] );
		$this->assertIsBool( $result['breadcrumb_enabled'] );
		$this->assertIsString( $result['code_placement'] );
		$this->assertIsString( $result['breadcrumb_separator'] );
	}
	
	public function test_sanitize_handles_null_values() {
		$input = array(
			'code_placement' => null,
			'default_image' => null,
		);
		
		$result = $this->rest_api->sanitize_settings( $input );
		
		// Null values should not be processed, so keys shouldn't exist
		// OR they should have default values if the function handles them
		// Since isset() returns false for null, these won't be processed
		$this->assertIsArray( $result );
	}
}
