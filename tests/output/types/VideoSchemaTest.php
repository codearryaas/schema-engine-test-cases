<?php
/**
 * Video Schema Test Cases
 *
 * Tests for Schema_Video class
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class VideoSchemaTest extends TestCase
{
    /**
     * Video schema instance
     *
     * @var \Schema_Video
     */
    private $schema;

    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_Video();
    }

    /**
     * Test that Schema_Video implements Schema_Builder_Interface
     */
    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    /**
     * Test get_schema_structure returns correct structure
     */
    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();

        $this->assertIsArray($structure);
        $this->assertEquals('VideoObject', $structure['@type']);
        $this->assertEquals('https://schema.org', $structure['@context']);
        $this->assertArrayHasKey('label', $structure);
        $this->assertArrayHasKey('description', $structure);
        $this->assertArrayHasKey('url', $structure);
        $this->assertArrayHasKey('icon', $structure);
        $this->assertEquals('video', $structure['icon']);
    }

    /**
     * Test get_fields returns array of field configurations
     */
    public function test_get_fields_returns_array()
    {
        $fields = $this->schema->get_fields();

        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);
    }

    /**
     * Test required fields are present
     */
    public function test_required_fields_are_present()
    {
        $fields = $this->schema->get_fields();
        $fieldNames = array_column($fields, 'name');

        $requiredFields = ['name', 'description', 'thumbnailUrl', 'uploadDate'];

        foreach ($requiredFields as $requiredField) {
            $this->assertContains($requiredField, $fieldNames, "Required field '{$requiredField}' is missing");
        }
    }

    /**
     * Test optional fields are present
     */
    public function test_optional_fields_are_present()
    {
        $fields = $this->schema->get_fields();
        $fieldNames = array_column($fields, 'name');

        $optionalFields = ['contentUrl', 'embedUrl', 'duration', 'url', 'width', 'height'];

        foreach ($optionalFields as $optionalField) {
            $this->assertContains($optionalField, $fieldNames, "Optional field '{$optionalField}' is missing");
        }
    }

    /**
     * Test Pro features placeholder is added when Pro is not active
     */
    public function test_pro_features_placeholder_added_without_pro()
    {
        $fields = $this->schema->get_fields();
        $fieldNames = array_column($fields, 'name');

        // Pro is not defined in tests, so placeholder should be present
        $this->assertContains('video_pro_features_placeholder', $fieldNames);

        // Find the placeholder field
        $placeholderField = null;
        foreach ($fields as $field) {
            if ($field['name'] === 'video_pro_features_placeholder') {
                $placeholderField = $field;
                break;
            }
        }

        $this->assertNotNull($placeholderField);
        $this->assertEquals('notice', $placeholderField['type']);
        $this->assertTrue($placeholderField['isPro']);
        $this->assertTrue($placeholderField['allowHtml']);
    }

    /**
     * Test build method with minimal required fields
     */
    public function test_build_with_minimal_fields()
    {
        $fields = [
            'name' => 'Test Video',
            'description' => 'Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
        ];

        $schema = $this->schema->build($fields);

        $this->assertIsArray($schema);
        $this->assertEquals('VideoObject', $schema['@type']);
        $this->assertEquals('Test Video', $schema['name']);
        $this->assertEquals('Test Description', $schema['description']);
        $this->assertEquals('https://example.com/thumb.jpg', $schema['thumbnailUrl']);
        $this->assertEquals('2024-01-01', $schema['uploadDate']);
    }

    /**
     * Test build method with default placeholders
     */
    public function test_build_with_default_placeholders()
    {
        $fields = [];

        $schema = $this->schema->build($fields);

        $this->assertIsArray($schema);
        $this->assertEquals('VideoObject', $schema['@type']);
        $this->assertEquals('{post_title}', $schema['name']);
        $this->assertEquals('{post_excerpt}', $schema['description']);
        $this->assertEquals('{featured_image}', $schema['thumbnailUrl']);
        $this->assertEquals('{post_date}', $schema['uploadDate']);
    }

    /**
     * Test build method with contentUrl
     */
    public function test_build_with_content_url()
    {
        $fields = [
            'name' => 'Test Video',
            'description' => 'Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
            'contentUrl' => 'https://example.com/video.mp4',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('contentUrl', $schema);
        $this->assertEquals('https://example.com/video.mp4', $schema['contentUrl']);
    }

    /**
     * Test build method with embedUrl
     */
    public function test_build_with_embed_url()
    {
        $fields = [
            'name' => 'Test Video',
            'description' => 'Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
            'embedUrl' => 'https://www.youtube.com/embed/VIDEO_ID',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('embedUrl', $schema);
        $this->assertEquals('https://www.youtube.com/embed/VIDEO_ID', $schema['embedUrl']);
    }

    /**
     * Test build method with duration
     */
    public function test_build_with_duration()
    {
        $fields = [
            'name' => 'Test Video',
            'description' => 'Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
            'duration' => 'PT5M30S',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('duration', $schema);
        $this->assertEquals('PT5M30S', $schema['duration']);
    }

    /**
     * Test build method with dimensions
     */
    public function test_build_with_dimensions()
    {
        $fields = [
            'name' => 'Test Video',
            'description' => 'Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
            'width' => '1920',
            'height' => '1080',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('width', $schema);
        $this->assertArrayHasKey('height', $schema);
        $this->assertEquals('1920', $schema['width']);
        $this->assertEquals('1080', $schema['height']);
    }

    /**
     * Test build method with URL
     */
    public function test_build_with_url()
    {
        $fields = [
            'name' => 'Test Video',
            'description' => 'Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
            'url' => 'https://example.com/video-page',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('url', $schema);
        $this->assertEquals('https://example.com/video-page', $schema['url']);
    }

    /**
     * Test build method with all fields
     */
    public function test_build_with_all_fields()
    {
        $fields = [
            'name' => 'Complete Test Video',
            'description' => 'Complete Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
            'contentUrl' => 'https://example.com/video.mp4',
            'embedUrl' => 'https://www.youtube.com/embed/VIDEO_ID',
            'duration' => 'PT1H30M',
            'url' => 'https://example.com/video-page',
            'width' => '1920',
            'height' => '1080',
        ];

        $schema = $this->schema->build($fields);

        $this->assertIsArray($schema);
        $this->assertEquals('VideoObject', $schema['@type']);
        $this->assertCount(11, $schema); // @type + 10 fields

        // Verify all fields are present
        $this->assertEquals('Complete Test Video', $schema['name']);
        $this->assertEquals('Complete Test Description', $schema['description']);
        $this->assertEquals('https://example.com/thumb.jpg', $schema['thumbnailUrl']);
        $this->assertEquals('2024-01-01', $schema['uploadDate']);
        $this->assertEquals('https://example.com/video.mp4', $schema['contentUrl']);
        $this->assertEquals('https://www.youtube.com/embed/VIDEO_ID', $schema['embedUrl']);
        $this->assertEquals('PT1H30M', $schema['duration']);
        $this->assertEquals('https://example.com/video-page', $schema['url']);
        $this->assertEquals('1920', $schema['width']);
        $this->assertEquals('1080', $schema['height']);
    }

    /**
     * Test that empty optional fields are not included in schema
     */
    public function test_empty_optional_fields_not_included()
    {
        $fields = [
            'name' => 'Test Video',
            'description' => 'Test Description',
            'thumbnailUrl' => 'https://example.com/thumb.jpg',
            'uploadDate' => '2024-01-01',
            'contentUrl' => '', // Empty
            'duration' => null, // Null
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayNotHasKey('contentUrl', $schema);
        $this->assertArrayNotHasKey('duration', $schema);
    }

    /**
     * Test field configuration structure
     */
    public function test_field_configuration_structure()
    {
        $fields = $this->schema->get_fields();

        foreach ($fields as $field) {
            // Every field should have a name and type
            $this->assertArrayHasKey('name', $field, 'Field missing name');
            $this->assertArrayHasKey('type', $field, "Field '{$field['name']}' missing type");

            // Skip notice fields for further validation
            if ($field['type'] === 'notice') {
                continue;
            }

            // Regular fields should have label
            $this->assertArrayHasKey('label', $field, "Field '{$field['name']}' missing label");
        }
    }

    /**
     * Test that field names are unique
     */
    public function test_field_names_are_unique()
    {
        $fields = $this->schema->get_fields();
        $fieldNames = array_column($fields, 'name');

        $uniqueNames = array_unique($fieldNames);

        $this->assertCount(
            count($fieldNames),
            $uniqueNames,
            'Duplicate field names detected'
        );
    }
}
