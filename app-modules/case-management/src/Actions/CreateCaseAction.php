<?php

namespace AdvisingApp\CaseManagement\Actions;

use AdvisingApp\CaseManagement\DataTransferObjects\CaseDataObject;
use AdvisingApp\CaseManagement\Models\CaseModel;
use Illuminate\Support\Facades\DB;

class CreateCaseAction
{
    public function execute(CaseDataObject $caseDataObject): CaseModel
    {
        return DB::transaction(
            function () use ($caseDataObject) {
                $case = new CaseModel($caseDataObject->toArray());
                $assignmentClass = $case->priority->type?->assignment_type?->getAssignerClass();
                $case->save();

                if ($assignmentClass) {
                    $assignmentClass->execute($case);
                }

                return $case;
            }
        );
    }
}
