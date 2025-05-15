<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class InteractionCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
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

            $interaction = Interaction::query()->create([
                'user_id' => $action->campaign->createdBy instanceof User
                    ? $action->campaign->createdBy->getKey()
                    : null,
                'interactable_type' => $educatable->getMorphClass(),
                'interactable_id' => $educatable->getKey(),
                'interaction_type_id' => $action->data['interaction_type_id'],
                'interaction_initiative_id' => $action->data['interaction_initiative_id'],
                'interaction_relation_id' => $action->data['interaction_relation_id'],
                'interaction_driver_id' => $action->data['interaction_driver_id'],
                'interaction_status_id' => $action->data['interaction_status_id'],
                'interaction_outcome_id' => $action->data['interaction_outcome_id'],
                'division_id' => $action->data['division_id'],
            ]);

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable->related()->associate($interaction);
            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
