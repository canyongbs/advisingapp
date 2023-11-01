<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Assist\Consent\Filament\Resources\ConsentAgreementResource;

class EditConsentAgreement extends EditRecord
{
    protected static string $resource = ConsentAgreementResource::class;

    public function getTitle(): string | Htmlable
    {
        return "Edit {$this->record->title}";
    }
}
