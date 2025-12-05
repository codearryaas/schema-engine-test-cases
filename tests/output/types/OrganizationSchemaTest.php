<?php
/**
 * Organization Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class OrganizationSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_Organization();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        
        $this->assertIsArray($structure);
        $this->assertEquals('Organization', $structure['@type']);
        $this->assertEquals('https://schema.org', $structure['@context']);
        $this->assertArrayHasKey('subtypes', $structure);
    }

    public function test_build_basic_organization()
    {
        $fields = array(
            'name' => 'Test Organization',
            'url' => 'https://example.com',
            'description' => 'A test organization',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('Organization', $schema['@type']);
        $this->assertEquals('Test Organization', $schema['name']);
        $this->assertEquals('https://example.com', $schema['url']);
        $this->assertEquals('A test organization', $schema['description']);
    }

    public function test_build_with_contact_info()
    {
        $fields = array(
            'name' => 'Test Org',
            'url' => 'https://example.com',
            'phone' => '+1-555-0123',
            'email' => 'info@example.com',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('+1-555-0123', $schema['telephone']);
        $this->assertEquals('info@example.com', $schema['email']);
    }

    public function test_build_with_address()
    {
        $fields = array(
            'name' => 'Test Org',
            'url' => 'https://example.com',
            'streetAddress' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postalCode' => '10001',
            'country' => 'US',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('address', $schema);
        $this->assertEquals('PostalAddress', $schema['address']['@type']);
        $this->assertEquals('123 Main St', $schema['address']['streetAddress']);
        $this->assertEquals('New York', $schema['address']['addressLocality']);
        $this->assertEquals('NY', $schema['address']['addressRegion']);
        $this->assertEquals('10001', $schema['address']['postalCode']);
        $this->assertEquals('US', $schema['address']['addressCountry']);
    }

    public function test_build_with_social_profiles()
    {
        $fields = array(
            'name' => 'Test Org',
            'url' => 'https://example.com',
            'socialProfiles' => array(
                array('url' => 'https://facebook.com/testorg'),
                array('url' => 'https://twitter.com/testorg'),
                array('url' => 'https://linkedin.com/company/testorg'),
            ),
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('sameAs', $schema);
        $this->assertIsArray($schema['sameAs']);
        $this->assertCount(3, $schema['sameAs']);
        $this->assertContains('https://facebook.com/testorg', $schema['sameAs']);
    }

    public function test_filters_invalid_social_profiles()
    {
        $fields = array(
            'name' => 'Test Org',
            'url' => 'https://example.com',
            'socialProfiles' => array(
                array('url' => 'https://facebook.com/valid'),
                array('url' => 'not-a-url'),
                array('url' => ''),
                array('url' => 'http://site_url/test'), // Placeholder
            ),
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('sameAs', $schema);
        $this->assertCount(1, $schema['sameAs']); // Only valid URL
        $this->assertEquals('https://facebook.com/valid', $schema['sameAs'][0]);
    }

    public function test_allows_template_variables_in_social_profiles()
    {
        $fields = array(
            'name' => 'Test Org',
            'url' => 'https://example.com',
            'socialProfiles' => array(
                array('url' => '{meta:facebook_url}'),
                array('url' => '{option:twitter_url}'),
            ),
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('sameAs', $schema);
        $this->assertContains('{meta:facebook_url}', $schema['sameAs']);
        $this->assertContains('{option:twitter_url}', $schema['sameAs']);
    }

    public function test_logo_with_custom_value()
    {
        $fields = array(
            'name' => 'Test Org',
            'url' => 'https://example.com',
            'logo' => 'https://example.com/custom-logo.png',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('logo', $schema);
        $this->assertEquals('ImageObject', $schema['logo']['@type']);
        $this->assertEquals('https://example.com/custom-logo.png', $schema['logo']['url']);
    }

    public function test_logo_defaults_to_site_logo_variable()
    {
        $fields = array(
            'name' => 'Test Org',
            'url' => 'https://example.com',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('logo', $schema);
        $this->assertEquals('{site_logo}', $schema['logo']['url']);
    }

    public function test_organization_subtypes()
    {
        $types = array('Corporation', 'EducationalOrganization', 'GovernmentOrganization', 'NGO');

        foreach ($types as $type) {
            $fields = array(
                'organizationType' => $type,
                'name' => 'Test Org',
                'url' => 'https://example.com',
            );

            $schema = $this->schema->build($fields);
            $this->assertEquals($type, $schema['@type']);
        }
    }

    public function test_get_fields_returns_array()
    {
        $fields = $this->schema->get_fields();
        
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);
        
        // Check required fields exist
        $fieldNames = array_column($fields, 'name');
        $this->assertContains('name', $fieldNames);
        $this->assertContains('url', $fieldNames);
        $this->assertContains('logo', $fieldNames);
        $this->assertContains('socialProfiles', $fieldNames);
    }
}
