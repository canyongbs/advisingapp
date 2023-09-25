<?php

namespace Assist\Consent\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

class ConsentAgreementResource extends Resource
{
    protected static ?string $model = ConsentAgreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsentAgreements::route('/'),
            // Creating consent agreements would currently require a code change
            // 'create' => Pages\CreateConsentAgreement::route('/create'),
            'edit' => Pages\EditConsentAgreement::route('/{record}/edit'),
        ];
    }
}
