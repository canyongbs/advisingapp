<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources;

use Laravel\Pennant\Feature;
use Filament\Resources\Resource;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\BasicNeeds\Models\BasicNeedsCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages\EditBasicNeedsCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages\ViewBasicNeedsCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages\CreateBasicNeedsCategory;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsCategoryResource\Pages\ListBasicNeedsCategories;

class BasicNeedsCategoryResource extends Resource
{
    protected static ?string $model = BasicNeedsCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Category';

    protected static ?string $modelLabel = 'Category';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Basic Needs';

    public static function canAccess(): bool
    {
        if (Feature::active('basic-needs')) {
            return parent::canAccess();
        }

        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBasicNeedsCategories::route('/'),
            'create' => CreateBasicNeedsCategory::route('/create'),
            'view' => ViewBasicNeedsCategory::route('/{record}'),
            'edit' => EditBasicNeedsCategory::route('/{record}/edit'),
        ];
    }
}
