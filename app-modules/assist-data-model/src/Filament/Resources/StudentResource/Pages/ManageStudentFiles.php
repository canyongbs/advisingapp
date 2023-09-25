<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\ManageEngagementFiles;

class ManageStudentFiles extends ManageEngagementFiles
{
    protected static string $resource = StudentResource::class;
}
