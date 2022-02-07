<?php

namespace App\Http\Middleware;

use App\Auth\ApiTokenProvider;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticateApiToken
{
    private ApiTokenProvider $apiTokenProvider;

    public function __construct(ApiTokenProvider $apiTokenProvider)
    {
        $this->apiTokenProvider = $apiTokenProvider;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->apiTokenProvider->hasApiToken()) {
            return response(null, 401);
        }

        return $next($request);
    }
}
