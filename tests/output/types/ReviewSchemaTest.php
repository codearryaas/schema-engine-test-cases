<?php
/**
 * Review Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class ReviewSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_Review();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_build_basic_review()
    {
        $fields = array(
            'itemName' => 'Test Product',
            'reviewBody' => 'Great product!',
            'ratingValue' => '5',
            'authorName' => 'John Doe',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('Review', $schema['@type']);
        $this->assertEquals('Great product!', $schema['reviewBody']);
    }

    public function test_build_with_rating()
    {
        $fields = array(
            'itemName' => 'Product',
            'ratingValue' => '4.5',
            'bestRating' => '5',
            'worstRating' => '1',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('reviewRating', $schema);
        $this->assertEquals('Rating', $schema['reviewRating']['@type']);
        $this->assertEquals('4.5', $schema['reviewRating']['ratingValue']);
        $this->assertEquals('5', $schema['reviewRating']['bestRating']);
    }

    public function test_build_with_item_reviewed()
    {
        $fields = array(
            'itemReviewedType' => 'Product',
            'itemReviewedName' => 'Test Product',
            'reviewBody' => 'Good',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('itemReviewed', $schema);
        $this->assertEquals('Product', $schema['itemReviewed']['@type']);
        $this->assertEquals('Test Product', $schema['itemReviewed']['name']);
    }
}
