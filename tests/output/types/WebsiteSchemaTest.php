<?php
/**
 * Website Schema Test Cases
 *
 * @package Schema_Engine_Pro
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;

// Load Pro Website Schema if available
$pro_website_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/output/types/class-website-schema.php';
if (file_exists($pro_website_path)) {
    require_once $pro_website_path;
}

class WebsiteSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists('\Schema_Website')) {
            $this->schema = new \Schema_Website();
        } else {
            $this->markTestSkipped('Schema_Website class not found');
        }
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        $this->assertEquals('WebSite', $structure['@type']);
    }

    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);
        $this->assertEmpty($fields);
    }
}
