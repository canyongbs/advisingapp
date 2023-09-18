<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Assist\Consent\Filament\Resources\ConsentAgreementResource;

class EditConsentAgreement extends EditRecord
{
    protected static string $resource = ConsentAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
