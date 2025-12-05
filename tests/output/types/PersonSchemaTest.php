<?php
/**
 * Person Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class PersonSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_Person();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_build_basic_person()
    {
        $fields = array(
            'name' => 'John Doe',
            'url' => 'https://johndoe.com',
            'description' => 'Author and developer',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('Person', $schema['@type']);
        $this->assertEquals('John Doe', $schema['name']);
        $this->assertEquals('https://johndoe.com', $schema['url']);
        $this->assertEquals('Author and developer', $schema['description']);
    }

    public function test_build_with_job_title()
    {
        $fields = array(
            'name' => 'John Doe',
            'jobTitle' => 'Software Engineer',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('Software Engineer', $schema['jobTitle']);
    }

    public function test_build_with_affiliation()
    {
        $fields = array(
            'name' => 'John Doe',
            'worksFor' => 'Acme Corporation',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('worksFor', $schema);
        $this->assertEquals('Organization', $schema['worksFor']['@type']);
        $this->assertEquals('Acme Corporation', $schema['worksFor']['name']);
    }

    public function test_build_with_social_profiles()
    {
        $fields = array(
            'name' => 'John Doe',
            'socialProfiles' => array(
                array('url' => 'https://twitter.com/johndoe'),
                array('url' => 'https://linkedin.com/in/johndoe'),
            ),
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('sameAs', $schema);
        $this->assertCount(2, $schema['sameAs']);
    }
}
