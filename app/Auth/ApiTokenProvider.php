<?php

namespace App\Auth;

use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiTokenProvider
{
    private Request $request;
    private ?ApiToken $apiToken = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getApiToken(): ?ApiToken
    {
        if (!is_null($this->apiToken)) {
            return $this->apiToken;
        }

        return $this->apiToken = $this->decodeToken($this->request);
    }

    public function hasApiToken(): bool
    {
        return !is_null($this->getApiToken());
    }

    private function decodeToken(Request $request): ?ApiToken
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return null;
        }

        $decoded = base64_decode($token);
        if (!Str::contains($decoded, ':')) {
            return null;
        }

        $exploded = explode(':', $decoded);

        if (count($exploded) !== 2) {
            return null;
        }

        /** @var ?ApiToken $apiToken */
        $apiToken = ApiToken::query()
            ->where('client_id', '=', $exploded[0])
            ->first();

        if (is_null($apiToken)) {
            return null;
        }

        if (!Hash::check($exploded[1], $apiToken->token)) {
            return null;
        }

        return $apiToken;
    }
}
