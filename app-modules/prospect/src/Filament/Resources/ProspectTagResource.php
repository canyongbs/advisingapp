<?php

namespace AdvisingApp\Prospect\Filament\Resources;

use App\Models\Tag;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\Prospect\Filament\Resources\ProspectTagResource\Pages\EditProspectTag;
use AdvisingApp\Prospect\Filament\Resources\ProspectTagResource\Pages\ViewProspectTag;
use AdvisingApp\Prospect\Filament\Resources\ProspectTagResource\Pages\ListProspectTags;
use AdvisingApp\Prospect\Filament\Resources\ProspectTagResource\Pages\CreateProspectTag;

class ProspectTagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Tags';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Prospect Management';

    public static function getPages(): array
    {
        return [
            'index' => ListProspectTags::route('/'),
            'create' => CreateProspectTag::route('/create'),
            'view' => ViewProspectTag::route('/{record}'),
            'edit' => EditProspectTag::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'Prospect');
    }
}
