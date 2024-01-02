<?php

namespace AdvisingApp\Analytics\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use AdvisingApp\Analytics\Models\AnalyticsResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages\EditAnalyticsResource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages\ListAnalyticsResources;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages\CreateAnalyticsResource;

class AnalyticsResourceResource extends Resource
{
    protected static ?string $model = AnalyticsResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'Data and Analytics';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Analytics Portal';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnalyticsResources::route('/'),
            'create' => CreateAnalyticsResource::route('/create'),
            'edit' => EditAnalyticsResource::route('/{record}/edit'),
        ];
    }
}
