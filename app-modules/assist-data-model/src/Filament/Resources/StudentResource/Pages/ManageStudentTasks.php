<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Task\Filament\RelationManagers\BaseTaskRelationManager;

class ManageStudentTasks extends BaseTaskRelationManager
{
    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'tasks';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Tasks';

    // TODO: Automatically set from Filament based on relationship name
    public static ?string $breadcrumb = 'Tasks';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
}
