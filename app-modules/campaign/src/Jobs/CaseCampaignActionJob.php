<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CaseModel;
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
                // Throw an exception if the Case Management addon is not enabled.
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
                'division_id' => $action->data['division_id'],
                'status_id' => $action->data['status_id'],
                'priority_id' => $action->data['priority_id'],
                'created_by_id' => $userId,
            ]);

            if ($action->data['assigned_to_id']) {
                $case->assignments()->create([
                    'user_id' => $action->data['assigned_to_id'],
                    'assigned_by_id' => $userId,
                    'assigned_at' => now(),
                    'status' => CaseAssignmentStatus::Active,
                ]);
            }

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable->related()->associate($case);
            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
