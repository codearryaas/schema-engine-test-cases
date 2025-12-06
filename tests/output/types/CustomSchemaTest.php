<?php
/**
 * Custom Schema Test Cases
 *
 * @package Schema_Engine_Pro
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;

// Load Pro Custom Schema if available
$pro_custom_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/output/types/class-custom-schema.php';
if (file_exists($pro_custom_path)) {
    require_once $pro_custom_path;
}

class CustomSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists('\Schema_Custom')) {
            $this->schema = new \Schema_Custom();
        } else {
            $this->markTestSkipped('Schema_Custom class not found');
        }
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        $this->assertEquals('Custom', $structure['@type']);
    }

    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);

        $fieldNames = array_column($fields, 'name');
        $this->assertContains('custom_structure', $fieldNames);
    }
}
