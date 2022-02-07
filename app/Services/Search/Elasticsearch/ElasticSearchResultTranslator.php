<?php

namespace App\Services\Search\Elasticsearch;

use App\Services\Search\SearchResultTranslator;
use Illuminate\Support\Facades\Log;

class ElasticSearchResultTranslator implements SearchResultTranslator
{

    /**
     * @inheritdoc
     */
    public function translateResults(mixed $response): array
    {
        if (!$this->isValidFormat($response)) {
            Log::warning("Response was not an array, cannot translate to search results", [ 'response' => $response ]);
        }

        $hits = $response['hits']['hits'];

        return array_map(function (array $hit) {
            return new ElasticSearchResult($hit);
        }, $hits);
    }

    /**
     * Sanity checks the response for the expected format (e.g. array and expected structures are in place).
     * If the structure does not match what we expect, returns false.
     *
     * @param mixed $response
     * @return bool
     */
    private function isValidFormat(mixed $response): bool
    {
        if (!is_array($response)) {
            return false;
        }

        if (!array_key_exists('hits', $response)) {
            return false;
        }

        $hitResult = $response['hits'];

        if (!array_key_exists('hits', $hitResult)) {
            return false;
        }

        if (!is_array($hitResult['hits'])) {
            return false;
        }

        return true;
    }
}
