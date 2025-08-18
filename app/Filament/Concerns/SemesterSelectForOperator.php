<?php

namespace App\Filament\Concerns;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\StudentDataModel\Models\Enrollment;

trait SemesterSelectForOperator
{
    public static function semesterSelect(): Select
    {
        return Select::make('semesters')
            ->label('Semester')
            ->options(static::getSemesterOptions())
            ->placeholder('Any semester')
            ->searchable()
            ->multiple()
            ->preload();
    }

    /**
     * @return array<string, string>
     */
    public static function getSemesterOptions(): array
    {
        return Enrollment::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($enrollment) => [$enrollment->name => $enrollment->name])
            ->toArray();
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
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
