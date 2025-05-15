<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProactiveAlertCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
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

            $action = $this->actionEducatable->campaignAction;

            $alert = Alert::query()->create([
                'concern_type' => $educatable->getMorphClass(),
                'concern_id' => $educatable->getKey(),
                'description' => $action->data['description'],
                'severity' => $action->data['severity'],
                'status_id' => $action->data['status_id'],
                'suggested_intervention' => $action->data['suggested_intervention'],
            ]);

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable->related()->associate($alert);
            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
