<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ServiceManagementAdministration;
use AdvisingApp\ServiceManagement\Models\ChangeRequestStatus;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource\Pages\EditChangeRequestStatus;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource\Pages\CreateChangeRequestStatus;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource\Pages\ListChangeRequestStatuses;

class ChangeRequestStatusResource extends Resource
{
    protected static ?string $model = ChangeRequestStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 40;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequestStatuses::route('/'),
            'create' => CreateChangeRequestStatus::route('/create'),
            'edit' => EditChangeRequestStatus::route('/{record}/edit'),
        ];
    }
}
