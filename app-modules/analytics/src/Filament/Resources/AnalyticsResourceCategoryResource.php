<?php

namespace AdvisingApp\Analytics\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages\EditAnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages\ViewAnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages\CreateAnalyticsResourceCategory;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceCategoryResource\Pages\ListAnalyticsResourceCategories;

class AnalyticsResourceCategoryResource extends Resource
{
    protected static ?string $model = AnalyticsResourceCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Settings';

    protected static ?int $navigationSort = 20;

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnalyticsResourceCategories::route('/'),
            'create' => CreateAnalyticsResourceCategory::route('/create'),
            'view' => ViewAnalyticsResourceCategory::route('/{record}'),
            'edit' => EditAnalyticsResourceCategory::route('/{record}/edit'),
        ];
    }
}
