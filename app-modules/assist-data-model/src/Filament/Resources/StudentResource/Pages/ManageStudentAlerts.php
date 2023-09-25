<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Alert\Filament\Resources\AlertResource\Pages\ManageAlerts;

class ManageStudentAlerts extends ManageAlerts
{
    protected static string $resource = StudentResource::class;
}
