<?php

namespace App\Http\Controllers\API\Upload;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadGifRequest;
use App\Http\Requests\UploadMetaRequest;
use App\Http\Resources\UploadDataResource;
use App\Jobs\IndexUpload;
use App\Models\Tag;
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
        $upload->path = $path;
        $upload->driver = $filesystemManager->getDefaultDriver();
        $upload->url = $filesystemManager->disk()
            ->url($path);
        $upload->save();
        $this->associateTags($upload, $request->tags);

        IndexUpload::dispatch($upload);

        return response([
            'id' => $upload->id,
        ], 201);
    }

    public function updateMeta(Upload $upload, UploadMetaRequest $request)
    {
        $this->associateTags($upload, $request->tags);

        IndexUpload::dispatch($upload);

        return response(null, 201);
    }

    public function getUpload(Upload $upload)
    {
        $upload->loadMissing(['tags', 'userIntegration']);
        return new UploadDataResource($upload);
    }

    public function getFile(Upload $upload, FilesystemManager $filesystemManager)
    {
        $filesystem = $filesystemManager->disk($upload->driver);
        return $filesystem->download($upload->path);
    }

    private function associateTags(Upload $upload, array $tags): void
    {
        $existingTags = Tag::query()
            ->whereIn('tag', $tags)
            ->get();

        $tagsToCreate = array_filter($tags, function (string $tag) use ($existingTags) {
            return is_null($existingTags->where('tag', $tag)->first());
        });

        foreach ($tagsToCreate as $tagToCreate) {
            $tag = new Tag();
            $tag->tag = $tagToCreate;
            $tag->save();
            $existingTags->merge([ $tag ]);
        }

        $upload->tags()->sync($existingTags->pluck('id'));
        $upload->save();
    }
}
