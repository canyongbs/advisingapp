<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementsRelationManager;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementResponsesRelationManager;

class ManageProspectEngagement extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

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
