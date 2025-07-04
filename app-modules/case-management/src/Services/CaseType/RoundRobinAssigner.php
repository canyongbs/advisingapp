<?php

namespace AdvisingApp\CaseManagement\Services\CaseType;

use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class RoundRobinAssigner implements CaseTypeAssigner
{
    public function execute(CaseModel $case): void
    {
        $caseType = $case->priority->type;

        $lastAssignee = $caseType->lastAssignedUser;
        $user = null;

        if ($lastAssignee) {
            $user = User::query()->whereRelation('team.manageableCaseTypes', 'case_types.id', $caseType->getKey())
                ->where('name', '>=', $lastAssignee->name)
                ->where(fn (Builder $query) => $query
                    ->where('name', '!=', $lastAssignee->name)
                    ->orWhere('users.id', '>', $lastAssignee->id))
                ->orderBy('name')->orderBy('id')->first();
        }

        if ($user === null) {
            $user = User::query()->whereRelation('team.manageableCaseTypes', 'case_types.id', $caseType->getKey())
                ->orderBy('name')->orderBy('id')->first();
        }

        if ($user !== null) {
            $caseType->last_assigned_id = $user->getKey();
            $caseType->save();
            $case->assignments()->create([
                'user_id' => $user->getKey(),
                'assigned_by_id' => null,
                'assigned_at' => now(),
                'status' => CaseAssignmentStatus::Active,
            ]);
        }
    }
}
