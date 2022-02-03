<?php

namespace App\Providers;

use App\Auth\ApiTokenProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        parent::register();

        $this->app->singleton(ApiTokenProvider::class, function (Application $application) {
            $apiTokenProvider = new ApiTokenProvider();

            $application->refresh('request', $apiTokenProvider, 'setRequest');

            return $apiTokenProvider;
        });
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
