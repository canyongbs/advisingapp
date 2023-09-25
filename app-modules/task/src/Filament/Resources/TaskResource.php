<?php

namespace Assist\Task\Filament\Resources;

use Assist\Task\Models\Task;
use Filament\Resources\Resource;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\Task\Filament\Resources\TaskResource\Pages;

class TaskResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
