<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class TagCampaignActionJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        public CampaignActionEducatable $actionEducatable,
    ) {}

    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled()];
    }

    public function handle(): void
    {
        $educatable = $this->actionEducatable->educatable;

        throw_if(
            ! $educatable instanceof Educatable,
            new Exception('The educatable model must implement the Educatable contract.')
        );

        $educatable
            ->tags()
            ->sync(
                ids: $this->actionEducatable->campaignAction->data['tag_ids'],
                detaching: $this->actionEducatable->campaignAction->data['remove_prior']
            );
    }
}
