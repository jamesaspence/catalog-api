<?php

namespace App\Http\Controllers\API\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadGifRequest;
use App\Http\Requests\UploadMetaRequest;
use App\Jobs\IndexUpload;
use App\Models\Tag;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Log;

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

    public function updateMeta(Upload $upload, UploadMetaRequest $request)
    {
        $existingTags = Tag::query()
            ->whereIn('tag', $request->tags)
            ->get();

        $tagsToCreate = array_filter($request->tags, function (string $tag) use ($existingTags) {
            return is_null($existingTags->where('tag', $tag)->first());
        });

        foreach ($tagsToCreate as $tagToCreate) {
            $tag = new Tag();
            $tag->tag = $tagToCreate;
            $tag->save();
            $existingTags->merge([ $tag ]);
        }

        Log::debug($existingTags->toJson());

        $upload->tags()->sync($existingTags->pluck('id'));
        // Mark as attached once any tags have been synced up
        $upload->attached = true;
        $upload->save();

        IndexUpload::dispatch($upload);

        return response(null, 201);
    }
}
