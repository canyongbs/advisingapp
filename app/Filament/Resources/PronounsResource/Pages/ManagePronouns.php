<?php

namespace App\Filament\Resources\PronounsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\PronounsResource;

class ManagePronouns extends ManageRecords
{
    protected static string $resource = PronounsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('md'),
        ];
    }
}
