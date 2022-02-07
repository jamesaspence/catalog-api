<?php

namespace App\Http\Controllers\API\Auth;

use App\Auth\ApiTokenProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\ApiToken;
use App\Models\User;
use App\Models\UserIntegration;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request, ApiTokenProvider $apiTokenProvider)
    {
        // TODO better validation of unique email addresses, etc.
        /** @var ApiToken $apiToken */
        $apiToken = $apiTokenProvider->getApiToken();

        /** @var User $user */
        $user = User::query()
            ->where('email', '=', $request->email)
            ->first();

        $userIntegration = null;
        if (!is_null($user)) {
            /** @var ?UserIntegration $userIntegration */
            $userIntegration = $user->userIntegrations()
                ->where('integration_id', '=', $apiToken->integration_id)
                ->first();

            if (!is_null($userIntegration) && $userIntegration->external_id !== $request->external_id) {
                return response(null, 403);
            }
        } else {
            $user = new User();
            $user->email = $request->email;
            $user->save();
        }

        if (is_null($userIntegration)) {
            $userIntegration = new UserIntegration();
            $userIntegration->user()->associate($user);
            $userIntegration->integration()->associate($apiToken->integration);
            $userIntegration->external_id = $request->external_id;
            $userIntegration->save();
        }

        return response([
            'id' => $userIntegration->id,
        ]);
    }
}
