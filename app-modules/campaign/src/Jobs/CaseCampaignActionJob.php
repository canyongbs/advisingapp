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

use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CaseCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            if (! app(LicenseSettings::class)->data->addons->caseManagement) {
                throw new Exception('The Case Management addon is not enabled.');
            }

            DB::beginTransaction();

            $educatable = $this->actionEducatable->educatable;

            throw_if(
                ! $educatable instanceof Educatable,
                new Exception('The educatable model must implement the Educatable contract.')
            );

            $action = $this->actionEducatable->campaignAction;

            $userId = $action->campaign->createdBy instanceof User
                ? $action->campaign->createdBy->getKey()
                : null;

            $case = CaseModel::query()->create([
                'respondent_type' => $educatable->getMorphClass(),
                'respondent_id' => $educatable->getKey(),
                'close_details' => $action->data['close_details'],
                'res_details' => $action->data['res_details'],
                'division_id' => $action->data['division_id'] ?? Division::where('is_default', true)->value('id') ?? Division::first()->getKey(),
                'status_id' => $action->data['status_id'],
                'priority_id' => $action->data['priority_id'],
                'created_by_id' => $userId,
            ]);

            if (isset($action->data['assigned_to_id'])) {
                if ($action->data['assigned_to_id'] === 'automatic') {
                    $assignmentClass = $case->priority->type->assignment_type->getAssignerClass();
                    $assignmentClass->execute($case);
                } else {
                    $case->assignments()->create([
                        'user_id' => $action->data['assigned_to_id'],
                        'assigned_by_id' => $userId,
                        'assigned_at' => now(),
                        'status' => CaseAssignmentStatus::Active,
                    ]);
                }
            }

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable
                ->related()
                ->make()
                ->related()
                ->associate($case)
                ->save();

            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
