<?php
declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Tests\Resources;

use Cadasto\OpenEHR\MCP\Assistant\Resources\Terminologies;
use Mcp\Exception\ResourceReadException;
use Mcp\Server;
use Mcp\Server\Builder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Terminologies::class)]
final class TerminologiesTest extends TestCase
{
    private Terminologies $terminologies;

    protected function setUp(): void
    {
        $this->terminologies = new Terminologies();
    }

    public function test_read_group(): void
    {
        $result = $this->terminologies->read('group', 'attestation_reason');

        $this->assertIsArray($result);
        $this->assertEquals('attestation_reason', $result['openehr_id']);
        $this->assertEquals('attestation reason', $result['name']);
        $this->assertIsArray($result['group']);
        $this->assertCount(2, $result['group']);
        // The structure is array of arrays like ['240' => 'signed']
        $this->assertEquals('signed', $result['group']['240']);
    }

    public function test_read_codeset(): void
    {
        $result = $this->terminologies->read('codeset', 'compression_algorithms');

        $this->assertIsArray($result);
        $this->assertEquals('compression_algorithms', $result['openehr_id']);
        $this->assertEquals('compression algorithms', $result['name']);
        $this->assertIsArray($result['codeset']);
        $this->assertGreaterThan(0, count($result['codeset']));
        $this->assertEquals('compress', $result['codeset'][0]);
    }

    public function test_read_invalid_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->terminologies->read('invalid', 'id');
    }

    public function test_read_not_found(): void
    {
        $this->expectException(ResourceReadException::class);
        $this->terminologies->read('group', 'non_existent_id');
    }

    public function test_read_all(): void
    {
        $result = $this->terminologies->readAll();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('codesets', $result);
        $this->assertArrayHasKey('groups', $result);

        // Check if codeset is an array of codesets
        $this->assertIsArray($result['codesets']);
        $this->assertGreaterThan(1, count($result['codesets']));

        // Check first codeset
        $firstCodeset = $result['codesets'][0];
        $this->assertEquals('compression_algorithms', $firstCodeset['openehr_id']);
        $this->assertArrayHasKey('codeset', $firstCodeset);

        // Check if group is an array of groups
        $this->assertIsArray($result['groups']);
        $this->assertGreaterThan(1, count($result['groups']));

        // Check first group
        $firstGroup = $result['groups'][0];
        $this->assertEquals('attestation_reason', $firstGroup['openehr_id']);
        $this->assertArrayHasKey('group', $firstGroup);
    }

    public function test_add_resources(): void
    {
        $builder = Server::builder();
        
        Terminologies::addResources($builder);
        $this->assertTrue(true);
    }
}
