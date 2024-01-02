<?php

namespace AdvisingApp\Analytics\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\Analytics\Models\AnalyticsResourceSource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages\EditAnalyticsResourceSource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages\ViewAnalyticsResourceSource;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages\ListAnalyticsResourceSources;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceSourceResource\Pages\CreateAnalyticsResourceSource;

class AnalyticsResourceSourceResource extends Resource
{
    protected static ?string $model = AnalyticsResourceSource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Settings';

    protected static ?int $navigationSort = 19;

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnalyticsResourceSources::route('/'),
            'create' => CreateAnalyticsResourceSource::route('/create'),
            'view' => ViewAnalyticsResourceSource::route('/{record}'),
            'edit' => EditAnalyticsResourceSource::route('/{record}/edit'),
        ];
    }
}
