<?php

namespace AdvisingApp\Prospect\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use AdvisingApp\Prospect\Models\Pipeline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Throwable;

class PipelineEducatablesMoveIntoStages implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 1200;

    /**
     * Create a new job instance.
     *
     * @param mixed $pipeline
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

        $this->pipeline?->segment
            ->retrieveEducatablesRecords()
            ->chunk(100, function ($educatables) use ($defaultStage) {
                DB::transaction(function () use ($educatables, $defaultStage) {
                    $attachData = $educatables->mapWithKeys(fn ($educatable) => [
                        $educatable->getKey() => ['pipeline_stage_id' => $defaultStage->getKey()],
                    ])->toArray();

                    $this->pipeline?->educatables()->attach($attachData);
                });
            });

        $this->pipeline->refresh();

        $passedRecords = $this->pipeline?->educatables()->count();
        $failedRecords = $this->pipeline?->segment->retrieveEducatablesRecords()->count() - $passedRecords;

        if ($this->pipeline->createdBy) {
            $this->pipeline->createdBy->notify(
                Notification::make()
                    ->title('Pipeline Creation Completed')
                    ->body("Your pipeline creation has completed successful and {$passedRecords} records is processed in the background and {$failedRecords} failed.")
                    ->success()
                    ->toDatabase(),
            );
        }
    }

    public function failed(?Throwable $exception): void
    {
        if ($this->pipeline->createdBy) {
            $this->pipeline->createdBy->notify(
                Notification::make()
                    ->title('Pipeline creation unsuccessful')
                    ->body("Your pipeline creation has been failed.")
                    ->danger()
                    ->toDatabase(),
            );
        }

        Log::debug(__('Failed to insert prospect into the pipeline :pipeline',[
            'pipeline' => $this->pipeline->name
        ]));

        report($exception); 
    }
}
