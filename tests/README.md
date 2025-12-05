# Schema Engine - Testing Guide

Complete guide for running and writing tests for Schema Engine plugin.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Running Tests](#running-tests)
- [Test Structure](#test-structure)
- [Writing Tests](#writing-tests)
- [Test Coverage](#test-coverage)
- [Continuous Integration](#continuous-integration)

---

## Prerequisites

Before running tests, ensure you have:

- **PHP 7.4 or higher**
- **Composer** (dependency manager for PHP)

### Check PHP Version

```bash
php -v
```

### Install Composer

If you don't have Composer installed:

**macOS/Linux:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Windows:**
Download and run the installer from [getcomposer.org](https://getcomposer.org/download/)

---

## Installation

### 1. Navigate to Plugin Directory

```bash
cd /path/to/wp-content/plugins/schema-engine
```

### 2. Install Test Dependencies

```bash
composer install
```

This will install:
- PHPUnit 9.5
- Brain Monkey (WordPress function mocking)
- Mockery (mocking framework)

---

## Running Tests

### Run All Tests

```bash
composer test
```

Or directly with PHPUnit:

```bash
vendor/bin/phpunit
```

### Run Specific Test Suite

```bash
# Run only Schema Builder tests
vendor/bin/phpunit --testsuite="Schema Builders"

# Run only Schema Output tests
vendor/bin/phpunit --testsuite="Schema Output"
```

### Run Specific Test File

```bash
vendor/bin/phpunit tests/output/types/VideoSchemaTest.php
```

### Run Specific Test Method

```bash
vendor/bin/phpunit --filter test_build_with_all_fields
```

### Run Tests with Verbose Output

```bash
vendor/bin/phpunit --verbose
```

### Run Tests and Stop on First Failure

```bash
vendor/bin/phpunit --stop-on-failure
```

---

## Test Results

### Understanding Test Output

When you run tests, you'll see output like this:

```
PHPUnit 9.5.28 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.1.0
Configuration: /path/to/schema-engine/phpunit.xml

...................................................  49 / 49 (100%)

Time: 00:00.234, Memory: 10.00 MB

OK (49 tests, 187 assertions)
```

**Legend:**
- `.` = Test passed
- `F` = Test failed (assertion failure)
- `E` = Test error (unexpected exception)
- `S` = Test skipped
- `I` = Test incomplete

### Failed Test Example

If a test fails, you'll see:

```
F

1) SchemaEngine\Tests\Output\Types\VideoSchemaTest::test_build_with_all_fields
Failed asserting that two arrays are identical.
Expected: "VideoObject"
Actual: "Video"

/path/to/VideoSchemaTest.php:215
```

---

## Test Structure

```
tests/
├── bootstrap.php              # Test environment setup
├── README.md                  # This file
└── output/
    └── types/
        ├── VideoSchemaTest.php    # Video schema tests
        └── ArticleSchemaTest.php  # Article schema tests
```

### Test Organization

- **Unit Tests**: Tests for individual classes and methods
- **Integration Tests**: Tests for component interactions
- **Test Suites**: Organized by functionality (Schema Builders, Schema Output)

---

## Writing Tests

### Test Class Template

```php
<?php
namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;

class MySchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_MyType();
    }

    public function test_something()
    {
        $result = $this->schema->someMethod();
        $this->assertEquals('expected', $result);
    }
}
```

### Common Assertions

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual); // Strict comparison

// Types
$this->assertIsArray($var);
$this->assertIsString($var);
$this->assertInstanceOf(ClassName::class, $obj);

// Arrays
$this->assertArrayHasKey('key', $array);
$this->assertContains('value', $array);
$this->assertCount(5, $array);

// Boolean
$this->assertTrue($condition);
$this->assertFalse($condition);
$this->assertNull($var);
$this->assertNotNull($var);

// Exceptions
$this->expectException(ExceptionClass::class);
```

### Test Naming Convention

- Prefix test methods with `test_`
- Use descriptive names: `test_build_with_all_fields`
- Group related tests together

### What to Test

1. **Happy Path**: Test with valid input
2. **Edge Cases**: Empty values, null, boundary conditions
3. **Error Conditions**: Invalid input, missing required fields
4. **Default Behavior**: Test defaults when values not provided
5. **Data Types**: Ensure correct types returned
6. **Field Validation**: Required vs optional fields

---

## Test Coverage

### Generate Coverage Report

```bash
composer test:coverage
```

This creates an HTML coverage report in the `coverage/` directory.

### View Coverage Report

```bash
# macOS
open coverage/index.html

# Linux
xdg-open coverage/index.html

# Windows
start coverage/index.html
```

### Coverage Requirements

Aim for:
- **80%+ overall coverage**
- **100% coverage** for critical business logic
- **100% coverage** for public API methods

### Coverage in Terminal

For quick coverage overview:

```bash
vendor/bin/phpunit --coverage-text
```

---

## Continuous Integration

### GitHub Actions Example

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php: ['7.4', '8.0', '8.1', '8.2']

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          coverage: xdebug

      - name: Install dependencies
        run: composer install

      - name: Run tests
        run: composer test

      - name: Generate coverage
        run: composer test:coverage
```

---

## Troubleshooting

### Issue: "Class not found"

**Solution:** Ensure you've run `composer install` to install dependencies.

### Issue: "Cannot open file bootstrap.php"

**Solution:** Run tests from the plugin root directory:
```bash
cd /path/to/schema-engine
vendor/bin/phpunit
```

### Issue: "Out of memory"

**Solution:** Increase PHP memory limit:
```bash
php -d memory_limit=512M vendor/bin/phpunit
```

### Issue: Tests run but all fail

**Solution:** Check that WordPress functions are properly mocked in `tests/bootstrap.php`.

---

## Best Practices

1. **Run tests before committing** - Ensure all tests pass
2. **Write tests for new features** - Add tests when adding functionality
3. **Update tests when changing code** - Keep tests in sync with code
4. **Test edge cases** - Don't just test the happy path
5. **Keep tests independent** - Each test should be able to run alone
6. **Use descriptive names** - Make test failures easy to understand
7. **Don't test WordPress core** - Only test your plugin code

---

## Test Coverage by Component

### Video Schema Tests (21 tests)

- ✅ Interface implementation
- ✅ Schema structure validation
- ✅ Field configuration
- ✅ Build with minimal fields
- ✅ Build with default placeholders
- ✅ Build with all fields
- ✅ Optional field handling
- ✅ Pro feature placeholders
- ✅ Field uniqueness
- ✅ Field configuration structure

### Article Schema Tests (22 tests)

- ✅ Interface implementation
- ✅ Schema structure validation
- ✅ Article subtypes (5 types)
- ✅ Field configuration
- ✅ Build with minimal fields
- ✅ Build with default placeholders
- ✅ Author structure
- ✅ Publisher structure
- ✅ All article types
- ✅ Pro feature placeholders

---

## Quick Reference

| Command | Description |
|---------|-------------|
| `composer install` | Install test dependencies |
| `composer test` | Run all tests |
| `composer test:coverage` | Run tests with coverage report |
| `vendor/bin/phpunit --testsuite="Schema Builders"` | Run specific test suite |
| `vendor/bin/phpunit --filter test_name` | Run specific test |
| `vendor/bin/phpunit --stop-on-failure` | Stop on first failure |
| `vendor/bin/phpunit --verbose` | Verbose output |

---

## Need Help?

- **PHPUnit Documentation**: https://phpunit.de/documentation.html
- **Brain Monkey Documentation**: https://brain-wp.github.io/BrainMonkey/
- **Mockery Documentation**: http://docs.mockery.io/

---

**Happy Testing! 🚀**
