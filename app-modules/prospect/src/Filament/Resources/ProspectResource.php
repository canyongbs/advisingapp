<?php

namespace Assist\Prospect\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Prospect\Models\Prospect;
use Filament\Resources\RelationManagers\RelationGroup;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages;
use Assist\Task\Filament\Resources\TaskResource\RelationManagers\TasksRelationManager;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementsRelationManager;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\InteractionsRelationManager;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementFilesRelationManager;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementResponsesRelationManager;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 2;

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Engagement', [
                EngagementsRelationManager::class,
                EngagementResponsesRelationManager::class,
                EngagementFilesRelationManager::class,
            ]),
            TasksRelationManager::class,
            InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspects::route('/'),
            'create' => Pages\CreateProspect::route('/create'),
            'view' => Pages\ViewProspect::route('/{record}'),
            'edit' => Pages\EditProspect::route('/{record}/edit'),
        ];
    }
}
