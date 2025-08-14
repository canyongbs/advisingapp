<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Concerns\SemesterSelectForOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\HasMaxOperator;

class HasMaxOperatorWithEnrollmentSemester extends HasMaxOperator
{
    use SemesterSelectForOperator;

    public function getFormSchema(): array
    {
        return array_merge([
            $this->semesterSelect(),
        ], parent::getFormSchema());
    }
}
