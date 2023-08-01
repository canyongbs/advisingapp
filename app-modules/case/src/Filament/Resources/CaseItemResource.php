<?php

namespace Assist\Case\Filament\Resources;

use Assist\Case\Models\CaseItem;
use Filament\Resources\Resource;
use Filament\Resources\RelationManagers\RelationGroup;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\EditCaseItem;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\ViewCaseItem;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\ListCaseItems;
use Assist\Case\Filament\Resources\CaseItemResource\Pages\CreateCaseItem;
use Assist\Case\Filament\Resources\CaseItemResource\RelationManagers\CreatedByRelationManager;
use Assist\Case\Filament\Resources\CaseItemResource\RelationManagers\AssignedToRelationManager;
use Assist\Case\Filament\Resources\CaseItemResource\RelationManagers\RespondentRelationManager;
use Assist\Case\Filament\Resources\CaseItemResource\RelationManagers\CaseUpdatesRelationManager;

class CaseItemResource extends Resource
{
    protected static ?string $model = CaseItem::class;

    protected static ?string $navigationGroup = 'Cases';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $label = 'Case';

    public static function getRelations(): array
    {
        return [
            RespondentRelationManager::class,
            CaseUpdatesRelationManager::class,
            RelationGroup::make('Related Users', [
                AssignedToRelationManager::class,
                CreatedByRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseItems::route('/'),
            'create' => CreateCaseItem::route('/create'),
            'view' => ViewCaseItem::route('/{record}'),
            'edit' => EditCaseItem::route('/{record}/edit'),
        ];
    }
}
