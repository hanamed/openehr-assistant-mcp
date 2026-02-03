<?php
declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Tests\Prompts;

use Cadasto\OpenEHR\MCP\Assistant\Prompts\GuideExplorer;
use Mcp\Schema\Enum\Role;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GuideExplorer::class)]
final class GuideExplorerTest extends TestCase
{
    public function test_invoke_returns_expected_structure(): void
    {
        $prompt = new GuideExplorer();
        $messages = $prompt();

        $this->assertIsArray($messages);
        $this->assertCount(2, $messages);

        $this->assertEquals(Role::Assistant, $messages[0]->role);
        $this->assertStringContainsString('openEHR implementation guides', $messages[0]->content->text);
        $this->assertStringContainsString('guide_search', $messages[0]->content->text);

        $this->assertEquals(Role::User, $messages[1]->role);
        $this->assertStringContainsString('Help me find and retrieve openEHR implementation guidance', $messages[1]->content->text);
    }
}
