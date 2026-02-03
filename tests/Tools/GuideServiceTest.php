<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Tests\Tools;

use Cadasto\OpenEHR\MCP\Assistant\Tools\GuideService;
use Mcp\Schema\Content\EmbeddedResource;
use Mcp\Schema\Content\TextResourceContents;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

#[CoversClass(GuideService::class)]
final class GuideServiceTest extends TestCase
{
    private GuideService $service;

    protected function setUp(): void
    {
        $this->service = new GuideService(new NullLogger());
    }

    public function test_guideSearch_returns_matches(): void
    {
        $results = $this->service->search('cardinality');

        $this->assertIsArray($results);
        $this->assertArrayHasKey('items', $results);
        $this->assertNotEmpty($results['items']);
        $first = $results['items'][0];
        $this->assertArrayHasKey('resourceUri', $first);
        $this->assertStringStartsWith('openehr://guides/', $first['resourceUri']);
    }

    public function test_guideSearch_respects_category_filter(): void
    {
        $results = $this->service->search('template', 'templates');

        $this->assertIsArray($results);
        $this->assertArrayHasKey('items', $results);
        foreach ($results['items'] as $item) {
            $this->assertSame('templates', $item['category']);
        }
    }

    public function test_guideGet_by_uri(): void
    {
        $resourceUri = 'openehr://guides/archetypes/adl-idioms-cheatsheet';
        $payload = $this->service->get($resourceUri);

        $this->assertInstanceOf(EmbeddedResource::class, $payload);
        $this->assertSame('resource', $payload->type);
        $this->assertInstanceOf(TextResourceContents::class, $payload->resource);
        $this->assertSame($resourceUri, $payload->resource->uri);
        $this->assertSame('text/markdown', $payload->resource->mimeType);
        $this->assertNotEmpty($payload->resource->text);
        $this->assertStringContainsString('idioms', $payload->resource->text);
    }

    public function test_guideGet_by_title(): void
    {
        $payload = $this->service->get('', 'archetypes', 'adl-idioms-cheatsheet');

        $this->assertInstanceOf(EmbeddedResource::class, $payload);
        $this->assertSame('resource', $payload->type);
        $this->assertInstanceOf(TextResourceContents::class, $payload->resource);
        $this->assertSame('openehr://guides/archetypes/adl-idioms-cheatsheet', $payload->resource->uri);
        $this->assertSame('text/markdown', $payload->resource->mimeType);
        $this->assertNotEmpty($payload->resource->text);
        $this->assertStringContainsString('idioms', $payload->resource->text);
    }

    public function test_adlIdiomLookup_returns_matches(): void
    {
        $results = $this->service->adlIdiomLookup('cardinality');

        $this->assertIsArray($results);
        $this->assertArrayHasKey('items', $results);
        $this->assertNotEmpty($results['items']);
        $this->assertArrayHasKey('resourceUri', $results['items'][0]);
    }
}
