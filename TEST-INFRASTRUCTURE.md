# Test Infrastructure Documentation

## Overview
Comprehensive PHPUnit testing infrastructure with React-based dashboard for automated testing and visualization of Schema Engine functionality.

## Components

### 1. PHPUnit Test Files
Location: `tests/output/types/`

**Created Test Files:**
- `OrganizationSchemaTest.php` - 15 test methods covering Organization schema
- `PersonSchemaTest.php` - 4 test methods for Person schema
- `LocalBusinessSchemaTest.php` - 4 test methods for LocalBusiness
- `ProductSchemaTest.php` - 5 test methods for Product schema
- `FAQSchemaTest.php` - 2 test methods for FAQ schema
- `ReviewSchemaTest.php` - 3 test methods for Review schema
- `JobPostingSchemaTest.php` - 4 test methods for JobPosting schema

**Test Coverage:**
- Schema builder interface implementation
- Required and optional field handling
- Data validation and filtering
- Template variable support ({meta:*}, {option:*})
- Social profile filtering (allows variables, removes invalid URLs)
- Subtype variations
- Empty value handling
- Address and contact information structures
- Ratings, offers, and nested objects

### 2. REST API Endpoints
Location: `includes/admin/class-test-api.php`

**Endpoints:**

#### POST /wp-json/schema-engine/v1/tests/run
Execute PHPUnit tests with optional filter.

**Request Body:**
```json
{
  "filter": "Organization" // Optional: filter tests by name
}
```

**Response:**
```json
{
  "success": true,
  "tests": 15,
  "assertions": 47,
  "failures": 0,
  "errors": 0,
  "time": 1.25,
  "pass_rate": 100,
  "raw_output": "PHPUnit 9.x output..."
}
```

#### GET /wp-json/schema-engine/v1/tests/list
Get list of available test files.

**Response:**
```json
{
  "success": true,
  "tests": [
    "OrganizationSchemaTest",
    "PersonSchemaTest",
    // ...
  ]
}
```

#### GET /wp-json/schema-engine/v1/tests/stats
Get test execution statistics from history.

**Response:**
```json
{
  "success": true,
  "stats": {
    "total_runs": 42,
    "success_rate": 95.2,
    "average_time": 1.35,
    "test_count": 7
  }
}
```

**Features:**
- Executes PHPUnit via `exec()` with phpunit.xml config
- Parses test output for statistics (tests, assertions, failures, time)
- Stores last 50 test runs in `wp_option` 'schema_engine_test_history'
- Validates permissions (manage_options capability required)
- Error handling for missing PHPUnit or invalid configurations

### 3. React Test Dashboard
Location: `src/test-dashboard/`

**Files:**
- `index.js` - Main dashboard component
- `style.scss` - Dashboard styles

**Features:**
- **Stats Cards Grid** - Visual overview of test metrics:
  - Total test runs executed
  - Success rate percentage
  - Average execution time
  - Number of test files
  
- **Test Controls:**
  - Filter input for test selection
  - Run button with loading state
  - Real-time execution feedback
  
- **Results Display:**
  - Summary statistics (tests, assertions, failures, errors, time, pass rate)
  - Individual test items with color-coded status
  - Collapsible raw output viewer
  
- **Test Suites List:**
  - Grid display of available test files
  - Quick-run buttons for individual suites
  - Visual organization of test categories

**Built Assets:**
- `build/test-dashboard/index.js` - Minified React app (7.95 KB)
- `build/test-dashboard/style-index.css` - Compiled styles (5.44 KB)
- `build/test-dashboard/index.asset.php` - WordPress asset dependencies

### 4. Admin Page Integration
Location: `includes/admin/class-test-dashboard.php`

**Menu Location:** Schema Engine → Test Dashboard

**Visibility:**
The dashboard is only visible when:
- `WP_DEBUG` is enabled in wp-config.php, OR
- Option `schema_engine_enable_test_dashboard` is set to true

**To enable in wp-config.php:**
```php
define( 'WP_DEBUG', true );
```

**Or via code/plugin:**
```php
update_option( 'schema_engine_enable_test_dashboard', true );
```

**Asset Enqueuing:**
- Automatically loads test-dashboard JS/CSS on the dashboard page
- Includes WordPress dependencies (@wordpress/api-fetch, @wordpress/components)
- Renders React mount point: `<div id="schema-engine-test-dashboard"></div>`

## Running Tests

### Via Command Line
```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/output/types/OrganizationSchemaTest.php

# Run with filter
vendor/bin/phpunit --filter Organization

# Run with code coverage
vendor/bin/phpunit --coverage-html coverage/
```

### Via Dashboard UI
1. Navigate to: **Schema Engine → Test Dashboard** (requires WP_DEBUG or enabled option)
2. Click "Run All Tests" or use quick-run buttons for specific suites
3. View real-time results with statistics
4. Expand raw output for detailed PHPUnit output

### Via REST API
```bash
# Run all tests
curl -X POST "http://skynet.local/wp-json/schema-engine/v1/tests/run" \
  -H "Content-Type: application/json" \
  -d '{"filter":""}'

# Run filtered tests
curl -X POST "http://skynet.local/wp-json/schema-engine/v1/tests/run" \
  -H "Content-Type: application/json" \
  -d '{"filter":"Organization"}'

# Get test list
curl "http://skynet.local/wp-json/schema-engine/v1/tests/list"

# Get statistics
curl "http://skynet.local/wp-json/schema-engine/v1/tests/stats"
```

## Test History Tracking

Test execution history is stored in `wp_options` table:
- Option name: `schema_engine_test_history`
- Storage limit: Last 50 test runs
- Data includes: timestamp, tests count, assertions, failures, errors, time, pass_rate

**Access via code:**
```php
$history = get_option( 'schema_engine_test_history', array() );
foreach ( $history as $run ) {
    echo "Run at {$run['timestamp']}: {$run['tests']} tests, {$run['pass_rate']}% pass rate\n";
}
```

## Adding New Tests

### 1. Create Test File
Location: `tests/output/types/YourSchemaTest.php`

```php
<?php
namespace Schema_Engine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;
use Schema_Engine\Output\Types\Your_Schema;

class YourSchemaTest extends TestCase {
    private $schema;
    
    protected function setUp(): void {
        $this->schema = new Your_Schema();
    }
    
    public function test_implements_interface() {
        $this->assertInstanceOf(
            'Schema_Engine\Output\Schema_Builder_Interface',
            $this->schema
        );
    }
    
    public function test_basic_build() {
        $data = array(
            'name' => 'Test Name',
            'description' => 'Test Description'
        );
        
        $result = $this->schema->build( $data );
        
        $this->assertEquals( 'https://schema.org', $result['@context'] );
        $this->assertEquals( 'YourType', $result['@type'] );
        $this->assertEquals( 'Test Name', $result['name'] );
    }
}
```

### 2. Register in phpunit.xml
The test will be automatically discovered if placed in `tests/output/types/` directory.

### 3. Build Assets
```bash
yarn bundle
```

### 4. Run Tests
Use dashboard UI, REST API, or command line as documented above.

## Pending Tests

### Pro Schema Types (To Be Created)
- `RecipeSchemaTest.php` - Recipe schema with ingredients, instructions
- `EventSchemaTest.php` - Event schema with dates, location, performers
- `HowToSchemaTest.php` - HowTo schema with steps, tools, materials
- `PodcastSchemaTest.php` - Podcast schema with episodes, series
- `CustomSchemaTest.php` - Custom schema type validation
- `WebsiteSchemaTest.php` - Website schema with sitelinks

### Settings & API Tests (To Be Created)
- `VariableReplacerTest.php` - Variable replacement system tests
- `ConditionsTest.php` - Template condition matching tests
- `RestApiTest.php` - REST API endpoint tests
- `SettingsTest.php` - Settings sanitization and validation

## CI/CD Integration

### GitHub Actions Example
```yaml
name: PHPUnit Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '7.4'
          
      - name: Install Composer dependencies
        run: composer install
        
      - name: Run tests
        run: vendor/bin/phpunit
        
      - name: Upload coverage
        uses: codecov/codecov-action@v2
        with:
          files: ./coverage/clover.xml
```

## Troubleshooting

### Dashboard Not Visible
- Verify WP_DEBUG is enabled in wp-config.php
- OR set option: `update_option( 'schema_engine_enable_test_dashboard', true );`
- Check admin capabilities (manage_options required)

### Tests Not Running
- Verify PHPUnit is installed: `composer require --dev phpunit/phpunit ^9.0`
- Check phpunit.xml configuration exists
- Verify test file location: `tests/output/types/*.php`
- Check PHP version compatibility (7.4+)

### REST API Errors
- Verify WordPress REST API is enabled
- Check user permissions (must have manage_options capability)
- Ensure test files exist in correct directory
- Check PHP exec() function is not disabled

### Build Errors
- Run `yarn install` to ensure dependencies are installed
- Clear build cache: `rm -rf build/test-dashboard/`
- Rebuild: `yarn bundle`
- Check webpack.config.js includes test-dashboard entry

## Performance Considerations

- Test execution via exec() is synchronous - large test suites may take time
- Dashboard loads all test history (50 runs) - consider pagination for large datasets
- Raw output display can be large - collapsed by default for performance
- Consider running heavy tests asynchronously or in background jobs

## Security

- REST API endpoints require `manage_options` capability
- Dashboard only accessible to administrators
- Test dashboard hidden in production (WP_DEBUG false by default)
- exec() commands are sanitized and validated
- No user input directly passed to shell commands

## Future Enhancements

- [ ] Add chart library (Chart.js/Recharts) for visual graphs
- [ ] Implement test coverage reporting
- [ ] Add test run scheduling (cron jobs)
- [ ] Email notifications for test failures
- [ ] Test result history pagination
- [ ] Export test results (CSV, JSON)
- [ ] Performance benchmarking
- [ ] Integration with CI/CD platforms
- [ ] Test snapshots for regression testing
- [ ] Parallel test execution
