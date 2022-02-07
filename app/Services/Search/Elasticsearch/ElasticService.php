<?php

namespace App\Services\Search\Elasticsearch;

use App\Services\Search\SearchResult;
use App\Services\Search\SearchResultTranslator;
use Elasticsearch\Client;

class ElasticService
{
    private Client $client;
    private SearchResultTranslator $searchResultTranslator;
    private array $config;

    public function __construct(Client $client, SearchResultTranslator $searchResultTranslator, array $config)
    {
        $this->client = $client;
        $this->searchResultTranslator = $searchResultTranslator;
        $this->config = $config;
    }

    /**
     * Index a given document with the given id and payload.
     * Will override any existing document in elasticsearch, or create it if it does not exist.
     * If index is not provided, the default configured index will be used instead.
     *
     * @param string $id
     * @param array $document
     * @param string|null $index
     * @return void
     */
    public function indexDocument(string $id, array $document, string $index = null): void
    {
        $this->client->index([
            'id' => $id,
            'index' => $this->getIndex($index),
            'body' => $document,
        ]);
    }

    /**
     * Performs a fuzzy search with the given text and fuzziness.
     * If fuzziness is invalid or not specified, defaults to 2 (highest amount of fuzziness).
     * Returns an array of search result instances.
     *
     * @param string $text - the text to search by.
     * @param int $fuzziness
     * @return SearchResult[]
     */
    public function fuzzySearch(string $text, int $fuzziness = 2, string $index = null): array
    {
        if ($fuzziness < 0 || $fuzziness > 2) {
            $fuzziness = 2;
        }

        $response = $this->client->search([
            'index' => $this->getIndex($index),
            'body' => [
                'query' => [
                    'match' => [
                        'tags' => [
                            'query' => $text,
                            'fuzziness' => $fuzziness,
                        ]
                    ]
                ]
            ]
        ]);

        return $this->searchResultTranslator->translateResults($response);
    }

    public function getDefaultIndex(): string
    {
        return $this->config['index'] ?? 'uploads';
    }

    private function getIndex(?string $providedIndex): string
    {
        return $providedIndex ?? $this->getDefaultIndex();
    }
}
