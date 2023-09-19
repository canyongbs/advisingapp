<?php

namespace Assist\Task\Filament\Pages;

use Filament\Pages\Page;

class TaskKanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'task::filament.pages.task-kanban';
}
