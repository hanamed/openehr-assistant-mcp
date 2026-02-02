<?php
declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Tests\Prompts;

use Cadasto\OpenEHR\MCP\Assistant\Prompts\TerminologyExplorer;
use Mcp\Schema\Enum\Role;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TerminologyExplorer::class)]
final class TerminologyExplorerTest extends TestCase
{
    public function test_invoke_returns_expected_structure(): void
    {
        $prompt = new TerminologyExplorer();
        $messages = $prompt();

        $this->assertIsArray($messages);
        $this->assertCount(2, $messages);

        $this->assertEquals(Role::Assistant, $messages[0]->role);
        $this->assertStringContainsString('openEHR Terminology definitions', $messages[0]->content->text);
        $this->assertStringContainsString('openehr://terminology', $messages[0]->content->text);

        $this->assertEquals(Role::User, $messages[1]->role);
        $this->assertStringContainsString('Help me find and retrieve an openEHR Terminology definition', $messages[1]->content->text);
    }
}
