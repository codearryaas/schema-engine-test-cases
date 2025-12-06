<?php
/**
 * Breadcrumb Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine/includes/output/types/class-breadcrumb-schema.php';

class BreadcrumbSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        $this->schema = new \Schema_Breadcrumb();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        $this->assertEquals('BreadcrumbList', $structure['@type']);
        $this->assertEquals('https://schema.org', $structure['@context']);
        $this->assertArrayHasKey('label', $structure);
        $this->assertArrayHasKey('description', $structure);
        $this->assertEquals('arrow-right', $structure['icon']);
    }

    public function test_get_fields_returns_empty_array()
    {
        // Breadcrumbs have no user-editable fields
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);
        $this->assertEmpty($fields);
    }

    public function test_build_returns_empty_if_no_post()
    {
        global $post;
        $post = null;

        $schema = $this->schema->build([]);
        $this->assertEmpty($schema);
    }

    public function test_build_simple_page_breadcrumb()
    {
        global $post;
        $post = (object) ['ID' => 123, 'post_title' => 'Sample Page', 'post_type' => 'page'];

        Functions\when('get_home_url')->justReturn('https://example.com');
        Functions\when('is_singular')->justReturn(true);
        Functions\when('is_post_type_hierarchical')->justReturn(false);
        Functions\when('get_post_type')->justReturn('page');
        Functions\when('get_the_title')->justReturn('Sample Page');
        Functions\when('get_permalink')->justReturn('https://example.com/sample-page');
        Functions\when('get_the_category')->justReturn([]);

        $settings = [
            'breadcrumb_show_home' => true,
            'breadcrumb_home_text' => 'Home'
        ];

        $schema = $this->schema->build($settings);

        $this->assertEquals('BreadcrumbList', $schema['@type']);
        $this->assertCount(2, $schema['itemListElement']);

        // Home item
        $this->assertEquals('Home', $schema['itemListElement'][0]['name']);
        $this->assertEquals(1, $schema['itemListElement'][0]['position']);
        $this->assertEquals('https://example.com', $schema['itemListElement'][0]['item']);

        // Current Page item
        $this->assertEquals('Sample Page', $schema['itemListElement'][1]['name']);
        $this->assertEquals(2, $schema['itemListElement'][1]['position']);
        $this->assertEquals('https://example.com/sample-page', $schema['itemListElement'][1]['item']);
    }
}
