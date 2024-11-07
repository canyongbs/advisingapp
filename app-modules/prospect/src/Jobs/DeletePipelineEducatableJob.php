<?php

namespace AdvisingApp\Prospect\Jobs;

use AdvisingApp\Prospect\Models\Pipeline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeletePipelineEducatableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Pipeline $pipeline
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::table('pipeline_educatable')
                ->where('educatable_type', 'prospect')
                ->orderBy('created_at','DESC')
                ->where('pipeline_id', $this->pipeline->getKey())
                ->whereNotIn('educatable_id', function($query) {
                    $query->select('id')
                        ->fromSub(
                            $this->pipeline?->segment->retrieveEducatablesRecords()->select('id'), 
                            'educatables'
                        );
                })
                ->chunk(100,function($educatables) {
                    $educatables->each(function($educatable) {
                        $this->pipeline?->educatables()->detach($educatable->educatable_id);
                    });
                });
    }

    public function failed(?Throwable $exception): void
    {
        Log::debug(__('Failed to sync pipeline at time of removal prospect :pipeline',[
            'pipeline' => $this->pipeline->name
        ]));

        report($exception); 
    }
}
