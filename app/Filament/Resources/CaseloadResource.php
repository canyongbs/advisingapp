<?php

namespace App\Filament\Resources;

use App\Models\Caseload;
use Filament\Resources\Resource;
use App\Filament\Resources\CaseloadResource\Pages;

class CaseloadResource extends Resource
{
    protected static ?string $model = Caseload::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // public static function prospects

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseloads::route('/'),
            'create' => Pages\CreateCaseload::route('/create'),
            'edit' => Pages\EditCaseload::route('/{record}/edit'),
        ];
    }
}
