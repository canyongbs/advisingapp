<?php

namespace AdvisingApp\Prospect\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class PipelineEducatablesMoveIntoStages implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $pipeline;

    /**
     * Create a new job instance.
     *
     * @param mixed $pipeline
     */
    public function __construct($pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $stage = $this?->pipeline?->stages()->where('is_default',true)->first();

        $educatableBatch = $this->pipeline?->segment
                                ?->retrieveRecords()
                                ?->chunk(100);


        foreach ($educatableBatch as $key => $educatables) {
           foreach($educatables as $educatable){
                $this->pipeline?->prospects()->attach($educatable->getKey(), ['pipeline_stage_id' => $stage->getKey()]);
           }
        }

        //TODO: need to create logic for edit pipeline. 
        //TODO: Nightly sync pipeline job
    }
}
