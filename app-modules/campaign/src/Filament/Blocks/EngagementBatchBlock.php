<?php

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
