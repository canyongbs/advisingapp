<?php

namespace Assist\Consent\Enums;

enum ConsentAgreementType: string
{
    case AzureOpenAI = 'azure_open_ai';

    // We may end up moving this to the model itself, but for now it doesn't quite make sense to make this editable by an admin
    public function getModalDescription(): string
    {
        return match ($this) {
            self::AzureOpenAI => "Warning: Changing the AI Consent Configuration will reset everyone's consents, making them agree to your new terms all over again. There's no undoing this, so please make sure this is your intention.",
        };
    }
}
