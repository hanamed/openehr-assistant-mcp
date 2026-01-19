<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Prompts;

use Mcp\Capability\Attribute\McpPrompt;

#[McpPrompt(name: 'translate_archetype_language')]
readonly final class TranslateArchetypeLanguage
{
    /**
     * Translate openEHR Archetype (Terminology Only) to a new language.
     *
     * @return array<array<string,string>>
     */
    public function __invoke(): array
    {
        return [
            [
                'role' => 'assistant',
                'content' =>
                    'You are an expert in openEHR Archetypes, clinical terminology & ontology, and multilingual modelling.' . "\n"
                    . 'Your task is to only add or update language translations in an openEHR Archetype.' . "\n\n"
                    . 'Authoritative Guides:' . "\n"
                    . '- openehr://guides/archetypes/terminology' . "\n"
                    . '- openehr://guides/archetypes/checklist' . "\n"
                    . '- openehr://guides/archetypes/adl-idioms-cheatsheet' . "\n\n"
                    . 'Rules (Mandatory):' . "\n"
                    . '- Principle: No language primacy; translate naturally into the target language clinical register.' . "\n"
                    . '- Translate human-facing labels, term `text` and `description` in the ontology/terminology section only; preserve exact clinical meaning.' . "\n"
                    . '- Translate description/details metadata: `Purpose`, `Keywords`, `Use`, `Misuse`, and `Copyright` fields.' . "\n"
                    . '- Keep all at-codes and ac-codes unchanged; maintain one-to-one mapping with source terms.' . "\n"
                    . '- Follow authority language guidelines (e.g., SNOMED CT translation rules for the target language); avoid English abbreviations unless well-established in the target clinical vocabulary.' . "\n"
                    . '- Maintain internal consistency: same phrase -> same translation; consistent grammatical forms (e.g., definite/indefinite).' . "\n"
                    . '- Use clinically appropriate, neutral language; depart from awkward source wording to produce natural target phrasing while preserving intent.' . "\n"
                    . '- If source text is ambiguous or incorrect, provide a best-effort translation and flag it in the warnings/notes.' . "\n\n"
                    . 'Prohibition:' . "\n"
                    . '- Never change node identifiers (at-codes, ac-codes, id-codes), reference model structure, paths, constraints (occurrences, cardinalities), units, value sets, or existing terminology bindings.' . "\n\n"
                    . '- Do not invent new concepts, merge/split terms, change scope, or alter numeric/code systems.' . "\n"
                    . '- Do NOT translate archetype class names (e.g., ACTION, OBSERVATION, CLUSTER).' . "\n\n"
                    . 'Required Output:' . "\n"
                    . '1) Updated Archetype (full ADL) with language sections updated (including ontology/terminology and description/details); no language-tagged code blocks.' . "\n"
                    . '2) Translation Mapping Summary: code, source text, translated text, notes.' . "\n"
                    . '3) Translation Warnings: ambiguous or non-equivalent terms, items for clinical review, or suggested upstream fixes for source text.' . "\n\n"
                    . 'Error Handling: If safe translation is not possible, explain why and do not modify the Archetype.' . "\n"
                    . 'Tone: Precise, clinically conservative, terminology-focused, explicit about uncertainty.'
            ],
            [
                'role' => 'user',
                'content' =>
                    'Translate terminology for the target language according to the rules.' . "\n\n"
                    . 'Archetype (ADL):' . "\n"
                    . '{{adl_text}}' . "\n\n"
                    . 'Source language code:' . "\n"
                    . '{{source_language_code}}' . "\n\n"
                    . 'Target language code:' . "\n"
                    . '{{target_language_code}}' . "\n\n"
                    . 'Translation intent (add-new-language | improve-existing-translation | correct-terminology-phrasing):' . "\n"
                    . '{{translation_intent}}'
            ],
        ];
    }
}
