# Schema Engine Test Cases

Automated testing plugin for Schema Engine (free and pro versions).

## Purpose

This standalone WordPress plugin provides comprehensive automated testing infrastructure for the Schema Engine plugin ecosystem. It includes:

- **126 PHPUnit Tests**: Validates schema type generation, template conditions, and settings
- **React Test Dashboard**: Beautiful UI for running tests and viewing results  
- **REST API**: Endpoints for test execution and management
- **Multi-Plugin Support**: Tests both Schema Engine free and pro versions

## Features

### Test Coverage

- ✅ **Schema Type Tests (79 tests)**: Organization, Person, LocalBusiness, Product, FAQ, Review, JobPosting, Article, Video
- ✅ **Template Conditions (23 tests)**: Post types, taxonomies, singular posts, grouped conditions, AND/OR logic
- ✅ **Settings & Admin (26 tests)**: REST API sanitization, XSS filtering, boolean conversion

### Test Dashboard

Access from WordPress admin menu: **Test Dashboard** (icon: 📊)

**Features**:
- Category-based test grouping (📋 Schema Types, 🎯 Template Conditions, ⚙️ Settings & Admin)
- Real-time test execution
- Statistics cards (total tests, passing, failing, duration)
- Test history tracking (last 50 runs)
- Raw PHPUnit output viewer
- Expandable test result details

### REST API Endpoints

- `POST /wp-json/schema-engine/v1/tests/run` - Execute PHPUnit tests
- `GET /wp-json/schema-engine/v1/tests/list` - List available tests
- `GET /wp-json/schema-engine/v1/tests/stats` - Get test statistics

## Installation

### Requirements

- WordPress 5.9+
- PHP 7.4+
- Schema Engine plugin (free or pro) installed
- Composer (for PHPUnit)
- Node.js & npm (for building React assets)

### Setup

1. **Clone/Copy** plugin to `wp-content/plugins/schema-engine-test-cases/`

2. **Install Dependencies**:
   ```bash
   cd wp-content/plugins/schema-engine-test-cases
   
   # Install Composer dependencies
   composer install
   
   # Install npm dependencies (if modifying React code)
   npm install
   
   # Build React assets (if modifying React code)
   npm run build
   ```

3. **Activate Plugin**: Go to WordPress admin → Plugins → Activate "Schema Engine Test Cases"

4. **Access Dashboard**: WordPress admin menu → Test Dashboard

## Usage

### Running Tests via Dashboard

1. Navigate to **Test Dashboard** in WordPress admin
2. Click **"Run All Tests"** button
3. View results by category
4. Expand individual tests to see details
5. Check raw PHPUnit output if needed

### Running Tests via CLI

```bash
cd wp-content/plugins/schema-engine-test-cases

# Run all tests
composer test

# Run specific test file
./vendor/bin/phpunit tests/includes/ConditionsTest.php

# Run with coverage
composer test:coverage
```

### Running Tests via REST API

```bash
# Run tests
curl -X POST http://your-site.local/wp-json/schema-engine/v1/tests/run \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# List tests
curl http://your-site.local/wp-json/schema-engine/v1/tests/list

# Get stats
curl http://your-site.local/wp-json/schema-engine/v1/tests/stats
```

## Plugin Detection

The plugin automatically detects Schema Engine installations:

- **Free Version**: `wp-content/plugins/schema-engine/`
- **Pro Version**: `wp-content/plugins/schema-engine-pro/`

Tests are categorized by plugin:
- Free tests: "Schema Types (📋)"
- Pro tests: "Pro Schema Types (🔷)"

## Development

### Building React Assets

```bash
# Development mode (watch)
npm start

# Production build
npm run build
```

### Adding New Tests

1. Create test file in appropriate directory:
   - Schema types: `tests/output/types/`
   - Conditions: `tests/includes/`
   - Settings: `tests/admin/`

2. Follow PHPUnit conventions:
   ```php
   <?php
   namespace SchemaEngineTestCases\Tests;
   
   use PHPUnit\Framework\TestCase;
   
   class YourTest extends TestCase {
       public function test_something() {
           $this->assertTrue(true);
       }
   }
   ```

3. Run tests to verify:
   ```bash
   composer test
   ```

### Modifying Dashboard UI

1. Edit files in `src/test-dashboard/`
2. Build assets: `npm run build`
3. Refresh WordPress admin to see changes

## File Structure

```
schema-engine-test-cases/
├── schema-engine-test-cases.php    # Main plugin file
├── includes/
│   ├── class-test-api.php          # REST API implementation
│   └── class-test-dashboard.php    # Dashboard page
├── tests/
│   ├── bootstrap.php               # PHPUnit bootstrap
│   ├── includes/                   # Core tests
│   ├── admin/                      # Settings tests
│   └── output/types/               # Schema type tests
├── src/test-dashboard/             # React source
│   ├── index.js                    # Main component
│   ├── TestDashboard.js            # Dashboard UI
│   └── style.scss                  # Styles
├── build/test-dashboard/           # Compiled assets
├── composer.json                   # PHP dependencies
├── package.json                    # npm dependencies
├── phpunit.xml                     # PHPUnit config
└── webpack.config.js               # Build config
```

## Troubleshooting

### Plugin Activation Error

**Error**: "Schema Engine Test Cases requires Schema Engine to be installed and activated"

**Solution**: Install and activate Schema Engine plugin first

### Tests Not Running

**Issue**: Dashboard shows "No tests found"

**Solutions**:
1. Ensure Composer dependencies installed: `composer install`
2. Verify Schema Engine plugin path exists
3. Check PHP CLI available: `which php`

### Build Errors

**Issue**: React assets not building

**Solutions**:
1. Delete `node_modules` and `package-lock.json`
2. Run `npm install` again
3. Ensure Node.js 14+ installed: `node -v`

### PHPUnit Not Found

**Issue**: "phpunit: command not found"

**Solution**: Use Composer path:
```bash
./vendor/bin/phpunit
```

## Contributing

When adding tests:
1. Write descriptive test method names
2. Use data providers for multiple scenarios
3. Add comments for complex assertions
4. Follow PSR-12 coding standards
5. Update this README if adding new features

## License

GPL-2.0-or-later (same as WordPress)

## Credits

Developed by Rakesh Lawaju for Schema Engine plugin ecosystem.
