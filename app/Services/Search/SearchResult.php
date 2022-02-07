<?php

namespace App\Services\Search;

interface SearchResult
{
    /**
     * Retrieve the unique identifier of this result in the search system
     * (e.g. the internal id in elasticsearch)
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Retrieves the matching upload id of the search result.
     * Used to retrieve the upload if chosen.
     *
     * @return int
     */
    public function getUploadId(): int;

    /**
     * Retrieves any relevant meta data from the given result.
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Retrieves the thumbnail URL to display with the search result.
     *
     * @return string
     */
    public function getThumbnailUrl(): string;

    /**
     * Retrieves an array of the tags for a given record.
     *
     * @return array
     */
    public function getTags(): array;
}
