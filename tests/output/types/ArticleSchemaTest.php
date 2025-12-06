<?php
/**
 * Article Schema Test Cases
 *
 * Tests for Schema_Article class
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


// Mock Schema_Reference_Resolver if not exists (Global Scope)
if (!class_exists('\Schema_Reference_Resolver')) {
    eval ('class Schema_Reference_Resolver {
        public static function is_reference($value) {
            return is_array($value) && isset($value["type"]) && $value["type"] === "reference";
        }
        public static function resolve($value) {
            if (isset($value["id"]) && $value["id"] === "ref-author") {
                return ["@type" => "Person", "@id" => "https://example.com/#person"];
            }
            if (isset($value["id"]) && $value["id"] === "ref-publisher") {
                return ["@type" => "Organization", "@id" => "https://example.com/#org"];
            }
            return null;
        }
    }');
}

// Load Pro Article Schema if available
$pro_article_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/output/extend-types/class-article-schema.php';
if (file_exists($pro_article_path)) {
    require_once $pro_article_path;
}

class ArticleSchemaTest extends TestCase
{
    /**
     * Article schema instance
     *
     * @var \Schema_Article
     */
    private $schema;

    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_Article();
    }

    /**
     * Test that Schema_Article implements Schema_Builder_Interface
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
        $this->assertEquals('Article', $structure['@type']);
        $this->assertEquals('https://schema.org', $structure['@context']);
        $this->assertArrayHasKey('label', $structure);
        $this->assertArrayHasKey('description', $structure);
        $this->assertArrayHasKey('url', $structure);
        $this->assertArrayHasKey('icon', $structure);
        $this->assertArrayHasKey('subtypes', $structure);
        $this->assertEquals('file-text', $structure['icon']);
    }

    /**
     * Test article subtypes are defined
     */
    public function test_article_subtypes_defined()
    {
        $structure = $this->schema->get_schema_structure();

        $this->assertArrayHasKey('subtypes', $structure);
        $this->assertIsArray($structure['subtypes']);

        $expectedSubtypes = ['Article', 'BlogPosting', 'NewsArticle', 'ScholarlyArticle', 'TechArticle'];

        foreach ($expectedSubtypes as $subtype) {
            $this->assertArrayHasKey($subtype, $structure['subtypes']);
        }
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

        $requiredFields = [
            'headline',
            'authorName',
            'imageUrl',
            'publisherName',
            'publisherLogo',
            'datePublished'
        ];

        foreach ($requiredFields as $requiredField) {
            $this->assertContains($requiredField, $fieldNames, "Required field '{$requiredField}' is missing");
        }
    }

    /**
     * Test articleType field exists and has correct options
     */
    public function test_article_type_field_configuration()
    {
        $fields = $this->schema->get_fields();

        $articleTypeField = null;
        foreach ($fields as $field) {
            if ($field['name'] === 'articleType') {
                $articleTypeField = $field;
                break;
            }
        }

        $this->assertNotNull($articleTypeField, 'articleType field not found');
        $this->assertEquals('select', $articleTypeField['type']);
        $this->assertArrayHasKey('options', $articleTypeField);
        $this->assertIsArray($articleTypeField['options']);
        $this->assertCount(5, $articleTypeField['options']);
        $this->assertEquals('Article', $articleTypeField['default']);
    }

    /**
     * Test Pro features placeholder is added when Pro is not active
     */
    public function test_pro_features_placeholder_added_without_pro()
    {
        $fields = $this->schema->get_fields();
        $fieldNames = array_column($fields, 'name');

        // Pro is not defined in tests, so placeholder should be present
        $this->assertContains('paywall_settings_placeholder', $fieldNames);

        // Find the placeholder field
        $placeholderField = null;
        foreach ($fields as $field) {
            if ($field['name'] === 'paywall_settings_placeholder') {
                $placeholderField = $field;
                break;
            }
        }

        $this->assertNotNull($placeholderField);
        $this->assertEquals('notice', $placeholderField['type']);
        $this->assertTrue($placeholderField['isPro']);
    }

    /**
     * Test build method with minimal required fields
     */
    public function test_build_with_minimal_fields()
    {
        $fields = [
            'headline' => 'Test Article',
            'authorName' => 'John Doe',
            'datePublished' => '2024-01-01',
            'dateModified' => '2024-01-02',
        ];

        $schema = $this->schema->build($fields);

        $this->assertIsArray($schema);
        $this->assertEquals('Article', $schema['@type']);
        $this->assertEquals('Test Article', $schema['headline']);
        $this->assertEquals('2024-01-01', $schema['datePublished']);
        $this->assertEquals('2024-01-02', $schema['dateModified']);
    }

    /**
     * Test build method with default placeholders
     */
    public function test_build_with_default_placeholders()
    {
        $fields = [];

        $schema = $this->schema->build($fields);

        $this->assertIsArray($schema);
        $this->assertEquals('Article', $schema['@type']);
        $this->assertEquals('{post_title}', $schema['headline']);
        $this->assertEquals('{post_url}', $schema['url']);
        $this->assertEquals('{post_date}', $schema['datePublished']);
        $this->assertEquals('{post_modified}', $schema['dateModified']);
        $this->assertEquals('{featured_image}', $schema['image']);
    }

    /**
     * Test build method with BlogPosting article type
     */
    public function test_build_with_blog_posting_type()
    {
        $fields = [
            'articleType' => 'BlogPosting',
            'headline' => 'Blog Post Title',
        ];

        $schema = $this->schema->build($fields);

        $this->assertEquals('BlogPosting', $schema['@type']);
    }

    /**
     * Test build method with NewsArticle type
     */
    public function test_build_with_news_article_type()
    {
        $fields = [
            'articleType' => 'NewsArticle',
            'headline' => 'Breaking News',
        ];

        $schema = $this->schema->build($fields);

        $this->assertEquals('NewsArticle', $schema['@type']);
    }

    /**
     * Test author structure
     */
    public function test_author_structure()
    {
        $fields = [
            'authorName' => 'Jane Smith',
            'authorUrl' => 'https://example.com/author/jane',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('author', $schema);
        $this->assertIsArray($schema['author']);
        $this->assertEquals('Person', $schema['author']['@type']);
        $this->assertEquals('Jane Smith', $schema['author']['name']);
        $this->assertEquals('https://example.com/author/jane', $schema['author']['url']);
    }

    /**
     * Test author without URL
     */
    public function test_author_without_url()
    {
        $fields = [
            'authorName' => 'Jane Smith',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('author', $schema);
        $this->assertEquals('Jane Smith', $schema['author']['name']);
        $this->assertArrayNotHasKey('url', $schema['author']);
    }

    /**
     * Test publisher structure
     */
    public function test_publisher_structure()
    {
        $fields = [
            'publisherName' => 'Example Publishing',
            'publisherLogo' => 'https://example.com/logo.png',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('publisher', $schema);
        $this->assertIsArray($schema['publisher']);
        $this->assertEquals('Organization', $schema['publisher']['@type']);
        $this->assertEquals('Example Publishing', $schema['publisher']['name']);
        $this->assertArrayHasKey('logo', $schema['publisher']);
        $this->assertEquals('ImageObject', $schema['publisher']['logo']['@type']);
        $this->assertEquals('https://example.com/logo.png', $schema['publisher']['logo']['url']);
    }

    /**
     * Test publisher with default values
     */
    public function test_publisher_with_defaults()
    {
        $fields = [];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('publisher', $schema);
        $this->assertEquals('{site_name}', $schema['publisher']['name']);
        $this->assertEquals('{site_logo}', $schema['publisher']['logo']['url']);
    }

    /**
     * Test description field
     */
    public function test_description_field()
    {
        $fields = [
            'description' => 'This is a test article description.',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('description', $schema);
        $this->assertEquals('This is a test article description.', $schema['description']);
    }

    /**
     * Test image field
     */
    public function test_image_field()
    {
        $fields = [
            'imageUrl' => 'https://example.com/image.jpg',
        ];

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('image', $schema);
        $this->assertEquals('https://example.com/image.jpg', $schema['image']);
    }

    /**
     * Test build with all fields
     */
    public function test_build_with_all_fields()
    {
        $fields = [
            'articleType' => 'TechArticle',
            'headline' => 'Complete Article Test',
            'url' => 'https://example.com/article',
            'description' => 'Complete test description',
            'authorName' => 'Test Author',
            'authorUrl' => 'https://example.com/author',
            'imageUrl' => 'https://example.com/image.jpg',
            'publisherName' => 'Test Publisher',
            'publisherLogo' => 'https://example.com/logo.png',
            'datePublished' => '2024-01-01',
            'dateModified' => '2024-01-15',
        ];

        $schema = $this->schema->build($fields);

        $this->assertIsArray($schema);
        $this->assertEquals('TechArticle', $schema['@type']);
        $this->assertEquals('Complete Article Test', $schema['headline']);
        $this->assertEquals('https://example.com/article', $schema['url']);
        $this->assertEquals('Complete test description', $schema['description']);
        $this->assertEquals('https://example.com/image.jpg', $schema['image']);
        $this->assertEquals('2024-01-01', $schema['datePublished']);
        $this->assertEquals('2024-01-15', $schema['dateModified']);

        // Verify author
        $this->assertEquals('Person', $schema['author']['@type']);
        $this->assertEquals('Test Author', $schema['author']['name']);
        $this->assertEquals('https://example.com/author', $schema['author']['url']);

        // Verify publisher
        $this->assertEquals('Organization', $schema['publisher']['@type']);
        $this->assertEquals('Test Publisher', $schema['publisher']['name']);
        $this->assertEquals('ImageObject', $schema['publisher']['logo']['@type']);
        $this->assertEquals('https://example.com/logo.png', $schema['publisher']['logo']['url']);
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
     * Test default article type when not specified
     */
    public function test_default_article_type()
    {
        $fields = [];
        $schema = $this->schema->build($fields);

        $this->assertEquals('Article', $schema['@type']);
    }

    /**
     * Test all article subtypes
     */
    public function test_all_article_subtypes()
    {
        $subtypes = ['Article', 'BlogPosting', 'NewsArticle', 'ScholarlyArticle', 'TechArticle'];

        foreach ($subtypes as $subtype) {
            $fields = ['articleType' => $subtype];
            $schema = $this->schema->build($fields);

            $this->assertEquals(
                $subtype,
                $schema['@type'],
                "Failed to build schema with {$subtype} type"
            );
        }
    }

    /**
     * Test Pro extension fields
     */
    public function test_pro_extension_fields()
    {
        if (!class_exists('\Schema_Article_Pro')) {
            $this->markTestSkipped('Schema_Article_Pro class not found');
        }

        $fields = $this->schema->get_fields();
        // Simulate the filter call
        $proFields = \Schema_Article_Pro::extend_article_fields($fields, 'Article');

        $fieldNames = array_column($proFields, 'name');

        // Check for new fields
        $this->assertContains('use_author_reference', $fieldNames);
        $this->assertContains('author_reference', $fieldNames);
        $this->assertContains('use_publisher_reference', $fieldNames);
        $this->assertContains('publisher_reference', $fieldNames);

        // Check dependency logic
        foreach ($proFields as $field) {
            if ($field['name'] === 'authorName') {
                $this->assertEquals('use_author_reference', $field['dependsOn']);
                $this->assertFalse($field['showWhen']);
            }
            if ($field['name'] === 'publisherName') {
                $this->assertEquals('use_publisher_reference', $field['dependsOn']);
                $this->assertFalse($field['showWhen']);
            }
        }
    }

    /**
     * Test Pro relationship processing
     */
    public function test_pro_relationship_processing()
    {
        if (!class_exists('\Schema_Article_Pro')) {
            $this->markTestSkipped('Schema_Article_Pro class not found');
        }

        // Mock Schema output
        $schema = [
            '@type' => 'Article',
            'author' => ['@type' => 'Person', 'name' => '{author_name}'],
            'publisher' => ['@type' => 'Organization', 'name' => '{site_name}']
        ];

        // 1. Test Author Reference
        $fields = [
            'use_author_reference' => true,
            'author_reference' => ['type' => 'reference', 'id' => 'ref-author']
        ];

        $processed = \Schema_Article_Pro::process_article_relationships($schema, 'Article', $fields);

        $this->assertEquals('https://example.com/#person', $processed['author']['@id']);
        $this->assertEquals('Person', $processed['author']['@type']);

        // 2. Test Publisher Reference
        $fields = [
            'use_publisher_reference' => true,
            'publisher_reference' => ['type' => 'reference', 'id' => 'ref-publisher']
        ];

        $processed = \Schema_Article_Pro::process_article_relationships($schema, 'Article', $fields);

        $this->assertEquals('https://example.com/#org', $processed['publisher']['@id']);
        $this->assertEquals('Organization', $processed['publisher']['@type']);
    }
}
