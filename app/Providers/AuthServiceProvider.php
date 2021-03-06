<?php

namespace App\Providers;

use App\Auth\ApiTokenProvider;
use App\Models\ApiToken;
use App\Models\Upload;
use App\Models\UserIntegration;
use App\Policies\UploadPolicy;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Upload::class => UploadPolicy::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        parent::register();

        $this->app->scoped(ApiTokenProvider::class);

        Auth::viaRequest('external-id', function (Request $request) {
            /** @var ApiTokenProvider $apiTokenProvider */
            $apiTokenProvider = app(ApiTokenProvider::class);
            if (!$apiTokenProvider->hasApiToken()) {
                return null;
            }

            $headerName = 'X-External-Id';
            if (!$request->hasHeader($headerName)) {
                return null;
            }

            /** @var ApiToken $apiToken */
            $apiToken = $apiTokenProvider->getApiToken();
            $externalId = $request->headers->get($headerName);

            /** @var ?UserIntegration $userIntegration */
            $userIntegration = UserIntegration::query()
                ->with('user')
                ->where('integration_id', '=', $apiToken->integration_id)
                ->where('external_id', '=', $externalId)
                ->first();

            if (is_null($userIntegration)) {
                return null;
            }

            $userIntegration->user->setAuthenticatedUserIntegration($userIntegration);
            return $userIntegration->user;
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
