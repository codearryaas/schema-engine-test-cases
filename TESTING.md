# Testing Quick Reference

Quick guide to run tests for Schema Engine plugin.

## Installation

```bash
composer install
```

## Run Tests

```bash
# Run all tests
composer test

# Or use PHPUnit directly
vendor/bin/phpunit

# With detailed output
vendor/bin/phpunit --testdox

# Run specific test file
vendor/bin/phpunit tests/output/types/VideoSchemaTest.php

# Run specific test method
vendor/bin/phpunit --filter test_build_with_all_fields
```

## Test Results

**✅ All Tests Passing:**
- **39 tests** with **226 assertions**
- Video Schema: 17 tests
- Article Schema: 22 tests

## Test Coverage

### Video Schema Tests
- ✔ Interface implementation
- ✔ Schema structure validation
- ✔ Field configurations (required & optional)
- ✔ Build with minimal fields
- ✔ Build with all fields
- ✔ Default placeholders
- ✔ Pro feature placeholders
- ✔ Field validation

### Article Schema Tests
- ✔ Interface implementation
- ✔ Schema structure validation
- ✔ Article subtypes (5 types)
- ✔ Build with minimal fields
- ✔ Build with all fields
- ✔ Author structure
- ✔ Publisher structure
- ✔ Pro feature placeholders
- ✔ Field validation

## Files Excluded from Release

The following test-related files are automatically excluded from the release ZIP:

- `tests/**` - All test files
- `phpunit.xml` - PHPUnit configuration
- `composer.json` - Composer configuration
- `composer.lock` - Composer lock file
- `vendor/**` - Test dependencies (via .gitignore)
- `coverage/**` - Coverage reports (via .gitignore)

See `Gruntfile.js` line 44 for complete exclusion list.

## Full Documentation

For complete testing documentation, see: [tests/README.md](tests/README.md)

## Troubleshooting

### Tests not running?

1. Make sure you installed dependencies: `composer install`
2. Run from plugin root directory
3. Check PHP version: `php -v` (requires 7.4+)

### Need help?

See [tests/README.md](tests/README.md) for detailed documentation.
