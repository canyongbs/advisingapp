<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\CareTeam\Models\CareTeam;
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

            /** @var Educatable $educatable */
            $action = $this->actionEducatable->campaignAction;

            if ($action->data['remove_prior']) {
                $educatable->careTeam()->detach();
            }

            $addedOrUpdatedPivotModels = [];

            foreach ($action->data['careTeam'] as $careTeam) {
                $sync = $educatable
                    ->careTeam()
                    ->syncWithPivotValues(
                        ids: $careTeam['user_id'],
                        values: ['care_team_role_id' => $careTeam['care_team_role_id']],
                        detaching: false,
                    );

                $addedOrUpdatedPivotModels[] = $sync['attached'];
                $addedOrUpdatedPivotModels[] = $sync['updated'];
            }

            collect($addedOrUpdatedPivotModels)
                ->flatten()
                ->unique()
                ->each(function (string $addedOrUpdatedPivotModel) {
                    $careTeam = CareTeam::query()
                        ->where('user_id', $addedOrUpdatedPivotModel)
                        ->where('educatable_type', $this->actionEducatable->educatable_type)
                        ->where('educatable_id', $this->actionEducatable->educatable_id)
                        ->first();

                    $this->actionEducatable
                        ->related()
                        ->make()
                        ->related()
                        ->associate($careTeam)
                        ->save();
                });

            $this->actionEducatable->markSucceeded();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
