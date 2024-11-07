<?php

namespace AdvisingApp\Prospect\Jobs;

use AdvisingApp\Prospect\Models\Pipeline;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncPipelineEducatableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;

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
        $defaultStage = $this->pipeline?->stages()->where('is_default', true)->first();
        
        $this->pipeline?->segment->retrieveEducatablesRecords()->whereNotIn('id', function($query) {
            $query->select('educatable_id')
                  ->from('pipeline_educatable')
                  ->where('educatable_type', 'prospect')
                  ->where('pipeline_id', $this->pipeline->getKey());
        })
        ->chunk(100,function($educatables) use ($defaultStage) {
            DB::transaction(function () use ($educatables, $defaultStage) {

                $attachData = $educatables->mapWithKeys(fn ($educatable) => [
                    $educatable->getKey() => ['pipeline_stage_id' => $defaultStage->getKey()],
                ])->toArray();

                $this->pipeline?->educatables()->attach($attachData);
            });
        });
    }

    public function failed(?Throwable $exception): void
    {
        Log::debug(__('Failed to sync pipeline :pipeline',[
            'pipeline' => $this->pipeline->name
        ]));

        report($exception); 
    }
}
