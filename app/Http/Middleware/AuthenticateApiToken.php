<?php

namespace App\Http\Middleware;

use App\Auth\ApiTokenProvider;
use Closure;
use Illuminate\Http\Request;

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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->apiTokenProvider->hasApiToken()) {
            return response(null, 401);
        }

        return $next($request);
    }
}
