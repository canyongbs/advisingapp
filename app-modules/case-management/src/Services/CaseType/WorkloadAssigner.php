<?php

namespace AdvisingApp\CaseManagement\Services\CaseType;

use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\CaseManagement\Models\CaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class WorkloadAssigner implements CaseTypeAssigner
{
    public function execute(CaseModel $case): void
    {
        $caseType = $case->priority->type;

        $lastAssignee = $caseType->lastAssignedUser;
        $user = null;

        if ($lastAssignee) {
            $lowestCase = User::query()->whereRelation('team.manageableCaseTypes', 'case_types.id', $caseType->getKey())
                ->withCount([
                    'cases as case_count' => function (Builder $query) {
                        $query->whereRelation('status', 'classification', '!=', SystemCaseClassification::Closed);
                    },
                ])
                ->orderBy('case_count', 'asc')
                ->first()->case_count ?? 0;

            $user = User::query()->whereRelation('team.manageableCaseTypes', 'case_types.id', $caseType->getKey())
                /** @phpstan-ignore-next-line */
                ->where(function (QueryBuilder $query) {
                    $query->selectRaw('count(*)')
                        ->from('cases')
                        ->join('case_assignments', 'case_assignments.case_model_id', '=', 'cases.id')
                        ->whereColumn('users.id', 'case_assignments.user_id')
                        ->whereExists(function (QueryBuilder $query) {
                            $query->selectRaw('*')
                                ->from('case_statuses')
                                ->whereColumn('cases.status_id', 'case_statuses.id')
                                ->where('classification', '!=', SystemCaseClassification::Closed)
                                ->whereNull('case_statuses.deleted_at');
                        })
                        ->whereNull('cases.deleted_at')
                        ->whereNull('case_assignments.deleted_at');
                }, '<=', $lowestCase)
                ->where('name', '>=', $lastAssignee->name)
                ->where(fn (Builder $query) => $query
                    ->where('name', '!=', $lastAssignee->name)
                    ->orWhere('users.id', '>', $lastAssignee->getKey()))
                ->orderBy('name')->orderBy('id')->first();
        }

        if ($user === null) {
            $user = User::query()->whereRelation('team.manageableCaseTypes', 'case_types.id', $caseType->getKey())
                ->withCount([
                    'cases as case_count' => function (Builder $query) {
                        $query->whereRelation('status', 'classification', '!=', SystemCaseClassification::Closed);
                    },
                ])
                ->orderBy('case_count', 'asc')
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
