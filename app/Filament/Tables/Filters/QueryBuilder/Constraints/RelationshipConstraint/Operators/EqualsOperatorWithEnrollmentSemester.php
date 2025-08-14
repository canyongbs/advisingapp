<?php

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use App\Filament\Concerns\SemesterSelectForOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\EqualsOperator;
use Illuminate\Database\Eloquent\Builder;

class EqualsOperatorWithEnrollmentSemester extends EqualsOperator
{
    use SemesterSelectForOperator;

    public function getFormSchema(): array
    {
        return array_merge([
            $this->semesterSelect(),
        ], parent::getFormSchema());
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        $relationshipName = $this->constraint->getRelationshipName();
        $count = $this->settings['count'] ?? 1;
        $semesters = $this->settings['semesters'] ?? null;

        if (is_null($semesters)) {
            $semesters = [];
        } elseif (! is_array($semesters)) {
            $semesters = [$semesters];
        }

        $semesters = array_values(array_filter($semesters, fn ($s) => $s !== null && $s !== ''));
        $lowerSemesters = array_map(fn ($s) => mb_strtolower($s), $semesters);

        return $query->whereHas($relationshipName, function (Builder $q) use ($lowerSemesters) {
            if (! empty($lowerSemesters)) {
                $placeholders = implode(',', array_fill(0, count($lowerSemesters), '?'));
                $q->whereRaw("LOWER(name) IN ({$placeholders})", $lowerSemesters);
            }
        }, '>=', $count);
    }

    public function getSummary(): string
    {
        $summary = parent::getSummary();

        if (! empty($this->settings['semesters'])) {
            $semesters = $this->settings['semesters'];

            if (! is_array($semesters)) {
                $semesters = [$semesters];
            }
            $concatedSemester = implode(', ', $semesters);
            $summary .= ' in semester "' . $concatedSemester . '"';
        }

        return $summary;
    }
}
