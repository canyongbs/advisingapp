<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Concerns\SemesterSelectForOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsEmptyOperator;

class IsEmptyOperatorWithEnrollmentSemester extends IsEmptyOperator
{
    use SemesterSelectForOperator;

    public function getFormSchema(): array
    {
        return array_merge(
            parent::getFormSchema(),
            [
                $this->semesterSelect(),
            ]
        );
    }
}
