<?php

namespace App\Jobs;

use App\Models\Upload;
use Carbon\Carbon;
use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Execute the job.
     *
     * @param Client $client
     * @return void
     */
    public function handle(Client $client)
    {
        $upload = $this->upload;
        $client->index([
            'id' => $upload->id,
            'index' => 'uploads',
            'body' => $this->translateToArray($upload),
        ]);
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
