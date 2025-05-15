<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class TagsCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

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

            // Because we are just attaching tags, we don't create anything other than the pivot record.
            // So we don't need to relate any records.
            $this->actionEducatable->markSucceeded();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
