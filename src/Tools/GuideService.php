<?php

declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Tools;

use Mcp\Capability\Attribute\McpTool;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\Content\EmbeddedResource;
use Mcp\Schema\Content\TextResourceContents;
use Mcp\Schema\ToolAnnotations;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final readonly class GuideService
{
    private const int DEFAULT_MAX_RESULTS = 15;
    private const int DEFAULT_SECTION_LIMIT = 5;
    private const int SNIPPET_CHARS = 350;

    public const string GUIDE_DIR = APP_RESOURCES_DIR . '/guides';

    public function __construct(
        private LoggerInterface $logger,
    )
    {
        if (!is_dir(self::GUIDE_DIR) || !is_readable(self::GUIDE_DIR)) {
            $this->logger->warning('Guides directory not found or not readable.', ['dir' => self::GUIDE_DIR]);
        }
    }

    /**
     * Search openEHR guides metadata and content to retrieve small, model-ready snippets plus canonical openehr://guides URIs.
     *
     * Use this tool when you need to locate the right guidance on demand.
     * It returns short, task-relevant chunks and meta-data so the model can decide which guide to pull next with `guide_get`.
     *
     * @param string $query
     *   The query string describing what guidance you need (e.g. "cardinality vs occurrences", "slot constraints"). Leave empty to search all guides.
     *
     * @param string $category
     *   Optional guide category filter (e.g. "archetypes", "templates"). Leave empty to search all guides.
     *
     * @param string $taskType
     *   Optional task hint (e.g. "lint", "review", "refactor", "author"). If supplied, matches guides containing it.
     *
     * @return array<string, array<int, array<string, string|int>>>
     *   A list of matching guides with short snippets and URIs.
     */
    #[McpTool(
        name: 'guide_search',
        annotations: new ToolAnnotations(readOnlyHint: true),
        outputSchema: [
            'type' => 'object',
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'description' => 'List of matching guide snippets and canonical guide URIs',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string', 'description' => 'Guide title'],
                            'category' => ['type' => 'string', 'description' => 'Guide category, e.g. archetypes/templates'],
                            'name' => ['type' => 'string', 'description' => 'Guide filename without extension'],
                            'resourceUri' => ['type' => 'string', 'description' => 'Canonical guide URI in openehr://guides namespace'],
                            'snippet' => ['type' => 'string', 'description' => 'Short, task-relevant snippet'],
                            'score' => ['type' => 'integer', 'description' => 'Relative match score for sorting (higher is better)'],
                        ],
                    ],
                ],
            ],
        ],
    )]
    public function search(string $query = '', string $category = '', string $taskType = ''): array
    {
        $this->logger->debug('called ' . __METHOD__, func_get_args());
        $query = trim($query);
        $category = trim($category);
        $taskType = trim($taskType);

        $results = [];
        foreach ($this->getGuideFiles() as $fileInfo) {
            $guide = $this->extractGuide($fileInfo);
            if ($category && $guide['category'] !== $category) {
                continue;
            }
            if ($taskType && stripos($guide['content'], $taskType) === false) {
                continue;
            }

            $results[] = [
                'title' => $guide['title'],
                'category' => $guide['category'],
                'name' => $guide['name'],
                'resourceUri' => $guide['resourceUri'],
                'snippet' => $this->buildSnippet($guide['content'], $query),
                'score' => $this->scoreGuide($query, $guide['title'], $guide['content'], $guide['category']),
            ];
        }

        usort($results, static fn(array $a, array $b): int => $b['score'] <=> $a['score']);
        $results = array_slice($results, 0, self::DEFAULT_MAX_RESULTS);

        return ['items' => $results];
    }

    /**
     * Retrieve a guide's full markdown content by URI or by (category, name).
     *
     * This tool retrieves the full openEHR guide for specific implementation tasks.
     * Such guides describe modeling workflows, best practices, syntax checklists, antipatterns and other guidance on demand.
     *
     * @param string $uri
     *   Canonical guide URI (openehr://guides/{category}/{name}). Optional when category and name are provided.
     *
     * @param string $category
     *   Guide category (e.g. "archetypes" or "templates"). Optional when URI is provided.
     *
     * @param string $name
     *   Guide filename without extension. Optional when URI is provided.
     *
     * @return EmbeddedResource
     *   The selected guide markdown content.
     */
    #[McpTool(
        name: 'guide_get',
        annotations: new ToolAnnotations(readOnlyHint: true),
    )]
    public function get(string $uri = '', string $category = '', string $name = ''): EmbeddedResource
    {
        $this->logger->debug('called ' . __METHOD__, func_get_args());
        $uri = trim($uri);
        $category = trim($category);
        $name = trim($name);

        if ($uri) {
            [$category, $name] = $this->parseGuideUri($uri);
        }

        if (!$category || !$name) {
            throw new ToolCallException('Guide category and name are required when URI is not provided.');
        }

        $path = $this->guidePath($category, $name);
        if (!is_file($path) || !is_readable($path)) {
            throw new ToolCallException(sprintf('Guide not found: %s/%s', $category, $name));
        }

        $content = (string)file_get_contents($path);
        if (!$content) {
            throw new ToolCallException(sprintf('Guide content is empty: %s/%s', $category, $name));
        }

        return new EmbeddedResource(
            resource: new TextResourceContents(
                uri: $this->buildGuideUri($category, $name),
                mimeType: 'text/markdown',
                text: $content,
            ),
        );
    }

    /**
     * Lookup ADL idiom snippets for a symptom or pattern to prevent generic prompting.
     *
     * This tool is a targeted cheatsheet retrieval for common ADL constraint idioms.
     * Provide the symptom or pattern (e.g. "occurrences vs cardinality", "coded text", "slots") to receive matching examples.
     *
     * @param string $pattern
     *   Symptom or pattern string to search within the ADL idioms cheatsheet.
     *
     * @return array<string, array<int, array<string, string>>>
     *   Matching idiom snippets with headings and canonical guide URIs.
     */
    #[McpTool(
        name: 'guide_adl_idiom_lookup',
        annotations: new ToolAnnotations(readOnlyHint: true),
        outputSchema: [
            'type' => 'object',
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'description' => 'Matching ADL idiom snippets',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'snippet' => ['type' => 'string'],
                            'resourceUri' => ['type' => 'string'],
                            'section' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ],
    )]
    public function adlIdiomLookup(string $pattern): array
    {
        $this->logger->debug('called ' . __METHOD__, func_get_args());
        $pattern = trim($pattern);
        if ($pattern === '') {
            return ['items' => []];
        }

        $category = 'archetypes';
        $name = 'adl-idioms-cheatsheet';
        $path = $this->guidePath($category, $name);
        if (!is_file($path) || !is_readable($path)) {
            throw new ToolCallException('ADL idioms cheatsheet not found.');
        }

        $content = (string)file_get_contents($path);
        $title = $this->extractTitle($content, $name);
        $sections = $this->parseSections($content);

        $matches = [];
        foreach ($sections as $section) {
            $score = $this->scoreGuide($pattern, $section['title'], $section['content']);
            if ($score === 0) {
                continue;
            }
            $matches[] = [
                'title' => $title,
                'snippet' => $this->buildSnippet($section['content'], $pattern),
                'resourceUri' => $this->buildGuideUri($category, $name),
                'section' => $section['title'],
                'score' => $score,
            ];
        }

        usort($matches, static fn(array $a, array $b): int => $b['score'] <=> $a['score']);
        $matches = array_slice($matches, 0, self::DEFAULT_SECTION_LIMIT + 2);

        return ['items' => array_map(static function (array $match): array {
            unset($match['score']);
            return $match;
        }, $matches)];
    }

    /** @return array<int, SplFileInfo> */
    private function getGuideFiles(): array
    {
        if (!is_dir(self::GUIDE_DIR)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(self::GUIDE_DIR, \FilesystemIterator::SKIP_DOTS)
        );
        /** @var SplFileInfo $fileInfo */
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            if (strtolower($fileInfo->getExtension()) !== 'md') {
                continue;
            }
            $files[] = $fileInfo;
        }

        return $files;
    }

    /**
     * @return array{title: string, category: string, name: string, resourceUri: string, content: string}
     */
    private function extractGuide(SplFileInfo $fileInfo): array
    {
        $content = (string)file_get_contents($fileInfo->getPathname()) ?: '';
        $relative = str_replace(self::GUIDE_DIR . '/', '', $fileInfo->getPathname());
        $parts = explode('/', $relative);
        $category = $parts[0] ?: 'unknown';
        $name = $fileInfo->getBasename('.md');

        return [
            'title' => $this->extractTitle($content, $name),
            'category' => $category,
            'name' => $name,
            'resourceUri' => $this->buildGuideUri($category, $name),
            'content' => $content,
        ];
    }

    private function extractTitle(string $content, string $fallback): string
    {
        foreach (preg_split('/\r?\n/', $content) ?: [] as $line) {
            $line = trim($line);
            if (str_starts_with($line, '# ')) {
                return trim(substr($line, 2));
            }
        }

        return $fallback;
    }

    /** @return array<int, array{title: string, level: int, content: string}> */
    private function parseSections(string $content): array
    {
        $lines = preg_split('/\r?\n/', $content) ?: [];
        $sections = [];
        $current = [
            'title' => 'Introduction',
            'level' => 2,
            'content' => '',
        ];

        foreach ($lines as $line) {
            if (preg_match('/^(#{2,3})\s+(.*)$/', trim($line), $matches)) {
                if (trim($current['content']) !== '') {
                    $sections[] = $current;
                }
                $current = [
                    'title' => trim($matches[2]),
                    'level' => strlen($matches[1]),
                    'content' => '',
                ];
                continue;
            }

            $current['content'] .= $line . "\n";
        }

        if (trim($current['content']) !== '') {
            $sections[] = $current;
        }

        return $sections;
    }

    private function scoreGuide(string $query, string $title, string $content, string $category = ''): int
    {
        $content = strtolower($content);
        $title = strtolower($title);
        $keywords = array_filter(preg_split('/\s+/', trim($query)) ?: []);

        $score = 0;
        foreach ($keywords as $keyword) {
            if (str_contains($title, $keyword)) {
                $score += 4;
            }
            if ($category && str_contains($category, $keyword)) {
                $score += 3;
            }
            $score += min(substr_count($content, $keyword), 6);
        }

        return $score;
    }

    private function buildSnippet(string $content, string $query): string
    {
        $lower = strtolower($content);
        $needle = strtolower($query);
        $pos = strpos($lower, $needle);
        if ($pos === false) {
            return $this->limitText($content, self::SNIPPET_CHARS);
        }

        $start = max(0, $pos - (int)(self::SNIPPET_CHARS / 2));
        $snippet = substr($content, $start, self::SNIPPET_CHARS);
        return trim($snippet);
    }

    private function limitText(string $text, int $maxChars): string
    {
        $text = trim($text);
        if (strlen($text) <= $maxChars) {
            return $text;
        }

        return rtrim(substr($text, 0, $maxChars - 1)) . '…';
    }

    /**
     * @param string $uri
     * @return array{string, string}
     */
    private function parseGuideUri(string $uri): array
    {
        $pattern = '#^openehr://guides/([\w-]+)/([\w-]+)$#';
        if (!preg_match($pattern, $uri, $matches)) {
            throw new ToolCallException(sprintf('Invalid guide URI: %s', $uri));
        }

        return [$matches[1], $matches[2]];
    }

    private function guidePath(string $category, string $name): string
    {
        $category = trim($category);
        $name = trim($name);
        if (!$category || !$name) {
            return '';
        }

        return self::GUIDE_DIR . '/' . $category . '/' . $name . '.md';
    }

    private function buildGuideUri(string $category, string $name): string
    {
        return sprintf('openehr://guides/%s/%s', $category, $name);
    }
}
