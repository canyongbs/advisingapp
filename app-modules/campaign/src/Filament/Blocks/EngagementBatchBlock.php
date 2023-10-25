<?php

namespace Assist\Campaign\Filament\Blocks;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Assist\Engagement\Enums\EngagementDeliveryMethod;

class EngagementBatchBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Bulk Engagement');

        $this->schema($this->createFields());
    }

    public function createFields(): array
    {
        return [
            Select::make('delivery_methods')
                ->reactive()
                ->label('How would you like to send this engagement?')
                ->options(EngagementDeliveryMethod::class)
                ->multiple()
                ->minItems(1)
                ->validationAttribute('Delivery Method')
                ->required(),
            TextInput::make('subject')
                ->required()
                ->placeholder(__('Subject'))
                ->hidden(fn (callable $get) => collect($get('delivery_methods'))->doesntContain(EngagementDeliveryMethod::Email->value))
                ->helperText('The subject will only be used for the email delivery method.'),
            Textarea::make('body')
                ->placeholder(__('Body'))
                ->required()
                ->maxLength(function (callable $get) {
                    if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                        return 320;
                    }

                    return 65535;
                })
                ->helperText(function (callable $get) {
                    if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                        return 'The body of your message can be up to 320 characters long.';
                    }

                    return 'The body of your message can be up to 65,535 characters long.';
                }),
        ];
    }

    public function editFields(): array
    {
        return [
            Fieldset::make('Bulk Engagement Details')
                ->schema([
                    Select::make('data.delivery_methods')
                        ->label('How would you like to send this engagement?')
                        ->translateLabel()
                        ->options(EngagementDeliveryMethod::class)
                        ->multiple()
                        ->minItems(1)
                        ->validationAttribute('Delivery Methods')
                        ->helperText('You can select multiple delivery methods.')
                        ->reactive(),
                    TextInput::make('data.subject')
                        ->required(),
                    TextInput::make('data.body')
                        ->required()
                        ->maxLength(function (callable $get) {
                            if (collect($get('data.delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                                return 320;
                            }

                            return 65535;
                        }),
                ]),
        ];
    }

    public static function type(): string
    {
        return 'bulk_engagement';
    }
}
