<?php

namespace AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource\Pages;

use AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManageProgram extends EditRecord
{
    protected static string $resource = ManageProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
