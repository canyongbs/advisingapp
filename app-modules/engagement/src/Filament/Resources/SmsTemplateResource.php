<?php

namespace Assist\Engagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Engagement\Models\SmsTemplate;
use Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages\EditSmsTemplate;
use Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages\ListSmsTemplates;
use Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages\CreateSmsTemplate;

class SmsTemplateResource extends Resource
{
    protected static ?string $model = SmsTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 12;

    public static function getPages(): array
    {
        return [
            'index' => ListSmsTemplates::route('/'),
            'create' => CreateSmsTemplate::route('/create'),
            'edit' => EditSmsTemplate::route('/{record}/edit'),
        ];
    }
}
