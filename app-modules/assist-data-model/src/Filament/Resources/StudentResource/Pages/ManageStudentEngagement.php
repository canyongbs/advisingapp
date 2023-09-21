<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementFilesRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementResponsesRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use function Filament\authorize;

class ManageStudentEngagement extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    // Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'engagements';

    protected static ?string $navigationLabel = 'Engagements';

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
