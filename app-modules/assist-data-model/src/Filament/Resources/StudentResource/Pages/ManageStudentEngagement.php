<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementResponsesRelationManager;

class ManageStudentEngagement extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'engagements';

    protected static ?string $navigationLabel = 'Engagements';

    protected static ?string $breadcrumb = 'Engagements';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function canAccess(?Model $record = null): bool
    {
        foreach ([
            EngagementsRelationManager::class,
            EngagementResponsesRelationManager::class,
        ] as $relationManager) {
            if (! $relationManager::canViewForRecord($record, static::class)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function getRelationManagers(): array
    {
        return [
            EngagementsRelationManager::class,
            EngagementResponsesRelationManager::class,
        ];
    }
}
