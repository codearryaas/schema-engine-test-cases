<?php
/**
 * LocalBusiness Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class LocalBusinessSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_LocalBusiness();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_build_basic_local_business()
    {
        $fields = array(
            'name' => 'Joe\'s Pizza',
            'url' => 'https://joespizza.com',
            'description' => 'Best pizza in town',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('LocalBusiness', $schema['@type']);
        $this->assertEquals('Joe\'s Pizza', $schema['name']);
    }

    public function test_build_with_geo_coordinates()
    {
        $fields = array(
            'name' => 'Business',
            'latitude' => '40.7128',
            'longitude' => '-74.0060',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('geo', $schema);
        $this->assertEquals('GeoCoordinates', $schema['geo']['@type']);
        $this->assertEquals('40.7128', $schema['geo']['latitude']);
        $this->assertEquals('-74.0060', $schema['geo']['longitude']);
    }

    public function test_build_with_price_range()
    {
        $fields = array(
            'name' => 'Restaurant',
            'priceRange' => '$$$',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('$$$', $schema['priceRange']);
    }

    public function test_business_subtypes()
    {
        $types = array('Restaurant', 'Store', 'Hotel', 'AutoDealer');

        foreach ($types as $type) {
            $fields = array(
                'businessType' => $type,
                'name' => 'Test Business',
            );

            $schema = $this->schema->build($fields);
            $this->assertEquals($type, $schema['@type']);
        }
    }
    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);

        $fieldNames = array_column($fields, 'name');
        $this->assertContains('name', $fieldNames);
        $this->assertContains('streetAddress', $fieldNames);
        $this->assertContains('openingHours', $fieldNames);
    }
}
