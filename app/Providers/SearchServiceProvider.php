<?php

namespace App\Providers;

use App\Services\Search\Elasticsearch\ElasticSearchResultTranslator;
use App\Services\Search\SearchResultTranslator;
use Illuminate\Support\ServiceProvider;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SearchResultTranslator::class, ElasticSearchResultTranslator::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
