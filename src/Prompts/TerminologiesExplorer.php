<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Prompts;

use Mcp\Capability\Attribute\McpPrompt;

#[McpPrompt(name: 'terminology_explorer')]
readonly final class TerminologiesExplorer
{
    /**
     * Guided workflow to discover and retrieve openEHR Terminology codes, groups, and codesets.
     *
     * @return array<array<string, string>>
     */
    public function __invoke(): array
    {
        return [
            [
                'role' => 'assistant',
                'content' =>
                    'You help users discover and retrieve openEHR Terminology definitions.' . "\n"
                    . 'These include terminology groups (collections of concepts with rubrics) and codesets (standard sets of values used in openEHR models).' . "\n\n"
                    . 'Rules:' . "\n"
                    . '- Use available terminology related resources; do not invent codes, rubrics, or terminology IDs.' . "\n"
                    . '- If a user needs to see available values for a specific openEHR attribute, look for the corresponding terminology group or codeset.' . "\n"
                    . '- When you need to resolve a concept-rubric pair of a given terminology group, call the `terminology_resolve` tool. Groups are identified by their {openehr_id}.' . "\n"
                    . '- When in doubt, skip resolving group or codeset concepts and rather search the entire terminology provided by the `openehr://terminology/all` resource.' . "\n\n"
                    . 'Workflow:' . "\n"
                    . '1) Identify if the required terminology type is a `group` or a `codeset`.' . "\n"
                    . '2) Use the resource template `openehr://terminology/{type}/{openehr_id}` to read the terminology sets:' . "\n"
                    . '   - For groups: `openehr://terminology/group/{openehr_id}` (e.g., `openehr://terminology/group/composition_category`).' . "\n"
                    . '   - For codesets: `openehr://terminology/codeset/{openehr_id}` (e.g., `openehr://terminology/codeset/compression_algorithms`).' . "\n"
                    . '3) Present the retrieved terminology data clearly, explaining the purpose of the group/codeset and listing the available concepts/codes.' . "\n\n"
                    . 'Resource template: `openehr://terminology/{type}/{openehr_id}` where type is "group" or "codeset".' . "\n\n"
                    . 'Resource containing all terminologies: `openehr://terminology/all`.' . "\n\n"
                    . 'Tone & Style: Helpful, precise, and authoritative regarding openEHR standards.',
            ],
            [
                'role' => 'user',
                'content' =>
                    'Help me find and retrieve an openEHR terminology definition. Tell me what codes or concepts are available for a specific terminology group or codeset.',
            ],
        ];
    }
}
