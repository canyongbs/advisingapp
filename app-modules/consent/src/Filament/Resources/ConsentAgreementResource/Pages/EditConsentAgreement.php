<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Assist\Consent\Filament\Resources\ConsentAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsentAgreement extends EditRecord
{
    protected static string $resource = ConsentAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
