<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Campaign\Filament\Blocks;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Assist\Engagement\Enums\EngagementDeliveryMethod;

class EngagementBatchBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Email or Text');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Select::make($fieldPrefix . 'delivery_methods')
                ->columnSpanFull()
                ->reactive()
                ->label('How would you like to send this engagement?')
                ->options(EngagementDeliveryMethod::class)
                ->multiple()
                ->minItems(1)
                ->validationAttribute('Delivery Method')
                ->required(),
            TextInput::make($fieldPrefix . 'subject')
                ->columnSpanFull()
                ->placeholder(__('Subject'))
                ->required()
                ->hidden(fn (callable $get) => collect($get($fieldPrefix . 'delivery_methods'))->doesntContain(EngagementDeliveryMethod::Email->value))
                ->helperText('The subject will only be used for the email delivery method.'),
            Textarea::make($fieldPrefix . 'body')
                ->columnSpanFull()
                ->placeholder(__('Body'))
                ->required()
                ->maxLength(function (callable $get) use ($fieldPrefix) {
                    if (collect($get($fieldPrefix . 'delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                        return 320;
                    }

                    return 65535;
                })
                ->helperText(function (callable $get) use ($fieldPrefix) {
                    if (collect($get($fieldPrefix . 'delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                        return 'The body of your message can be up to 320 characters long.';
                    }

                    return 'The body of your message can be up to 65,535 characters long.';
                }),
            DateTimePicker::make('execute_at')
                ->label('When should the journey step be executed?')
                ->required()
                ->minDate(now(auth()->user()->timezone))
                ->closeOnDateSelection(),
        ];
    }

    public static function type(): string
    {
        return 'bulk_engagement';
    }
}
