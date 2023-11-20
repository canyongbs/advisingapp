<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Task\Filament\RelationManagers\BaseTaskRelationManager;

class ManageProspectTasks extends BaseTaskRelationManager
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'tasks';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Tasks';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Tasks';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
}
