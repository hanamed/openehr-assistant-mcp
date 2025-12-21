<?php

namespace Cadasto\OpenEHR\MCP\Assistant\CompletionProviders;

use Mcp\Capability\Completion\ProviderInterface;

class ArchetypeGuidelines implements ProviderInterface
{

    public function getCompletions(string $currentValue): array
    {
        $directory = APP_RESOURCES_DIR . '/guidelines/archetypes/v1';
        $files = scandir($directory);
        $completions = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (str_ends_with($file, '.md')) {
                $filename = substr($file, 0, -3);
                if (!$currentValue || str_starts_with($filename, $currentValue)) {
                    $completions[] = $filename;
                }
            }
        }

        return $completions;
    }
}