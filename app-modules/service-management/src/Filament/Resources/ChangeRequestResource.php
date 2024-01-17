<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\ServiceManagement\Models\ChangeRequest;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\EditChangeRequest;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\ListChangeRequests;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\CreateChangeRequest;

class ChangeRequestResource extends Resource
{
    protected static ?string $model = ChangeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequests::route('/'),
            'create' => CreateChangeRequest::route('/create'),
            'edit' => EditChangeRequest::route('/{record}/edit'),
        ];
    }
}
