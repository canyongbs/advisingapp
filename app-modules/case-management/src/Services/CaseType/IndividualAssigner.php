<?php

namespace AdvisingApp\CaseManagement\Services\CaseType;

use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CaseModel;

class IndividualAssigner implements CaseTypeAssigner
{
    public function execute(CaseModel $case): void
    {
        $manager = $case->priority->type->assignmentTypeIndividual;

        if ($manager) {
            $case->assignments()->create([
                'user_id' => $manager->getKey(),
                'assigned_by_id' => null,
                'assigned_at' => now(),
                'status' => CaseAssignmentStatus::Active,
            ]);
        }
    }
}
