<?php
/**
 * Podcast Schema Test Cases
 *
 * @package Schema_Engine_Pro
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;

// Load Pro Podcast Schema if available
$pro_podcast_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/output/types/class-podcast-episode-schema.php';
if (file_exists($pro_podcast_path)) {
    require_once $pro_podcast_path;
}

class PodcastSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists('\Schema_Podcast_Episode')) {
            $this->schema = new \Schema_Podcast_Episode();
        } else {
            $this->markTestSkipped('Schema_Podcast_Episode class not found');
        }
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        $this->assertEquals('PodcastEpisode', $structure['@type']);
    }

    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);

        $fieldNames = array_column($fields, 'name');
        $this->assertContains('headline', $fieldNames);
        $this->assertContains('url', $fieldNames);
    }
}
