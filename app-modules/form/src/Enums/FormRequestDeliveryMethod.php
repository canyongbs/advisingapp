<?php

namespace Assist\Form\Enums;

use Assist\Form\Models\FormRequest;
use Filament\Support\Contracts\HasLabel;
use Assist\Form\Actions\DeliverFormRequestBySms;
use Assist\Form\Actions\DeliverFormRequestByEmail;

enum FormRequestDeliveryMethod: string implements HasLabel
{
    case Email = 'email';
    case Sms = 'sms';

    public function getLabel(): ?string
    {
        return match ($this) {
            static::Email => 'Email',
            static::Sms => 'SMS',
        };
    }

    public function deliver(FormRequest $request): void
    {
        match ($this) {
            static::Email => DeliverFormRequestByEmail::dispatch($request),
            static::Sms => DeliverFormRequestBySms::dispatch($request),
        };
    }
}
