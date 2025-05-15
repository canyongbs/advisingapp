<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Exception;

class TagsCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
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
