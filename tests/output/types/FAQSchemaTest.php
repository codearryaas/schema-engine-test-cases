<?php
/**
 * FAQ Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class FAQSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_FAQ();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_build_faq_with_questions()
    {
        $fields = array(
            'items' => array(
                array(
                    'question' => 'What is Schema Engine?',
                    'answer' => 'A WordPress plugin for structured data.',
                ),
                array(
                    'question' => 'Is it free?',
                    'answer' => 'Yes, with a Pro version available.',
                ),
            ),
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('FAQPage', $schema['@type']);
        $this->assertArrayHasKey('mainEntity', $schema);
        $this->assertCount(2, $schema['mainEntity']);
        $this->assertEquals('Question', $schema['mainEntity'][0]['@type']);
        $this->assertEquals('What is Schema Engine?', $schema['mainEntity'][0]['name']);
    }

    public function test_filters_empty_questions()
    {
        $fields = array(
            'items' => array(
                array(
                    'question' => 'Valid question?',
                    'answer' => 'Valid answer.',
                ),
                array(
                    'question' => '',
                    'answer' => 'No question.',
                ),
                array(
                    'question' => 'Another question?',
                    'answer' => '',
                ),
            ),
        );

        $schema = $this->schema->build($fields);

        // Should only include complete Q&A pairs
        $this->assertArrayHasKey('mainEntity', $schema);
        $this->assertCount(1, $schema['mainEntity']);
    }

    public function test_build_faq_with_meta()
    {
        $fields = array(
            'name' => 'My FAQ Page',
            'url' => 'https://example.com/faq',
            'items' => array(
                array(
                    'question' => 'Q1',
                    'answer' => 'A1',
                ),
            ),
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('My FAQ Page', $schema['name']);
        $this->assertEquals('https://example.com/faq', $schema['url']);
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();

        $this->assertEquals('FAQPage', $structure['@type']);
        $this->assertEquals('https://schema.org', $structure['@context']);
        $this->assertArrayHasKey('label', $structure);
        $this->assertArrayHasKey('icon', $structure);
    }

    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();

        $this->assertIsArray($fields);

        // Find the items repeater
        $itemsField = null;
        foreach ($fields as $field) {
            if ($field['name'] === 'items') {
                $itemsField = $field;
                break;
            }
        }

        $this->assertNotNull($itemsField, 'Items field should exist');
        $this->assertEquals('repeater', $itemsField['type']);
        $this->assertTrue($itemsField['required']);

        // Verify subfields
        $this->assertArrayHasKey('fields', $itemsField);
        $subFieldNames = array_column($itemsField['fields'], 'name');
        $this->assertContains('question', $subFieldNames);
        $this->assertContains('answer', $subFieldNames);
    }
}
