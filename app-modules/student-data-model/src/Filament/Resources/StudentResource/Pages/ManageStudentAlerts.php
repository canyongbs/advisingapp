<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\HasStudentHeader;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns\CanManageEducatableAlerts;

class ManageStudentAlerts extends ManageRelatedRecords
{
    use CanManageEducatableAlerts;
    use HasStudentHeader;

    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'alerts';

    protected static ?string $title = 'Alerts';
}
