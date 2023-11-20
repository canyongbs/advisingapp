<?php

namespace App\Filament\Resources;

use App\Models\EmailTemplate;
use Filament\Resources\Resource;
use App\Filament\Resources\EmailTemplateResource\Pages\EditEmailTemplate;
use App\Filament\Resources\EmailTemplateResource\Pages\ListEmailTemplates;
use App\Filament\Resources\EmailTemplateResource\Pages\CreateEmailTemplate;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 10;

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailTemplates::route('/'),
            'create' => CreateEmailTemplate::route('/create'),
            'edit' => EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
