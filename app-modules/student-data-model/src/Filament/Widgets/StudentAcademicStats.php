<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StudentAcademicStats extends StatsOverviewWidget
{
    public Student $record;

    protected function getStats(): array
    {
        $studentId = $this->record->getKey();

        return [
            Stat::make(
                'Courses Attempted',
                Number::format(
                    Enrollment::query()
                        ->where('sisid', $studentId)
                        ->count()
                )
            ),

            Stat::make(
                'Courses Completed',
                Number::format(
                    Enrollment::query()
                        ->where('sisid', $studentId)
                        ->where('unt_earned', '>', 0)
                        ->count()
                )
            ),

            Stat::make(
                'Credits Attempted',
                Number::format(
                    Enrollment::query()
                        ->where('sisid', $studentId)
                        ->sum('unt_taken'),
                )
            ),

            Stat::make(
                'Credits Earned',
                Number::format(
                    Enrollment::query()
                        ->where('sisid', $studentId)
                        ->sum('unt_earned'),
                )
            ),
        ];
    }
}
