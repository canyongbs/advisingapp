<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Forms\Form;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Filament\Resources\EngagementResource;
use Assist\Engagement\Actions\CreateDeliverablesForEngagement;

class CreateEngagement extends CreateRecord
{
    protected static string $resource = EngagementResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // TODO Better validation error messages here, "You must select at least 1 delivery method"
                Select::make('delivery_methods')
                    ->label('How would you like to send this engagement?')
                    ->translateLabel()
                    ->options(EngagementDeliveryMethod::class)
                    ->multiple()
                    ->minItems(1)
                    ->validationAttribute('Delivery Methods')
                    ->helperText('You can select multiple delivery methods.')
                    ->reactive(),
                Fieldset::make('Content')
                    ->schema([
                        TextInput::make('subject')
                            ->autofocus()
                            ->translateLabel()
                            ->required()
                            ->placeholder(__('Subject'))
                            ->hidden(fn (callable $get) => collect($get('delivery_methods'))->doesntContain(EngagementDeliveryMethod::EMAIL->value))
                            ->helperText('The subject will only be used for the email delivery method.'),
                        Textarea::make('body')
                            ->translateLabel()
                            ->placeholder(__('Body'))
                            ->required()
                            ->maxLength(function (callable $get) {
                                if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::SMS->value)) {
                                    return 320;
                                }

                                return 65535;
                            })
                            ->helperText(function (callable $get) {
                                if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::SMS->value)) {
                                    return 'The body of your message can be up to 320 characters long.';
                                }

                                return 'The body of your message can be up to 65,535 characters long.';
                            }),
                    ]),
                MorphToSelect::make('recipient')
                    ->label('Recipient')
                    ->translateLabel()
                    ->searchable()
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
                Fieldset::make('Send your engagement')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this engagement will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('deliver_at')
                            ->required()
                            ->visible(fn (callable $get) => $get('send_later')),
                    ]),
            ]);
    }

    public function afterCreate(): void
    {
        $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);

        $createDeliverablesForEngagement($this->record, $this->data['delivery_methods']);
    }
}
