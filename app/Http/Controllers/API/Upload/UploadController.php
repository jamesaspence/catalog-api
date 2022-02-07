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
use App\Models\UserIntegration;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadController extends Controller
{
    private FilesystemManager $filesystemManager;
    private Dispatcher $dispatcher;

    public function __construct(FilesystemManager $filesystemManager, Dispatcher $dispatcher)
    {
        $this->filesystemManager = $filesystemManager;
        $this->dispatcher = $dispatcher;
    }

    public function uploadGif(UploadGifRequest $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        /** @var UserIntegration $userIntegration */
        $userIntegration = $user->getAuthenticatedUserIntegration();
        /** @var UploadedFile $file */
        $file = $request->file('image');
        /** @var string $path */
        $path = $file->storePublicly("gifs/$userIntegration->id");

        $upload = new Upload();
        $upload->userIntegration()->associate($userIntegration);
        $upload->path = $path;
        $upload->driver = $this->filesystemManager->getDefaultDriver();
        $upload->url = $this->filesystemManager->disk()
            ->url($path);
        $upload->save();
        $this->associateTags($upload, $request->tags);
        $this->dispatcher->dispatch(new IndexUpload($upload));

        return response([
            'id' => $upload->id,
        ], 201);
    }

    public function updateMeta(Upload $upload, UploadMetaRequest $request): Response
    {
        $this->associateTags($upload, $request->tags);
        $this->dispatcher->dispatch(new IndexUpload($upload));

        return response(null, 201);
    }

    public function getUpload(Upload $upload): JsonResource
    {
        $upload->loadMissing(['tags', 'userIntegration']);
        return new UploadDataResource($upload);
    }

    public function getFile(Upload $upload): StreamedResponse
    {
        $filesystem = $this->filesystemManager->disk($upload->driver);
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
