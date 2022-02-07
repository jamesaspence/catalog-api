<?php

namespace App\Services\Search\Elasticsearch;

use App\Services\Search\SearchResult;
use App\Services\Search\SearchResultTranslator;
use Elasticsearch\Client;

class ElasticService
{
    private Client $client;
    private SearchResultTranslator $searchResultTranslator;

    public function __construct(Client $client, SearchResultTranslator $searchResultTranslator)
    {
        $this->client = $client;
        $this->searchResultTranslator = $searchResultTranslator;
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
    public function fuzzySearch(string $text, int $fuzziness = 2): array
    {
        if ($fuzziness < 0 || $fuzziness > 2) {
            $fuzziness = 2;
        }

        $response = $this->client->search([
            'index' => 'uploads',
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
}
