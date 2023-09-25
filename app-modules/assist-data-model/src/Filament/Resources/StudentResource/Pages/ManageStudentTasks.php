<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Task\Filament\Resources\TaskResource\Pages\ManageTasks;

class ManageStudentTasks extends ManageTasks
{
    protected static string $resource = StudentResource::class;
}
