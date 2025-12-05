<?php
/**
 * Product Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class ProductSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_Product();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_build_basic_product()
    {
        $fields = array(
            'name' => 'Test Product',
            'description' => 'A test product',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('Product', $schema['@type']);
        $this->assertEquals('Test Product', $schema['name']);
    }

    public function test_build_with_offer()
    {
        $fields = array(
            'name' => 'Product',
            'price' => '29.99',
            'priceCurrency' => 'USD',
            'availability' => 'InStock',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('offers', $schema);
        $this->assertEquals('Offer', $schema['offers']['@type']);
        $this->assertEquals('29.99', $schema['offers']['price']);
        $this->assertEquals('USD', $schema['offers']['priceCurrency']);
    }

    public function test_build_with_brand()
    {
        $fields = array(
            'name' => 'Product',
            'brand' => 'Test Brand',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('brand', $schema);
        $this->assertEquals('Brand', $schema['brand']['@type']);
        $this->assertEquals('Test Brand', $schema['brand']['name']);
    }

    public function test_build_with_sku()
    {
        $fields = array(
            'name' => 'Product',
            'sku' => 'TEST-SKU-123',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('TEST-SKU-123', $schema['sku']);
    }

    public function test_build_with_price()
    {
        $fields = array(
            'name' => 'Product',
            'price' => '99.99',
            'priceCurrency' => 'USD',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('offers', $schema);
        $this->assertEquals('Offer', $schema['offers']['@type']);
        $this->assertEquals('99.99', $schema['offers']['price']);
        $this->assertEquals('USD', $schema['offers']['priceCurrency']);
    }
}
