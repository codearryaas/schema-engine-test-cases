<?php
/**
 * Recipe Schema Test Cases
 *
 * @package Schema_Engine_Pro
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;

// Load Pro Recipe Schema if available
$pro_recipe_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/schema-engine-pro/includes/output/types/class-recipe-schema.php';
if (file_exists($pro_recipe_path)) {
    require_once $pro_recipe_path;
}

class RecipeSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists('\Schema_Recipe')) {
            $this->schema = new \Schema_Recipe();
        } else {
            $this->markTestSkipped('Schema_Recipe class not found');
        }
    }

    public function test_get_schema_structure()
    {
        $structure = $this->schema->get_schema_structure();
        $this->assertEquals('Recipe', $structure['@type']);
    }

    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);

        $fieldNames = array_column($fields, 'name');
        $this->assertContains('headline', $fieldNames);
        $this->assertContains('cookTime', $fieldNames);
        $this->assertContains('recipeCategory', $fieldNames);
    }
}
