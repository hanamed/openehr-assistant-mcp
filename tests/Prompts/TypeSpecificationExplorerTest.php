<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Tests\Prompts;

use Cadasto\OpenEHR\MCP\Assistant\Prompts\TypeSpecificationExplorer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TypeSpecificationExplorer::class)]
final class TypeSpecificationExplorerTest extends TestCase
{
    public function testPromptReturnsWellFormedMessagesAndReferencesTools(): void
    {
        $prompt = new TypeSpecificationExplorer();
        $messages = $prompt->__invoke();

        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);

        $allowedRoles = ['system','user','assistant'];
        $combinedContent = '';
        foreach ($messages as $msg) {
            $this->assertIsArray($msg);
            $this->assertArrayHasKey('role', $msg);
            $this->assertArrayHasKey('content', $msg);
            $this->assertContains($msg['role'], $allowedRoles);
            $this->assertIsString($msg['content']);
            $this->assertNotSame('', trim($msg['content']));
            $combinedContent .= "\n" . $msg['content'];
        }

        $this->assertStringContainsString('type_specification_search', $combinedContent);
        $this->assertStringContainsString('type_specification_get', $combinedContent);
        $this->assertStringContainsString('openehr://spec/type', $combinedContent);
    }
}
