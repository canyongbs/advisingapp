<?php

namespace Assist\Engagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\EditEngagement;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\ViewEngagement;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\ListEngagements;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\CreateEngagement;
use Assist\Engagement\Filament\Resources\EngagementResource\RelationManagers\EngagementDeliverablesRelationManager;

class EngagementResource extends Resource
{
    protected static ?string $model = Engagement::class;

    protected static ?string $navigationLabel = 'Engage';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static bool $shouldRegisterNavigation = false;

    public static function getRelations(): array
    {
        return [
            EngagementDeliverablesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEngagements::route('/'),
            'create' => CreateEngagement::route('/create'),
            'view' => ViewEngagement::route('/{record}'),
            'edit' => EditEngagement::route('/{record}/edit'),
        ];
    }
}
