<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources;

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\Pages;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource\RelationManagers;
use AdvisingApp\BasicNeeds\Models\BasicNeedCategory;
use App\Filament\Clusters\ConstituentManagement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BasicNeedCategoryResource extends Resource
{
    protected static ?string $model = BasicNeedCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Category';

    protected static ?string $modelLabel = 'Category';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Basic Needs';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBasicNeedCategories::route('/'),
            'create' => Pages\CreateBasicNeedCategory::route('/create'),
            'view' => Pages\ViewBasicNeedCategory::route('/{record}'),
            'edit' => Pages\EditBasicNeedCategory::route('/{record}/edit'),
        ];
    }
}
