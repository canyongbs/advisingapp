<?php

namespace Assist\Campaign\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Campaign\Models\Campaign;
use Assist\Campaign\Filament\Resources\CampaignResource\Pages\EditCampaign;
use Assist\Campaign\Filament\Resources\CampaignResource\Pages\ViewCampaign;
use Assist\Campaign\Filament\Resources\CampaignResource\Pages\ListCampaigns;
use Assist\Campaign\Filament\Resources\CampaignResource\Pages\CreateCampaign;
use Assist\Campaign\Filament\Resources\CampaignResource\RelationManagers\CampaignActionsRelationManager;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Mass Engagement';

    protected static ?int $navigationSort = 2;

    public static function getRelations(): array
    {
        return [
            CampaignActionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view' => ViewCampaign::route('/{record}'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }
}
