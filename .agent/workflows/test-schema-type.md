---
description: How to implement and verify tests for Schema Engine types (Free & Pro)
---

This workflow outlines the steps to add or update test coverage for Schema Engine schema types, ensuring both the JSON-LD output and the Admin UI field definitions are verified.

1. **Identify the Schema Class**
   - Locate the schema class file in `schema-engine/includes/output/types/` (Free) or `schema-engine-pro/includes/output/types/` (Pro).
   - Note the class name (e.g., `Schema_Recipe`) and the expected `@type` (e.g., `Recipe`).

2. **Create or Open Test File**
   - Go to `schema-engine-test-cases/tests/output/types/`.
   - Create/Open `[Type]SchemaTest.php` (e.g., `RecipeSchemaTest.php`).

3. **Setup Test Class**
   - If testing a **Pro** schema, you must manually require the file if it's not autoloaded:
     ```php
     $pro_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/schema-engine-pro/includes/output/types/class-recipe-schema.php';
     if (file_exists($pro_path)) {
         require_once $pro_path;
     }
     ```
   - In `setUp()`, instantiate the class:
     ```php
     protected function setUp(): void {
         parent::setUp();
         if (class_exists('\\Schema_Recipe')) {
             $this->schema = new \Schema_Recipe();
         } else {
             $this->markTestSkipped('Class not found');
         }
     }
     ```

4. **Implement Standard Tests**
   - **Structure**: Verify the schema type.
     ```php
     public function test_get_schema_structure() {
         $structure = $this->schema->get_schema_structure();
         $this->assertEquals('Recipe', $structure['@type']);
     }
     ```
   - **UI Fields**: Verify the Admin UI fields exist.
     ```php
     public function test_get_fields_structure() {
         $fields = $this->schema->get_fields();
         $this->assertIsArray($fields);
         $fieldNames = array_column($fields, 'name');
         $this->assertContains('headline', $fieldNames); // Check for key fields
     }
     ```
   - **Build**: Verify JSON-LD output generation.

5. **Verify Tests**
   - Run the specific test file:
     ```bash
     vendor/bin/phpunit tests/output/types/[Type]SchemaTest.php
     ```
   - Check for failures regarding:
     - Missing classes (check paths).
     - Field name mismatches (check `get_fields` in source).
