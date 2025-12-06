<?php
/**
 * Job Posting Schema Test Cases
 *
 * @package Schema_Engine
 */

namespace SchemaEngine\Tests\Output\Types;

use PHPUnit\Framework\TestCase;


class JobPostingSchemaTest extends TestCase
{
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new \Schema_Job_Posting();
    }

    public function test_implements_schema_builder_interface()
    {
        $this->assertInstanceOf(\Schema_Builder_Interface::class, $this->schema);
    }

    public function test_build_basic_job_posting()
    {
        $fields = array(
            'title' => 'Software Engineer',
            'description' => 'We are hiring!',
            'datePosted' => '2024-01-01',
        );

        $schema = $this->schema->build($fields);

        $this->assertEquals('JobPosting', $schema['@type']);
        $this->assertEquals('Software Engineer', $schema['title']);
        $this->assertEquals('2024-01-01', $schema['datePosted']);
    }

    public function test_build_with_hiring_organization()
    {
        $fields = array(
            'title' => 'Developer',
            'hiringOrganizationName' => 'Acme Corp',
            'hiringOrganizationUrl' => 'https://acme.com',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('hiringOrganization', $schema);
        $this->assertEquals('Organization', $schema['hiringOrganization']['@type']);
        $this->assertEquals('Acme Corp', $schema['hiringOrganization']['name']);
    }

    public function test_build_with_salary()
    {
        $fields = array(
            'title' => 'Developer',
            'baseSalaryValue' => '100000',
            'baseSalaryCurrency' => 'USD',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('baseSalary', $schema);
        $this->assertEquals('MonetaryAmount', $schema['baseSalary']['@type']);
        $this->assertEquals('100000', $schema['baseSalary']['value']['value']);
        $this->assertEquals('USD', $schema['baseSalary']['currency']);
    }

    public function test_build_with_location()
    {
        $fields = array(
            'title' => 'Developer',
            'jobLocationCity' => 'New York',
            'jobLocationState' => 'NY',
            'jobLocationCountry' => 'US',
        );

        $schema = $this->schema->build($fields);

        $this->assertArrayHasKey('jobLocation', $schema);
        $this->assertEquals('Place', $schema['jobLocation']['@type']);
    }
    public function test_get_fields_structure()
    {
        $fields = $this->schema->get_fields();
        $this->assertIsArray($fields);

        $fieldNames = array_column($fields, 'name');
        $this->assertContains('title', $fieldNames);
        $this->assertContains('datePosted', $fieldNames);
        $this->assertContains('jobLocations', $fieldNames);
    }
}
