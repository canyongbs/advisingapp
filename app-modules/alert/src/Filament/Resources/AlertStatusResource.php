<?php

namespace AdvisingApp\Alert\Filament\Resources;

use AdvisingApp\Alert\Filament\Resources\AlertStatusResource\Pages\CreateAlertStatus;
use AdvisingApp\Alert\Filament\Resources\AlertStatusResource\Pages\EditAlertStatus;
use AdvisingApp\Alert\Filament\Resources\AlertStatusResource\Pages\ListAlertStatuses;
use AdvisingApp\Alert\Filament\Resources\AlertStatusResource\Pages\ViewAlertStatus;
use AdvisingApp\Alert\Models\AlertStatus;
use App\Filament\Clusters\ConstituentManagement;
use Filament\Resources\Resource;

class AlertStatusResource extends Resource
{
    protected static ?string $model = AlertStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Statuses';

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Alert';

    protected static ?int $navigationSort = 10;

    public static function getPages(): array
    {
        return [
            'index' => ListAlertStatuses::route('/'),
            'create' => CreateAlertStatus::route('/create'),
            'view' => ViewAlertStatus::route('/{record}'),
            'edit' => EditAlertStatus::route('/{record}/edit'),
        ];
    }
}
