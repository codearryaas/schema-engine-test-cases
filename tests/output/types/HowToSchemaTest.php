<?php
/**
 * HowTo Schema Test Cases
 *
 * @package Schema_Engine_Pro
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;

// Load Pro HowTo Schema if available
$pro_howto_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/output/types/class-howto-schema.php';
if (file_exists($pro_howto_path)) {
    require_once $pro_howto_path;
}

class HowToSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists('\Schema_HowTo')) {
            $this->schema = new \Schema_HowTo();
        } else {
            $this->markTestSkipped('Schema_HowTo class not found');
        }
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        $this->assertEquals('HowTo', $structure['@type']);
    }

    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);

        $fieldNames = array_column($fields, 'name');
        $this->assertContains('name', $fieldNames);
        $this->assertContains('steps', $fieldNames);
        $this->assertContains('supplies', $fieldNames);
        $this->assertContains('tools', $fieldNames);
    }
}
