<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Services\Search\Elasticsearch\ElasticService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class IndexUpload implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Upload $upload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->upload->id))->dontRelease()];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ElasticService $elasticService)
    {
        $upload = $this->upload;
        $elasticService->indexDocument($upload->id, $this->translateToArray($upload));
        $upload->indexed_at = Carbon::now();
        $upload->save();
    }

    private function translateToArray(Upload $upload): array
    {
        return [
            'upload_id' => $upload->id,
            'url' => $upload->url,
            'tags' => $upload->tags()->get()->pluck('tag'),
        ];
    }
}
