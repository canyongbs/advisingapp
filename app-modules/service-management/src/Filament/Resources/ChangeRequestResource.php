<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ServiceManagement;
use AdvisingApp\ServiceManagement\Models\ChangeRequest;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\EditChangeRequest;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\ListChangeRequests;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages\CreateChangeRequest;

class ChangeRequestResource extends Resource
{
    protected static ?string $model = ChangeRequest::class;

    protected static ?string $navigationLabel = 'Change Management';

    protected static ?string $navigationIcon = 'heroicon-m-arrow-path-rounded-square';

    protected static ?int $navigationSort = 30;

    protected static ?string $breadcrumb = 'Change Management';

    protected static ?string $cluster = ServiceManagement::class;

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequests::route('/'),
            'create' => CreateChangeRequest::route('/create'),
            'edit' => EditChangeRequest::route('/{record}/edit'),
        ];
    }
}
