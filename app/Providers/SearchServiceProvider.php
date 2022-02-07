<?php

namespace App\Providers;

use App\Services\Search\Elasticsearch\ElasticSearchResultTranslator;
use App\Services\Search\Elasticsearch\ElasticService;
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
        $this->app->when(ElasticService::class)
            ->needs('$config')
            // See the services.php config for the elasticsearch config being passed in
            ->giveConfig('services.elasticsearch');
        $this->app->singleton(ElasticService::class);
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
