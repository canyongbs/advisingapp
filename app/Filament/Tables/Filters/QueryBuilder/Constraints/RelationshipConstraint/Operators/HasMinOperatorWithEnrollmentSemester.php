<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Concerns\SemesterSelectForOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMinOperator;

class HasMinOperatorWithEnrollmentSemester extends HasMinOperator
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
