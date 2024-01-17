<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Filament\Widgets\TasksWidget;

class StudentTasks extends TasksWidget
{
    public function title(): string
    {
        return 'My Tasks for Students';
    }

    public function concern(): string
    {
        return Student::class;
    }
}
