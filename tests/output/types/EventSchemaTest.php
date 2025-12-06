<?php
/**
 * Event Schema Test Cases
 *
 * @package Schema_Engine_Pro
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;

// Load Pro Event Schema if available
$pro_event_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/output/types/class-event-schema.php';
if (file_exists($pro_event_path)) {
    require_once $pro_event_path;
}

class EventSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists('\Schema_Event')) {
            $this->schema = new \Schema_Event();
        } else {
            $this->markTestSkipped('Schema_Event class not found');
        }
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        $this->assertEquals('Event', $structure['@type']);
        $this->assertArrayHasKey('subtypes', $structure);
    }

    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);

        $fieldNames = array_column($fields, 'name');
        $this->assertContains('name', $fieldNames);
        $this->assertContains('startDate', $fieldNames);
        $this->assertContains('locationType', $fieldNames);
    }
}
