<?php

namespace AdvisingApp\StudentDataModel\Filament\Tables;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\EnrollmentSemester;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UnsyncedEnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Enrollment::query()
                ->whereNotIn('semester_name', EnrollmentSemester::query()->select('name'))
                ->distinct('semester_name')
                ->orderBy('semester_name'))
            ->columns([
                TextColumn::make('semester_name'),
            ]);
    }
}
