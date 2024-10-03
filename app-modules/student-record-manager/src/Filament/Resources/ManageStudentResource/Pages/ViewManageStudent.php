<?php

namespace AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource\Pages;

use AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewManageStudent extends ViewRecord
{
    protected static string $resource = ManageStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
