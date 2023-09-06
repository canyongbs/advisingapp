<?php

namespace Assist\Audit\Filament\Resources;

use Assist\Audit\Models\Audit;
use Filament\Resources\Resource;
use Assist\Audit\Filament\Resources\AuditResource\Pages;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationLabel = 'Audit';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAudits::route('/'),
            'view' => Pages\ViewAudit::route('/{record}'),
        ];
    }
}
