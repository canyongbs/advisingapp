<?php

namespace Assist\Engagement\Filament\Actions;

use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Assist\Engagement\Actions\CreateEngagementBatch;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\DataTransferObjects\EngagementBatchCreationData;

class BulkEngagementAction
{
    public static function make(string $context)
    {
        return BulkAction::make('engage')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Send Bulk Engagement')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} {$context} to engage.")
            ->steps([
                Step::make('Choose your delivery methods')
                    ->description('SelectFormFieldBlock email, sms, or both.')
                    ->schema([
                        Select::make('delivery_methods')
                            ->label('How would you like to send this engagement?')
                            ->translateLabel()
                            ->options(EngagementDeliveryMethod::class)
                            ->multiple()
                            ->minItems(1)
                            ->validationAttribute('Delivery Method')
                            ->required(),
                    ]),
                Step::make('Engagement Details')
                    ->description("Add the details that will be sent to the selected {$context}")
                    ->schema([
                        TextInput::make('subject')
                            ->autofocus()
                            ->translateLabel()
                            ->required()
                            ->placeholder(__('Subject'))
                            ->hidden(fn (callable $get) => collect($get('delivery_methods'))->doesntContain(EngagementDeliveryMethod::EMAIL->value))
                            ->helperText('The subject will only be used for the email delivery method.'),
                        // https://www.twilio.com/docs/glossary/what-sms-character-limit#:~:text=Twilio's%20platform%20supports%20long%20messages,best%20deliverability%20and%20user%20experience.
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
                        // TODO Potentially re-enable this later...
                        // Fieldset::make('Send your engagement')
                        //     ->schema([
                        //         Toggle::make('send_later')
                        //             ->reactive()
                        //             ->helperText('By default, this engagement will send as soon as it is created unless you schedule it to send later.'),
                        //         DateTimePicker::make('deliver_at')
                        //             ->required()
                        //             ->visible(fn (callable $get) => $get('send_later')),
                        //     ]),
                    ]),
            ])
            ->action(function (Collection $records, array $data) {
                CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
                    'user' => auth()->user(),
                    'records' => $records,
                    'subject' => $data['subject'],
                    'body' => $data['body'],
                    'deliveryMethods' => $data['delivery_methods'],
                ]));
            })
            ->modalSubmitActionLabel('Send')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            // FIXME This is currently not working exactly as expected. Dan is taking a look and will report back
            ->modalCancelAction(
                fn ($action) => Action::make('cancel')
                    ->requiresConfirmation()
                    ->modalDescription(fn () => 'The message has not been sent, are you sure you wish to return to the list view?')
                    ->cancelParentActions()
                    ->close()
                    ->color('gray'),
            )
            ->deselectRecordsAfterCompletion();
    }
}
