<?php
declare(strict_types=1);

namespace Cadasto\OpenEHR\MCP\Assistant\Resources;

use Mcp\Capability\Attribute\CompletionProvider;
use Mcp\Capability\Attribute\McpResource;
use Mcp\Capability\Attribute\McpResourceTemplate;
use Mcp\Exception\ResourceReadException;
use Mcp\Server\Builder;
use SimpleXMLElement;

final class Terminologies
{
    public const string FILE_PATH = APP_RESOURCES_DIR . '/terminology/openehr_terminology.xml';

    private SimpleXMLElement|null $xml = null;

    private function loadXml(): SimpleXMLElement
    {
        if ($this->xml === null) {
            if (!file_exists(self::FILE_PATH) || !is_readable(self::FILE_PATH)) {
                throw new ResourceReadException('Terminology file not found or not readable.');
            }

            $content = file_get_contents(self::FILE_PATH);
            if ($content === false) {
                throw new ResourceReadException('Unable to read terminology file.');
            }

            try {
                $this->xml = new SimpleXMLElement($content);
            } catch (\Exception $e) {
                throw new ResourceReadException('Error parsing terminology XML: ' . $e->getMessage());
            }
        }

        return $this->xml;
    }

    /**
     * Read the entire openEHR terminology.
     *
     * URI: openehr://terminology/all
     *
     * @return array<string, mixed>
     *   The entire terminology as an associative array.
     */
    #[McpResource(
        uri: 'openehr://terminology/all',
        name: 'terminology_all',
        description: 'The entire openEHR terminology',
        mimeType: 'application/json'
    )]
    public function readAll(): array
    {
        $xml = $this->loadXml();
        $results = ['codesets' => [], 'groups' => []];
        foreach ((array)$xml->xpath('//codeset') as $element) {
            $results['codesets'][] = [
                'name' => (string)$element['name'],
                'issuer' => (string)$element['issuer'],
                'openehr_id' => (string)$element['openehr_id'],
                'external_id' => (string)$element['external_id'],
                'codeset' => array_map(fn($code) => (string)$code['value'], (array)$element->xpath('code')),
            ];
        }
        foreach ((array)$xml->xpath('//group') as $element) {
            $concepts = [];
            foreach ($element->concept as $concept) {
                $concepts[(string)$concept['id']] = (string)$concept['rubric'];
            }
            $results['groups'][] = [
                'name' => (string)$element['name'],
                'openehr_id' => (string)$element['openehr_id'],
                'group' => $concepts,
            ];
        }
        return $results;
    }

    /**
     * Read a terminology group or codeset from openEHR's terminology.
     *
     * URI template:
     *  openehr://terminology/{type}/{openehr_id}
     *
     * Examples:
     *  - openehr://terminology/group/attestation_reason
     *  - openehr://terminology/codeset/compression_algorithms
     *
     * @param string $type
     *   The terminology type: "group" or "codeset".
     * @param string $openehr_id
     *   The openEHR terminology ID.
     * @return array<string, mixed>
     *   The terminology group or codeset as an associative array.
     */
    #[McpResourceTemplate(
        uriTemplate: 'openehr://terminology/{type}/{openehr_id}',
        name: 'terminology',
        description: 'An openEHR terminology group or codeset',
        mimeType: 'application/json'
    )]
    public function read(
        #[CompletionProvider(values: ['group', 'codeset'])]
        string $type,
        string $openehr_id
    ): array
    {
        $type = strtolower(trim($type));
        $openehr_id = trim($openehr_id);

        if (!in_array($type, ['group', 'codeset'])) {
            throw new \InvalidArgumentException(sprintf('Invalid terminology type: %s', $type));
        }

        $xml = $this->loadXml();
        $xpath = sprintf('/terminology/%s[@openehr_id="%s"]', $type, $openehr_id);
        $elements = $xml->xpath($xpath);

        if (empty($elements)) {
            throw new ResourceReadException(sprintf('Terminology %s not found: %s', $type, $openehr_id));
        }

        $element = $elements[0];
        $found = [];

        if ($type === 'group') {
            foreach ($element->concept as $concept) {
                $found[(string)$concept['id']] = (string)$concept['rubric'];
            }
        } else {
            foreach ($element->code as $code) {
                $found[] = (string)$code['value'];
            }
        }

        return [
            'openehr_id' => (string)$element['openehr_id'],
            'name' => (string)$element['name'],
            $type => $found,
        ];
    }

    /**
     * Registers terminology groups and codesets as MCP resources.
     *
     * @param Builder $builder
     * @return void
     */
    public static function addResources(Builder $builder): void
    {
        $instance = new self();
        try {
            $xml = $instance->loadXml();
        } catch (\Exception) {
            return;
        }

        // Register all
        $builder->addResource(
            handler: fn() => $instance->readAll(),
            uri: 'openehr://terminology/all',
            name: 'terminology_all',
            description: 'The entire openEHR terminology',
            mimeType: 'application/json'
        );

        // Register groups
        foreach ($xml->group as $group) {
            $id = (string)$group['openehr_id'];
            $name = (string)$group['name'];
            $builder->addResource(
                handler: fn() => $instance->read('group', $id),
                uri: sprintf('openehr://terminology/group/%s', $id),
                name: sprintf('terminology_group_%s', $id),
                description: sprintf('Group: %s', $name),
                mimeType: 'application/json'
            );
        }

        // Register codesets
        foreach ($xml->codeset as $codeset) {
            $id = (string)$codeset['openehr_id'];
            $name = (string)$codeset['name'];
            $builder->addResource(
                handler: fn() => $instance->read('codeset', $id),
                uri: sprintf('openehr://terminology/codeset/%s', $id),
                name: sprintf('terminology_codeset_%s', $id),
                description: sprintf('Codeset: %s', $name),
                mimeType: 'application/json'
            );
        }
    }
}
