<?php

namespace App\Services\Search\Elasticsearch;

use App\Services\Search\SearchResult;

class ElasticSearchResult implements SearchResult
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->getData()['_id'];
    }

    /**
     * @inheritdoc
     */
    public function getUploadId(): int
    {
        return $this->getData()['_source']['upload_id'];
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getThumbnailUrl(): string
    {
        return $this->getData()['_source']['url'];
    }

    /**
     * @inheritdoc
     */
    public function getTags(): array
    {
        return $this->getData()['_source']['tags'];
    }
}
