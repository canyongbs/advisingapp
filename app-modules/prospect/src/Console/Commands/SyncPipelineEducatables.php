<?php

namespace AdvisingApp\Prospect\Console\Commands;

use AdvisingApp\Prospect\Jobs\DeletePipelineEducatableJob;
use AdvisingApp\Prospect\Jobs\SyncPipelineEducatableJob;
use AdvisingApp\Prospect\Jobs\SyncPipelineEducatables as JobsSyncPipelineEducatables;
use AdvisingApp\Prospect\Jobs\SyncPipelineEducatablesJob;
use AdvisingApp\Prospect\Models\Pipeline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class SyncPipelineEducatables extends Command
{
    use TenantAware;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:pipeline-educatables {--tenant=*}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pipelines = Pipeline::get();

        foreach($pipelines as $pipeline){
            Bus::chain([
                new SyncPipelineEducatableJob($pipeline),
                new DeletePipelineEducatableJob($pipeline)
            ])
            ->catch(function ($exception){
                report($exception);
            })
            ->dispatch();
        }
    }
}
