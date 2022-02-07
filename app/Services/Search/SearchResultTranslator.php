<?php

namespace App\Services\Search;

interface SearchResultTranslator
{
    /**
     * Translates a given set of data to an array of search results.
     * Returns an array of search result instances.
     *
     * @param mixed $response
     * @return SearchResult[]
     */
    public function translateResults(mixed $response): array;
}
