<?php

namespace App\Http\Controllers\API\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Services\Search\Elasticsearch\ElasticService;
use App\Services\Search\SearchResult;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    public function searchForGifs(SearchRequest $request, ElasticService $elasticService): Response
    {
        $results = $elasticService->fuzzySearch($request->text);

        return response(
            collect($results)
            ->map(fn (SearchResult $result) => [
                'id' => $result->getUploadId(),
                'tags' => $result->getTags(),
                'thumbnail_url' => $result->getThumbnailUrl(),
            ])
        );
    }
}
