<?php

namespace Assist\Engagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Filament\Resources\EngagementResponseResource\Pages\ViewEngagementResponse;
use Assist\Engagement\Filament\Resources\EngagementResponseResource\Pages\ListEngagementResponses;

class EngagementResponseResource extends Resource
{
    protected static ?string $model = EngagementResponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static bool $shouldRegisterNavigation = false;

    public static function getPages(): array
    {
        return [
            'index' => ListEngagementResponses::route('/'),
            'view' => ViewEngagementResponse::route('/{record}'),
        ];
    }
}
