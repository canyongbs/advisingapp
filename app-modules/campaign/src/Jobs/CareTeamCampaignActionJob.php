<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CareTeamCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
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

            if ($action->data['remove_prior']) {
                $educatable->careTeam()->detach();
            }

            foreach ($action->data['careTeam'] as $careTeam) {
                $educatable
                    ->careTeam()
                    ->syncWithPivotValues(
                        ids: $careTeam['user_id'],
                        values: ['care_team_role_id' => $careTeam['care_team_role_id']],
                        detaching: false,
                    );
            }

            // Because we are updating multiple pivots to add Users to the Educatable's care team,
            // we do not relate any record to the actionEducatable.
            $this->actionEducatable->markSucceeded();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
