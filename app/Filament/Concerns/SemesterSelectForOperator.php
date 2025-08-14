<?php

namespace App\Filament\Concerns;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use Filament\Forms\Components\Select;

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

    public static function getSemesterOptions(): array
    {
        return Enrollment::query()
            ->orderBy('name')
            ->pluck('name', 'name')
            ->mapWithKeys(fn ($semester_name, $name) => [$name => $semester_name])
            ->toArray();
    }
}
