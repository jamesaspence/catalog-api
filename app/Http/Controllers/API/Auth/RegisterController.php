<?php


namespace App\Http\Controllers\API\Auth;


use App\Auth\ApiTokenProvider;
use App\Http\Requests\RegisterRequest;
use App\Models\ApiToken;
use App\Models\User;

class RegisterController
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

        if (!is_null($user)) {
            $alreadyIntegrated = User::query()
                ->join('integration_user', 'users.id', '=', 'integration_user')
                ->where('user_id', '=', $user->id)
                ->where('integration_id', '=', $apiToken->integration_id)
                ->exists();

            if ($alreadyIntegrated) {
                return response(null, 403);
            }
        } else {
            $user = new User();
            $user->email = $request->email;
            $user->save();
            $user->userIntegrations()->attach($apiToken->integration_id, [ 'external_id' => $request->external_id ]);
        }

        return response(null, 204);
    }
}
