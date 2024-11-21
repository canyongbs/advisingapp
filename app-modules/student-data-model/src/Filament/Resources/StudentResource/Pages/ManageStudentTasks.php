<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\HasStudentHeader;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns\CanManageEducatableTasks;

class ManageStudentTasks extends ManageRelatedRecords
{
    use CanManageEducatableTasks;
    use HasStudentHeader;

    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Tasks';
}
