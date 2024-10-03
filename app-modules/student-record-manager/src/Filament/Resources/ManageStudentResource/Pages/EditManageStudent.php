<?php

namespace AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource\Pages;

use AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManageStudent extends EditRecord
{
    protected static string $resource = ManageStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
