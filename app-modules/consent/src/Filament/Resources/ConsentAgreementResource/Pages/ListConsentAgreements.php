<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Assist\Consent\Filament\Resources\ConsentAgreementResource;

class ListConsentAgreements extends ListRecords
{
    protected static string $resource = ConsentAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
