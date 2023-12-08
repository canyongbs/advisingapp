<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Filament\Blocks;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;

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
            Select::make($fieldPrefix . 'delivery_method')
                ->columnSpanFull()
                ->reactive()
                ->label('How would you like to send this engagement?')
                ->options(EngagementDeliveryMethod::class)
                ->validationAttribute('Delivery Method')
                ->required(),
            TextInput::make($fieldPrefix . 'subject')
                ->columnSpanFull()
                ->placeholder(__('Subject'))
                ->required()
                ->hidden(fn (callable $get) => collect($get($fieldPrefix . 'delivery_method'))->doesntContain(EngagementDeliveryMethod::Email->value)),
            Textarea::make($fieldPrefix . 'body')
                ->columnSpanFull()
                ->placeholder(__('Body'))
                ->required()
                ->maxLength(function (callable $get) use ($fieldPrefix) {
                    if (collect($get($fieldPrefix . 'delivery_method'))->contains(EngagementDeliveryMethod::Sms->value)) {
                        return 320;
                    }

                    return 65535;
                })
                ->helperText(function (callable $get) use ($fieldPrefix) {
                    if (collect($get($fieldPrefix . 'delivery_method'))->contains(EngagementDeliveryMethod::Sms->value)) {
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
