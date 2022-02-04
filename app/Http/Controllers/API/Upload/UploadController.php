<?php

namespace App\Http\Controllers\API\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadGifRequest;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Filesystem\FilesystemManager;

class UploadController extends Controller
{
    public function uploadGif(UploadGifRequest $request, FilesystemManager $filesystemManager)
    {
        /** @var User $user */
        $user = $request->user();
        $userIntegration = $user->getAuthenticatedUserIntegration();
        $path = $request->file('image')->storePublicly("gifs/$userIntegration->id");

        $upload = new Upload();
        $upload->userIntegration()->associate($userIntegration);
        $upload->url = $filesystemManager->disk()
            ->url($path);
        $upload->save();

        return response([
            'id' => $upload->id,
        ], 201);
    }
}
