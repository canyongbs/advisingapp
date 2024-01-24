<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Clusters\ServiceManagementAdministration;
use AdvisingApp\ServiceManagement\Models\ServiceRequestForm;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages\EditServiceRequestForm;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages\ListServiceRequestForms;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages\CreateServiceRequestForm;

class ServiceRequestFormResource extends Resource
{
    protected static ?string $model = ServiceRequestForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    protected static ?int $navigationSort = 30;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequestForms::route('/'),
            'create' => CreateServiceRequestForm::route('/create'),
            'edit' => EditServiceRequestForm::route('/{record}/edit'),
        ];
    }
}
