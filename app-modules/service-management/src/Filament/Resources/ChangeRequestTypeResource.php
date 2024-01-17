<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ServiceManagementAdministration;
use AdvisingApp\ServiceManagement\Models\ChangeRequestType;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource\Pages\EditChangeRequestType;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource\Pages\ListChangeRequestTypes;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource\Pages\CreateChangeRequestType;

class ChangeRequestTypeResource extends Resource
{
    protected static ?string $model = ChangeRequestType::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    protected static ?string $navigationLabel = 'Change Request Types';

    protected static ?int $navigationSort = 30;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequestTypes::route('/'),
            'create' => CreateChangeRequestType::route('/create'),
            'edit' => EditChangeRequestType::route('/{record}/edit'),
        ];
    }
}
